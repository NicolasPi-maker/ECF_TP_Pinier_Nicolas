<?php

namespace App\Controller;

use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StructureDisplayerController extends AbstractController
{
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route(path: '/structure', name: 'structure')]
  public function index(StructureRepository $structureRepo)
  {
    $user = $this->getUser();
    $currentStructure = $structureRepo->findOneBy(['user_id' => $user]);

    $isFirstConnexion = $this->setLastConnexion();

    if($isFirstConnexion) {
      $this->addFlash('danger', 'Veuillez modifier votre mot de passe par défaut rapidement');
    }

    if($currentStructure->isIsActive()) {
      return $this->render('customer/structure.html.twig', [
        'isFirstConnexion' => $isFirstConnexion,
        'structure' => $currentStructure,
      ]);
    }

    return $this->render('miscellaneous/unactivated.html.twig', [
      'franchise' => false,
      'structure' => $currentStructure,
    ]);
  }

  public function setLastConnexion(): bool
  {
    $user = $this->getUser();

    if($user->getLastConnexion() !== null) {
      $user->setLastConnexion(new \DateTime());

      $this->em->persist($user);
      $this->em->flush($user);

      return false;
    }
    $user->setLastConnexion(new \DateTime());

    $this->em->persist($user);
    $this->em->flush($user);

    return true;
  }

}