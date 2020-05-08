<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TricksType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{

    /**
    * @Route("/tricks", name="allTricks")
    */
   public function findAllTricks(TrickRepository $repo)
   {
        $tricks = $repo->findAll();
        return $this->render('trick/index.html.twig', [
           'tricks'     =>  $tricks
       ]);
   }

    /**
    * Routes multiples 
    * @Route("/trick/create",name="trick_create")
    * @Route("/trick/edit/{id}", name="trick_edit")
    */
    public function formTrick(Trick $trick = null,Request $request,EntityManagerInterface $em)
    {
        if(!$trick)
        {
            $trick = new Trick();
        }
        // $form = $this->createFormBuilder($trick)
        //             ->add('name', TextType::class, [
        //                 'attr'  => [
        //                     'rel'   => 1 ,
        //                     'placeholder'   => 'le nom'
        //                 ]
        //             ])
        //             ->add('description', TextareaType::class)
        //             ->add('video_link', TextType::class)
        //             ->getForm();
        $form = $this->createForm(TricksType::class,$trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($trick);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        return $this->render('trick/create.html.twig',[
            'formTrick'  =>  $form->createView(),
            'editMode'  =>  $trick->getId() !== null
        ]); 
    }

    /**
    * @Route("/trick/{id}", name="trick_show",methods={"GET"})
    */
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick'     =>  $trick
        ]); 
    }

}
