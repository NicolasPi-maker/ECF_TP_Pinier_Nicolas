<?php

namespace App\EventListener;

use App\Entity\Franchise;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class FranchiseEventListener extends AbstractController
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

  public function postPersist(Franchise $franchise, LifecycleEventArgs $event):void
  {
    $user = $franchise->getUserId();

    $email = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($user->getEmail())
      ->subject('Vos identifants de franchise - JustSport')
      ->htmlTemplate('email/confirm_created_franchise.html.twig')
      ->context([
        'user' => $user
      ]);

    try {
      $this->mailer->send($email);
      $this->addFlash('success', 'La franchise '.$franchise->getClientName().' a bien été créée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }

  public function postRemove(Franchise $franchise, LifecycleEventArgs $event)
  {

    $email = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($franchise->getUserId()->getEmail())
      ->subject('Vos franchise a été supprimée - JustSport')
      ->htmlTemplate('email/remove_franchise.html.twig')
      ->context([
        'franchise' => $franchise
      ]);

    try {
      $this->mailer->send($email);
      $this->addFlash('danger', 'La franchise '.$franchise->getClientName().' a été supprimée');
    } catch (ExceptionInterface $e) {
      throw new Exception($e);
    }
  }

}