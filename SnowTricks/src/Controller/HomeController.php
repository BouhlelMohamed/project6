<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(TrickRepository $repo)
    {
        if($this->getUser() != null)
        {
            $user = $this->getUser();
            if($user->getActive() == false || empty($user->getActive()) == true)
            {
                return $this->redirectToRoute('logout');
            }
        }
        
        $tricks = $repo->findAll();
        return $this->render('home/index.html.twig', array(
          'tricks'	=>	$tricks
        ));
    }
}
