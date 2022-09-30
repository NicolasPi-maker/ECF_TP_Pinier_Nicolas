<?php

namespace App\Controller;

use App\Entity\Franchise;
use App\Form\FranchiseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
  public function index(Request $request)
  {
    $franchise = new Franchise();
    $form = $this->createForm(FranchiseType::class, $franchise);

    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid()) {

      $this->logoUrlConstructor($form, $franchise);

      $this->em->persist($franchise);
      $this->em->flush();
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

    if($form->isSubmitted() && $form->isValid() ) {

      $this->logoUrlConstructor($form, $franchise);

      $this->em->persist($franchise);
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
  public function logoUrlConstructor(FormInterface $form, Franchise $franchise)
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
        $franchise->setLogoUrl($newFileName);
      } catch (FileException $e) {
        echo $e;
      }
    }
  }
}