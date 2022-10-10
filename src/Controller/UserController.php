<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
    TokenGeneratorInterface $tokenGenerator,
  ): Response
  {
    $entity = new User();
    $form = $this->createForm(UserType::class, $entity);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $passwordBeforeHash = $form->getData()->getPassword();
      $hashedPassword = $passwordEncoder->hashPassword($entity, $passwordBeforeHash);

      try {
        $token = $tokenGenerator->generateToken();
        $entity->setToken($token);

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
      return $this->redirectToRoute('staff');
    }

    return $this->render('forms/user_form.html.twig', [
      'form' => $form->createView(),
    ]);
  }
}