<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Entity\User;
use App\Form\FranchiseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FranchiseController extends AbstractController
{
  public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
  {
    $this->em = $em;
    $this->slugger = $slugger;
  }

  ### Create Franchise ###
  #[Route(path: '/staff/create_franchise', name: 'create_franchise')]
  public function index(Request $request, MailerInterface $mailer)
  {
    $franchise = new Franchise();
    $form = $this->createForm(FranchiseType::class, $franchise);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $logoFile = $form->get('logo_url')->getData();
      $this->logoUrlConstructor($franchise, $logoFile);

      $this->em->persist($franchise);
      $this->em->flush();

      return $this->redirect($this->generateUrl('staff'));
    }

    return $this->render('forms/franchise_form.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  ### Edit franchise ###
  #[Route(path: '/staff/edit/franchise/{id}', name: 'edit_franchise')]
  public function editFranchise(Request $request, int $id): Response
  {
    $franchise = $this->getSelectedFranchise($id);

    if (!$franchise) {
      throw $this->createNotFoundException(
        'Aucune franchise n\'existe avec l\'identifiant suivant' . $id
      );
    }

    $form = $this->createForm(FranchiseType::class, $franchise);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {

      $logoFile = $form->get('logo_url')->getData();

      if($logoFile !== null) {
        $this->logoUrlConstructor($franchise, $logoFile);
      }

      $franchise->setLogoUrl($franchise->getLogoUrl());

      $this->em->persist($franchise);
      $this->structurePermsSynchronizer($id);

      $this->em->flush();

      return $this->redirect($this->generateUrl('staff'));
    }

    return $this->render('forms/edit_franchise_form.html.twig', [
      'form' => $form->createView(),
      'franchise' => $franchise,
    ]);
  }

  ### Remove franchise ###
  #[Route(path: '/staff/delete/franchise/{id}', name: 'remove_franchise')]
  public function removeFranchise($id)
  {
    $franchise = $this->getSelectedFranchise($id);

    if (!$franchise) {
      throw $this->createNotFoundException(
        'Aucune franchise n\'existe avec l\'identifiant suivant' . $id
      );
    }

    $this->em->remove($franchise);
    $this->em->flush();

    return $this->redirect($this->generateUrl('staff'));
  }

  #Récupère la franchise sur laquelle on souhaite modifier ses propriétées
  public function getSelectedFranchise($id)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    return $franchiseRepo->findOneBy(['id' => $id]);
  }

  #Format et envoie les fichiers image pour le logo au bon endroit
  public function logoUrlConstructor(Franchise $franchise, $logoFile)
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
        $franchise->setLogoUrl($newFileName);
      } catch (FileException $e) {
        echo $e;
      }
    } else {
      $this->addFlash('danger', 'Le logo a été rejeté ! Seulement les images au format .jpg et .png sont autorisées');
    }
  }

  public function structurePermsSynchronizer(int $id)
  {
    $franchiseRepo = $this->em->getRepository(Franchise::class);
    $currentFranchise = $franchiseRepo->findOneBy(['id' => $id]);

    $structureRepo = $this->em->getRepository(Structure::class);
    $structures = $structureRepo->getAllByCurrentFranchise($id);

    # get only updated fields #
    $uow = $this->em->getUnitOfWork();
    $uow->computeChangeSets();
    $changeSet = $uow->getEntityChangeSet($currentFranchise);

    foreach($structures as $structure) {
      $currentStructure = $structureRepo->findOneBy(['id'=> $structure['id']]);

      # loop on each changed perms label #
      foreach($changeSet as $key => $change) {

        # Generate dynamically setter depends on changed perms #
        $underscorePosition = strpos($key, '_');
        $searchedLetter = $key[$underscorePosition+1];

        $key = ucfirst($key);
        $key = str_replace($searchedLetter, strtoupper($searchedLetter), $key);
        $key = str_replace('_','',$key);
        $key = 'set'.$key;

        # $change is an array where index 0 = previous data and index 1 = new data #
        if($change[1] !== null) {
          $currentStructure->$key($change[1]);
        }
      }

      $this->em->persist($currentStructure);
    }
  }
}