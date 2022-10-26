<?php

namespace App\EventListener;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class StructureEventListener extends AbstractController
{
  public function __construct(FranchiseRepository $franchiseRepository, MailerInterface $mailer, EntityManagerInterface $em)
  {
    $this->mailer = $mailer;
    $this->franchiseRepository = $franchiseRepository;
    $this->em = $em;
  }

  public function postUpdate(Structure $structure, LifecycleEventArgs $event): void
  {
    $linkedFranchise = $this->franchiseRepository->findOneBy(['id' => $structure->getFranchiseId()]);

    $structureEmail = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($structure->getUserId()->getEmail())
      ->subject('Votre structure '.$structure->getStructureName().' a été modifiée - JustSport')
      ->htmlTemplate('email/edit_structure.html.twig')
      ->context([
        'structure' => $structure
      ]);

    $emailToFranchise = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($linkedFranchise->getUserId()->getEmail())
      ->subject('La structure '.$structure->getStructureName().' appartenant à votre franchise a été modifiée - JustSport')
      ->htmlTemplate('email/edit_structure.html.twig')
      ->context([
        'structure' => $structure
      ]);

    try {
      $this->mailer->send($structureEmail);
      $this->mailer->send($emailToFranchise);
      $this->addFlash('warning', 'La structure'.$structure->getStructureName().' a bien été modifiée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }

  public function postPersist(Structure $structure, LifecycleEventArgs $event)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $linkedFranchise = $franchiseRepo->findOneBy(['id' => $structure->getFranchiseId()]);

    $emailToStructure = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($structure->getUserId()->getEmail())
      ->subject('Vos identifants de structure - JustSport')
      ->htmlTemplate('email/confirm_created_franchise.html.twig')
      ->context([
        'user' => $structure->getUserId(),
      ]);

    $emailToFranchise = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($linkedFranchise->getUserId()->getEmail())
      ->subject('Une nouvelle structure a été créée pour votre franchise - JustSport')
      ->htmlTemplate('email/confirm_created_structure.html.twig')
      ->context([
        'structure' => $structure
      ]);

    try {
      $this->mailer->send($emailToStructure);
      $this->mailer->send($emailToFranchise);
      $this->addFlash('success', 'La structure'.$structure->getStructureName().' a bien été créée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }

  public function postRemove(Structure $structure, LifecycleEventArgs $event)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $linkedFranchise = $franchiseRepo->findOneBy(['id' => $structure->getFranchiseId()]);

    $emailToStructure = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($structure->getUserId()->getEmail())
      ->subject('Votre structure a été supprimée - JustSport')
      ->htmlTemplate('email/remove_structure.html.twig')
      ->context([
        'structure' => $structure
      ]);

    $emailToFranchise = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($linkedFranchise->getUserId()->getEmail())
      ->subject('Une de vos structures a été supprimée - JustSport')
      ->htmlTemplate('email/remove_structure.html.twig')
      ->context([
        'structure' => $structure
      ]);

    try {
      $this->mailer->send($emailToStructure);
      $this->mailer->send($emailToFranchise);
      $this->addFlash('danger', 'La structure '.$structure->getStructureName().' a été supprimée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }
}