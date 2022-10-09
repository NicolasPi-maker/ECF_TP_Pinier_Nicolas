<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  ### Create User ###
  #[Route(path: 'staff/create_user', name: 'create_user')]
  public function createUser(
    Request $request,
    UserPasswordHasherInterface $passwordEncoder,
    MailerInterface $mailer
  ): Response
  {
    $entity = new User();
    $form = $this->createForm(UserType::class, $entity);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $franchiseId = null;

      if($form->getData()->getFranchise()) {
        $franchiseId = $form->getData()->getFranchise()->getId();
      }

      $passwordBeforeHash = $form->getData()->getPassword();
      $hashedPassword = $passwordEncoder->hashPassword($entity, $passwordBeforeHash);

      try {
        $mailer->send($this->userCreatedMailer($entity));

        $entity->setPassword($hashedPassword);

        $this->em->persist($entity);
        $this->em->flush();

      } catch (ORMException|TransportExceptionInterface $e) {
        $this->addFlash('danger', $e);
      }

      if($entity->getRoles()[0] !== 'ROLE_STRUCTURE') {
        $this->addFlash('success', 'L\'utilisateur a bien été crée');
        return $this->redirectToRoute('create_franchise');
      }

      $this->addFlash('success', 'L\'utilisateur a bien été crée');
      return $this->redirectToRoute('create_structure', ['id' => $franchiseId]);
    }

    return $this->render('forms/user_form.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  public function userCreatedMailer($entity)
  {
    $subject = 'Vos identifants de franchise - JustSport';

    if($entity->getRoles() !== 'ROLE_FRANCHISE') {
      $subject = 'Vos identifiants de structure - JustSport';
    }

    $email = (new TemplatedEmail())
      ->from('justsport@gmail.com')
      ->to($entity->getEmail())
      ->to()
      ->subject($subject)
      ->htmlTemplate('email/confirm_created_account.html.twig')
      ->context([
        'user' => $entity
      ]);

    return $email;
  }
}