<?php

namespace App\Controller;

use App\Entity\Franchise;
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

  #[Route(path: '/staff', name: 'staff')]
  public function index(FranchiseRepository $franchiseRepository, Request $request): Response
  {
    $franchises = $franchiseRepository->getAll();

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $franchises = $franchiseRepository->getAll();
      } else {
        $filter = $request->get('filter');
        $franchises = $franchiseRepository->franchiseFiltered($filter);
      }
      return new JsonResponse([
        'content'=> $this->renderView('franchise/_card.html.twig', [
          'franchises' => $franchises,
        ])
      ]);
    }

    return $this->render('admin/admin.html.twig', [
      'franchises' => $franchises,
    ]);
  }
}