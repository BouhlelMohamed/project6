<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;

use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

        $trick = new Trick();

        $video = new Video();
        $video->setLink('');
        $trick->getVideos()->add($video);
        $video2 = new Video();
        $video2->setLink('');
        $trick->getVideos()->add($video2);
        $form = $this->createForm(TrickType::class,$trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $video->setTrick($trick);
            $em->persist($trick);
            //dd($trick);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        return $this->render('trick/create.html.twig',[
            'formTrick'  =>  $form->createView(),
            'editMode'  =>  $trick->getId() !== null
        ]); 
    }

    /**
    * @Route("/trick/{id}", name="trick_show")
    */
    public function show(Trick $trick,Request $request,EntityManagerInterface $em)
    {
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setTrick($trick);
            $em->persist($comment);
            $em->flush();
        }
        return $this->render('trick/show.html.twig', [
            'trick'     =>  $trick,
            'commentForm'   =>  $form->createView()
        ]); 
    }

    /**
    * @Route("/trick/delete/{id}", name="trick_delete")
    */
    public function delete(Trick $trick,EntityManagerInterface $em)
    {
        $em->remove($trick);
        $em->flush();
        return $this->redirectToRoute('allTricks');
    }

}
