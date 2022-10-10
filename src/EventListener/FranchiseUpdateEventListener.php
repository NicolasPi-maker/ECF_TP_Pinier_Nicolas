<?php

namespace App\EventListener;

use App\Entity\Franchise;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class FranchiseUpdateEventListener
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

    $this->mailer->send($email);
  }
}