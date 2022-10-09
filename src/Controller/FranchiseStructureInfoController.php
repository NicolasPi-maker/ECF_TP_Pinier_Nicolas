<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\FranchisePermissionsType;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FranchiseStructureInfoController extends AbstractController
{
  public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
  {
    $this->em = $em;
    $this->slugger = $slugger;
  }

  #[Route(path: "/staff/franchise/{id}", name: 'franchise_structure_info')]
  public function index($id, Request $request)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $currentFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structureRepo = $this->em->getRepository(Structure::class);
    $structures = $structureRepo->getAllByCurrentFranchise($id);

    $franchiseForm = $this->createForm(FranchisePermissionsType::class, $currentFranchise);
    $franchiseForm->handleRequest($request);

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $structures = $structureRepo->getAllByCurrentFranchise($id);
      } else {
        $filter = $request->get('filter');
        $structures = $structureRepo->structureFilteredByFranchise($currentFranchise, $filter);
      }
      return new JsonResponse([
        'content'=> $this->renderView('structure/_structure_card.html.twig', [
          'structures' => $structures,
          'franchise' => $currentFranchise,
        ])
      ]);
    }

    if($franchiseForm->isSubmitted() && $franchiseForm->isValid()) {

      $this->structurePermsSynchronizer($id);

      $this->em->persist($currentFranchise);
      $this->em->flush();
    }

    return $this->render('franchise/franchise_structure_info.html.twig', [
      'franchise' => $currentFranchise,
      'structures' => $structures,
      'form' => $franchiseForm->createView(),
    ]);
  }

  public function structurePermsSynchronizer(int $id)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $currentFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structureRepo = $this->em->getRepository(Structure::class);
    $structures = $structureRepo->getAllByCurrentFranchise($id);

    $uow = $this->em->getUnitOfWork();
    $uow->computeChangeSets();
    $changeSet = $uow->getEntityChangeSet($currentFranchise);

    foreach($structures as $structure) {
      $currentStructure = $structureRepo->findOneBy(['id'=> $structure['id']]);

      # get only global changed perms #
      foreach($changeSet as $key => $change) {

        # Generate dynamically setter depends on changed perms #
        $underscorePosition = strpos($key, '_');
        $searchedLetter = $key[$underscorePosition+1];

        $key = ucfirst($key);
        $key = str_replace($searchedLetter, strtoupper($searchedLetter), $key);
        $key = str_replace('_','',$key);
        $key = 'set'.$key;

        $currentStructure->$key($change[1]);
      }
      $this->em->persist($currentStructure);
    }
  }
}