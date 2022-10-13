<?php

namespace App\EventListener;

use App\Entity\Franchise;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class FranchiseUpdateEventListener extends AbstractController
{
  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }
  public function postUpdate(Franchise $franchise, LifecycleEventArgs $event): void
  {
    $email = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($franchise->getUserId()->getEmail())
      ->subject('Votre franchise '.$franchise->getClientName().' a été modifiée - JustSport')
      ->htmlTemplate('email/edit_franchise.html.twig')
      ->context([
        'franchise' => $franchise
      ]);
    try {
      $this->mailer->send($email);
      $this->addFlash('warning', 'La franchise '.$franchise->getClientName().' a bien été modifiée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }
}