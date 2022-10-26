<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\FranchiseType;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route(path: 'app/staff', name: 'staff')]
  public function index(FranchiseRepository $franchiseRepository, Request $request): Response
  {
    $isFirstConnexion = $this->setLastConnexion();

    $franchises = $franchiseRepository->getAll();

    if(isset($_POST['btn-switch-active'])) {
      $this->updateFranchiseActive();
      return $this->redirectToRoute('staff');
    }

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $franchises = $franchiseRepository->getAll();
      } else {
        $filter = $request->get('filter');
        $franchises = $franchiseRepository->franchiseFiltered($filter);
      }

      if($request->get('search_filter') !== null) {
        $searchFilter = $request->get('search_filter');
        $franchises = $franchiseRepository->franchiseFilteredBySearch($searchFilter);
      }
      return new JsonResponse([
        'content'=> $this->renderView('franchise/_card.html.twig', [
          'franchises' => $franchises,
        ])
      ]);
    }

    return $this->render('admin/admin.html.twig', [
      'franchises' => $franchises,
      'isFirstConnexion' => $isFirstConnexion,
    ]);

  }

  public function updateFranchiseActive()
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $structureRepo = $this->em->getRepository(Structure::class);

    if(isset($_POST['franchiseId'])) {
      $currentFranchise = $franchiseRepo->findOneBy(['id' => $_POST['franchiseId']]);
      $currentFranchise->setIsActive(!$currentFranchise->isIsActive());
      $linkedStructures = $structureRepo->findBy(['franchise_id' => $currentFranchise]);

      if(!$currentFranchise->isIsActive()) {
        foreach ($linkedStructures as $structure) {
          $structure->setIsActive(false);
          $this->em->persist($structure);
        }
      }

      $this->em->persist($currentFranchise);
      $this->em->flush();
    }
  }

  public function setLastConnexion()
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