<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $trick = new Trick;
        $trick->setName('Title 1');
        $trick->setDescription('Test');

        $em = $this->getDoctrine()->getManager();

        $em->persist($trick);
        $em->flush();

       // dump($trick);
        return $this->render('home/index.html.twig');
    }
}
