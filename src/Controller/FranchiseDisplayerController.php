<?php

namespace App\Controller;

use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FranchiseDisplayerController extends AbstractController
{
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route(path: '/franchise', name: 'franchise')]
  public function index(StructureRepository $structureRepo, Request $request)
  {
    $user = $this->getUser();
    $currentFranchise = $user->getFranchise();

    $structures = $structureRepo->getAllByCurrentFranchise($currentFranchise);

    $isFirstConnexion = $this->setLastConnexion();

    if($isFirstConnexion) {
      $this->addFlash('danger', 'Veuillez modifier votre mot de passe par défaut rapidement');
    }

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $structures = $structureRepo->getAllByCurrentFranchise($currentFranchise);
      } else {
        $filter = $request->get('filter');
        $structures = $structureRepo->franchiseStructureFiltered($currentFranchise, $filter);
      }
      return new JsonResponse([
        'content'=> $this->renderView('customer/_franchise_structure_card.html.twig', [
          'structures' => $structures,
        ])
      ]);
    }

    if($currentFranchise->isIsActive()) {
      return $this->render('customer/franchise.html.twig', [
        'isFirstConnexion' => $isFirstConnexion,
        'franchise' => $currentFranchise,
        'structures' => $structures,
      ]);
    }

    return $this->render('miscellaneous/unactivated.html.twig', [
      'franchise' => $currentFranchise,
      'structure' => false,
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