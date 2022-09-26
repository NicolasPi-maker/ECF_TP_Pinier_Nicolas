<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
  #[Route(path: '/staff', name: 'staff')]
  public function index()
  {
    return $this->render('admin/admin.html.twig');
  }

}