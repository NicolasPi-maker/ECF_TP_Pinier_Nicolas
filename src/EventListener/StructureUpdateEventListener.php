<?php

namespace App\EventListener;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class StructureUpdateEventListener extends AbstractController
{
  public function __construct(FranchiseRepository $franchiseRepository, MailerInterface $mailer)
  {
    $this->mailer = $mailer;
    $this->franchiseRepository = $franchiseRepository;
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
}