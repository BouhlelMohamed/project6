<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthentificationController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('authentification/login.html.twig', [
            'controller_name' => 'AuthentificationController',
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register()
    {
        return $this->render('authentification/register.html.twig', [
            'controller_name' => 'AuthentificationController',
        ]);
    }
}
