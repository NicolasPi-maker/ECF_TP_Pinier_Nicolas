<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\StructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class StructureController extends AbstractController
{
  public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
  {
    $this->em = $em;
    $this->slugger = $slugger;
  }

  ### Read Structure ###
  #[Route(path: '/staff/structures', name: 'staff_structure')]
  public function index(Request $request)
  {
    $structureRepo = $this->em->getRepository(Structure::class);

    $structures = $structureRepo->getAll();

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $structures = $structureRepo->getAll();
      } else {
        $filter = $request->get('filter');
        $structures = $structureRepo->structureFiltered($filter);
      }
      return new JsonResponse([
        'content'=> $this->renderView('structure/_structure_card.html.twig', [
          'structures' => $structures,
        ])
      ]);
    }

    return $this->render('structure/structures.html.twig', [
      'structures' => $structures,
    ]);
  }

  ### Create Structure ###
  #[Route(path: '/staff/franchise:{id}/create_structure', name: 'create_structure')]
  public function createStructure(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, int $id)
  {
    $franchiseRepo = $doctrine->getRepository(Franchise::class);
    $linkedFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structure = new Structure();
    $form = $this->createForm(StructureType::class, $structure);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $this->logoUrlConstructor($form, $structure);

      $entityManager = $doctrine->getManager();

      $structure->setFranchiseId($linkedFranchise);
      $structure->setSellFood($linkedFranchise->isSellFood());
      $structure->setSellDrink($linkedFranchise->isSellDrink());
      $structure->setManageSchedule($linkedFranchise->isManageSchedule());
      $structure->setCreateNewsletter($linkedFranchise->isCreateNewsletter());
      $structure->setCreateEvent($linkedFranchise->isCreateEvent());
      $structure->setAddPromotion($linkedFranchise->isAddPromotion());

      $entityManager->persist($structure);
      $entityManager->flush();

      return $this->redirect($this->generateUrl('staff'));
    }

    return $this->render('forms/structure_form.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  ### Edit structure ###
  #[Route(path: '/staff/edit_structure/{id}', name: 'edit_structure')]
  public function editFranchise(Request $request, int $id): Response
  {
    $structure= $this->getSelectedStructure($id);

    if (!$structure) {
      throw $this->createNotFoundException(
        'Aucune structure n\'existe avec l\'identifiant suivant' . $id
      );
    }

    $form = $this->createForm(StructureType::class, $structure);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid() ) {

      $this->logoUrlConstructor($form, $structure);

      $this->em->persist($structure);
      $this->em->flush();

      return $this->redirect($this->generateUrl('staff'));
    }

    return $this->render('forms/edit_structure_form.html.twig', [
      'form' => $form->createView(),
      'structure' => $structure,
    ]);
  }

  ### Remove structure ###
  #[Route(path: '/delete/structure/{id}', name: 'remove_structure')]
  public function removeFranchise($id)
  {
    $franchise = $this->getSelectedStructure($id);

    if (!$franchise) {
      throw $this->createNotFoundException(
        'Aucune franchise n\'existe avec l\'identifiant suivant' . $id
      );
    }

    $this->em->remove($franchise);
    $this->em->flush();

    return $this->redirect($this->generateUrl('staff'));
  }

  #Récupère la structure sur laquelle on souhaite modifier ses propriétées
  public function getSelectedStructure($id)
  {
    $structureRepo = $this->em->getRepository(Structure::class);
    return $structureRepo->findOneBy(['id' => $id]);
  }

  #Format et envoie les fichiers image pour le logo au bon endroit
  public function logoUrlConstructor(FormInterface $form, Structure $structure)
  {
    $logoFile = $form->get('logo_url')->getData();

    if($logoFile) {
      $originalFileName = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);

      $safeFileName = $this->slugger->slug($originalFileName);
      $newFileName = $safeFileName.'-'.uniqid().'.'.$logoFile->guessExtension();

      try {
        $logoFile->move(
          $this->getParameter('logo_files'),
          $newFileName
        );
        $structure->setLogoUrl($newFileName);
      } catch (FileException $e) {
        echo $e;
      }
    }
  }
}