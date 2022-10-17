<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExterneUrlRedirectController extends AbstractController
{
  #[Route(path: '/{url}', name: 'app_url_redirect')]
  public function index(string $url)
  {
    return $this->redirect('https://'.$url);
  }
}