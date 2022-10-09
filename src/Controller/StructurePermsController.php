<?php

namespace App\Controller;

use App\Entity\Structure;
use App\Form\StructurePermissionsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class StructurePermsController extends AbstractController
{
  public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
  {
    $this->em = $em;
    $this->request = $requestStack->getMainRequest();

  }
  public function createDynamicPermsForm(int $id = null)
  {
    $structureRepo = $this->em->getRepository(Structure::class);
    $structure = $structureRepo->findOneBy(['id'=> $id]);

    $form = $this->createForm(StructurePermissionsType::class, $structure);
    $form->handleRequest($this->request);

    if($form->isSubmitted() && $form->isValid()) {
      $currentStructure = $structureRepo->findOneBy(['franchise_id' => $this->request->get('id')]);

      $this->em->persist($currentStructure);
      $this->em->flush();
    }

    return $this->render('forms/structure_permissions.html.twig', [
      'structureForm' => $form->createView(),
      'structure' => $structure,
    ]);
  }

}