<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\FranchisePermissionsType;
use App\Form\StructurePermissionsType;
use App\Form\StructureType;
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

    if(isset($_POST['btn-switch-active'])) {
      $this->updateFranchiseActive();
      $this->updateStructureActive();
      return $this->redirectToRoute('franchise_structure_info', [
        'id' => $id,
      ]);
    }

    $structureRepo = $this->em->getRepository(Structure::class);
    $structures = $structureRepo->getAllByCurrentFranchise($id);

    $structureForm = $this->createForm(StructurePermissionsType::class);
    $structureForm->handleRequest($request);

    $franchiseForm = $this->createForm(FranchisePermissionsType::class, $currentFranchise);
    $franchiseForm->handleRequest($request);

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $structures = $structureRepo->getAllByCurrentFranchise($id);
      } else {
        $filter = $request->get('filter');
        $structures = $structureRepo->structureFilteredByFranchise($currentFranchise, $filter);
      }

      if($request->get('search_filter') !== null) {
        $searchFilter = $request->get('search_filter');
        $structures = $structureRepo->structureFilteredBySearchAndByFranchise($searchFilter, $currentFranchise);
      }

      return new JsonResponse([
        'content'=> $this->renderView('structure/_structure_card.html.twig', [
          'structures' => $structures,
          'franchise' => $currentFranchise,
          'structureForm' => $structureForm->createView(),
        ])
      ]);
    }

    if($structureForm->isSubmitted() && $structureForm) {
      if(isset($_POST['structureId'])) {
        $currentStructure = $structureRepo->findOneBy(['id'=> $_POST['structureId']]);

        $currentStructure->setSellDrink($structureForm->get('sell_drink')->getData());
        $currentStructure->setManageSchedule($structureForm->get('manage_schedule')->getData());
        $currentStructure->setCreateNewsletter($structureForm->get('create_newsletter')->getData());
        $currentStructure->setCreateEvent($structureForm->get('create_event')->getData());
        $currentStructure->setAddPromotion($structureForm->get('add_promotion')->getData());
        $currentStructure->setSellFood($structureForm->get('sell_food')->getData());

        $this->em->persist($currentStructure);
        $this->em->flush();
      }
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
      'structureForm' => $structureForm->createView(),
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

  public function updateStructureActive()
  {
    $structureRepo = $this->em->getRepository(Structure::class);

    if(isset($_POST['structureId'])) {
      $currentStructure = $structureRepo->findOneBy(['id' => $_POST['structureId']]);
      $currentStructure->setIsActive(!$currentStructure->isIsActive());

      $this->em->persist($currentStructure);
      $this->em->flush();
    }
  }

  public function updateFranchiseActive()
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);

    if(isset($_POST['franchiseId'])) {
      $currentFranchise = $franchiseRepo->findOneBy(['id' => $_POST['franchiseId']]);
      $currentFranchise->setIsActive(!$currentFranchise->isIsActive());

      $this->em->persist($currentFranchise);
      $this->em->flush();
    }
  }
}