<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\FranchiseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FranchiseStructureInfoController extends AbstractController
{
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route(path: "/staff/franchise/{id}", name: 'franchise_structure_info')]
  public function index($id, Request $request)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $currentFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structureRepo = $this->em->getRepository(Structure::class);
    $structures = $structureRepo->findBy(['franchise_id' => $currentFranchise->getId()]);

    $franchiseForm = $this->createForm(FranchiseType::class, $currentFranchise);
    $franchiseForm->handleRequest($request);

    if($franchiseForm->isSubmitted() && $franchiseForm->isValid()) {
      $this->em->persist($currentFranchise);
      $this->em->flush();
    }

    return $this->render('franchise/franchise_structure_info.html.twig', [
      'franchise' => $currentFranchise,
      'structures' => $structures,
      'form' => $franchiseForm->createView(),
    ]);
  }
}