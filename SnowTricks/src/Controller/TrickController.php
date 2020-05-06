<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/{id}", methods={"GET"})
     */
    public function findOne(TrickRepository $repo,int $id)
    {
        $trick = $repo->find($id);
        return $this->render('trick/index.html.twig', [
            'trick'     =>  $trick
        ]);
    }

   
            /*         $trick = new Trick;
        $trick->setName('Title 1');
        $trick->setDescription('Test');

        $em = $this->getDoctrine()->getManager();

        $em->persist($trick);
        $em->flush(); */
}
