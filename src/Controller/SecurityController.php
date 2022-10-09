<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    ### Change password function ###
    #[Route(path: '/change-password', name: 'change-password')]
    public function editPassword(
      Request $request,
      UserPasswordHasherInterface $passwordEncoder,
      EntityManagerInterface $entityManager
    ): Response
    {
      $user = $this->getUser();

      $form = $this->createForm(ChangePasswordType::class);
      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
        try {
          if($this->changePasswordSecurity($form)) {

            $passwordBeforeHash = $form->get('new_password')->getData();
            $hashedPassword = $passwordEncoder->hashPassword($user, $passwordBeforeHash);

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

    public function changePasswordSecurity($form)
    {
      $user = $this->getUser();

      if(!password_verify($form->get('previous_password')->getData(), $user->getPassword() )) {
        $form->get('previous_password')->addError(new FormError('Une erreur est survenue'));
      }

      if($form->get('previous_password')->getData() === $form->get('new_password')->getData()) {
        $form->get('new_password')->addError(new FormError('Votre nouveau mot de passe ne peut pas être identique au précédent'));
      }

      if($form->get('new_password')->getData() !== $form->get('confirm_password')->getData()) {
        $form->get('confirm_password')->addError(new FormError('Les deux champs mot de passe ne correspondent pas'));
      }
      return true;
    }
}
