<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\StructurePermissionsType;
use App\Form\StructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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

    if(isset($_POST['btn-switch-active'])) {
      $this->updateActive();
      return $this->redirectToRoute('staff_structure');
    }

    $structureForm = $this->createForm(StructurePermissionsType::class);
    $structureForm->handleRequest($request);

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

        return $this->redirectToRoute('staff_structure');
      }
    }

    if($request->get('ajax')) {
      if($request->get('filter') === 'all') {
        $structures = $structureRepo->getAll();
      } else {
        $filter = $request->get('filter');
        $structures = $structureRepo->structureFiltered($filter);
      }

      if($request->get('search_filter') !== null) {
        $searchFilter = $request->get('search_filter');
        $structures = $structureRepo->structureFilteredBySearch($searchFilter);
      }

      return new JsonResponse([
        'content'=> $this->renderView('structure/_structure_card.html.twig', [
          'structures' => $structures,
          'structureForm' => $structureForm->createView(),
        ])
      ]);
    }

    return $this->render('structure/structures.html.twig', [
      'structures' => $structures,
      'structureForm' => $structureForm->createView(),
    ]);
  }

  ### Create Structure ###
  #[Route(path: '/staff/franchise:{id}/create_structure', name: 'create_structure')]
  public function createStructure(
    Request $request,
    ManagerRegistry $doctrine,
    int $id,
    MailerInterface $mailer,
  )
  {
    $franchiseRepo = $doctrine->getRepository(Franchise::class);
    $linkedFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structure = new Structure();
    $form = $this->createForm(StructureType::class, $structure);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $logoFile = $form->get('logo_url')->getData();
      $this->logoUrlConstructor($structure, $logoFile);

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
      'franchise' => $linkedFranchise,
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

      $logoFile = $form->get('logo_url')->getData();

      if($logoFile !== null) {
        $this->logoUrlConstructor($structure, $logoFile);
      }

      $structure->setLogoUrl($structure->getLogoUrl());

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
    $structure = $this->getSelectedStructure($id);

    if (!$structure) {
      throw $this->createNotFoundException(
        'Aucune franchise n\'existe avec l\'identifiant suivant' . $id
      );
    }

    $this->em->remove($structure);
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
  public function logoUrlConstructor(Structure $structure, $logoFile)
  {


    if($logoFile && in_array($logoFile->guessExtension(), ['jpg','png'])) {
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
    } else {
      $this->addFlash('danger', 'Le logo a été rejeté ! Seulement les images au format .jpg et .png sont autorisées');
    }
  }

  public function updateActive()
  {
    $structureRepo = $this->em->getRepository(Structure::class);

    if(isset($_POST['structureId'])) {
      $currentStructure = $structureRepo->findOneBy(['id' => $_POST['structureId']]);
      $currentStructure->setIsActive(!$currentStructure->isIsActive());

      $this->em->persist($currentStructure);
      $this->em->flush();
    }
  }
}