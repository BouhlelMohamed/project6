<?php

namespace App\Controller;

use DateTime;
use App\Entity\Image;
use App\Entity\Trick;

use App\Entity\Video;
use App\Entity\Comment;
use App\Form\ImageType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Form\CommentType;
use App\Repository\ImageRepository;

use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    * @Route("/trick/edit/{id<[0-9]+>}", name="trick_edit")
    */
    public function formTrick(Trick $trick = null,Request $request,EntityManagerInterface $em,
    SluggerInterface $slugger)
    {
        
        if(!$trick)
        {
            $trick = new Trick();
        }
        $form = $this->createForm(TrickType::class,$trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $imageName = $form->get('bestImage')->getData();
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
                $trick->setBestImage($newFilename);
                //dump($image);
            }else{
                $trick->setBestImage('stale.jpg');
            }
            
            if(strpos($request->server->get('HTTP_REFERER'),'edit'))
            {
                $this->addFlash('update', 'Le trick a bien été modifié !');
            }else {
                $this->addFlash('success', 'Le trick a bien été créé !');
            }
            $em->persist($trick);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        return $this->render('trick/create.html.twig',[
            'formTrick'          =>  $form->createView(),
            'editMode'           =>  $trick->getId() !== null,
            'trickId'            =>  $trick->getId()
        ]); 
    }

    /**
    * @Route("/trick/{id}", name="trick_show")
    */
    public function show(Trick $trick,Request $request,EntityManagerInterface $em,
    PaginatorInterface $paginator,CommentRepository $repo,SluggerInterface $slugger)
    {
        $category = $trick->getCategory()->getName();
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
                //dump($image);
            }
            $this->addFlash('addImage', 'L\'image a bien été ajoutée !');
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class,$video);
        $formVideo->handleRequest($request);
        if($formVideo->isSubmitted() && $formVideo->isValid())
        {
            $this->addFlash('addVideo', 'La vidéo a bien été ajoutée!');
            $video->setTrick($trick);
            $em->persist($video);
            $em->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        $commentQuery = $repo->createQueryBuilder('c')->where("c.trick = ".$trick->getId())->orderBy('c.createdAt','DESC')->getQuery();
        $pagination = $paginator->paginate($commentQuery,$request->query->getInt('p', 1),10);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setTrick($trick);
            $comment->setCreatedAt(new DateTime('NOW'));
            $em->persist($comment);
            $em->flush();
            $this->addFlash('addComment', 'Votre commentaire a bien été ajouté !');
            return $this->redirect($request->getUri());
        }

        return $this->render('trick/show.html.twig', [
            'trick'        =>  $trick,
            'commentForm'  =>  $form->createView(),
            'pagination'   => $pagination,
            'formVideo'          =>  $formVideo->createView(),
            'formImage'          =>  $formImage->createView(),
            'category'           => $category
        ]); 
    }

    /**
    * @Route("/trick/delete/image/{id}", name="trick_delete_image")
    */
    public function deleteImage(Image $image,EntityManagerInterface $em)
    {
        $em->remove($image);
        $em->flush();
        $this->addFlash('danger', 'L\'image a bien été supprimée !');
        return $this->redirectToRoute('allTricks');
    }

    /**
    * @Route("/trick/delete/{id}", name="trick_delete")
    */
    public function delete(Trick $trick,EntityManagerInterface $em)
    {
        $em->remove($trick);
        $em->flush();
        $this->addFlash('danger', 'Le trick a bien été supprimé !');
        return $this->redirectToRoute('allTricks');
    }
}
