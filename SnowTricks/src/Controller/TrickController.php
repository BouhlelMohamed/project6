<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;

use App\Entity\Comment;
use App\Form\ImageType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Form\CommentType;

use App\Repository\TrickRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function formTrick(Trick $trick = null,Request $request,EntityManagerInterface $em,SluggerInterface $slugger)
    {
        if(!$trick)
        {
            $trick = new Trick();
        }

        $image = new Image();
        $formImage = $this->createForm(ImageType::class,$image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid())
        {
            $imageName = $formImage->get('name')->getData();
            if ($imageName) {
                $nameFile = pathinfo($imageName->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($nameFile);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageName->guessExtension();

                try {
                    $imageName->move(
                        $this->getParameter('directory_images_tricks'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo 'error';
                }
                $image->setName($newFilename);
                $image->setTrick($trick);
                dump($image);
            }
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class,$video);
        $formVideo->handleRequest($request);
        if($formVideo->isSubmitted() && $formVideo->isValid())
        {
            $video->setTrick($trick);
            $em->persist($video);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        $form = $this->createForm(TrickType::class,$trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($trick);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        return $this->render('trick/create.html.twig',[
            'formTrick'          =>  $form->createView(),
            'editMode'           =>  $trick->getId() !== null,
            'formVideo'          =>  $formVideo->createView(),
            'formImage'          =>  $formImage->createView()
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
