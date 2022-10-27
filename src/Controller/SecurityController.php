<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Uuid;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/app/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->redirectToRoute('app_login');
    }

    ### Change password ###
    #[Route(path: 'change-password/{token}', name: 'change-password')]
    public function editPassword(
      Request $request,
      UserPasswordHasherInterface $passwordEncoder,
      EntityManagerInterface $entityManager,
      UserRepository $userRepository,
      TokenGeneratorInterface $tokenGenerator,
      string $token
    ): Response
    {
      $user = $userRepository->findOneBy(['token' => $token]);

      if($user) {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
          try {
            if($this->changePasswordSecurity($form, $user)) {

              $passwordBeforeHash = $form->get('new_password')->getData();
              $hashedPassword = $passwordEncoder->hashPassword($user, $passwordBeforeHash);

              $token = $tokenGenerator->generateToken();
              $user->setToken($token);

              $user->setPassword($hashedPassword);

              $entityManager->persist($user);
              $entityManager->flush();

              $this->addFlash('success', 'Le mot de passe a été modifié avec succès !');

              $userRoles = $user->getRoles();
              switch ($userRoles[0]) {
                case 'ROLE_ADMIN' :
                  return $this->redirectToRoute('staff');
                case 'ROLE_FRANCHISE' :
                  return $this->redirectToRoute('franchise');
                case 'ROLE_STRUCTURE' :
                  return $this->redirectToRoute('structure');
                default :
                  return $this->redirectToRoute('app_login');
              }
            }
          } catch (ORMException $e) {
            throw new ORMException($e);
          }
        }

        return $this->render('security/changePassword.html.twig', [
          'form' => $form->createView(),
        ]);
      }
      return $this->redirectToRoute('app_login');
    }

    public function changePasswordSecurity($form, $user)
    {
      if($form->get('email')->getData() !== $user->getEmail()) {
        $form->get('email')->addError(new FormError('Une erreur est survenue'));
        return false;
      }

      if($form->get('new_password')->getData() !== $form->get('confirm_password')->getData()) {
        $form->get('confirm_password')->addError(new FormError('Les deux champs mot de passe ne correspondent pas'));
        return false;
      }
      return true;
    }
}
