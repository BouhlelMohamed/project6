<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
    * @Route("/profile/{id}", name="profile")
    */
    public function profile(User $user,Request $request,EntityManagerInterface $em,SluggerInterface $slugger,UserRepository $repo)
    {
        $userInfo = $repo->find($user);
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $nameFile = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($nameFile);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();
    
                // Move the file to the directory where brochures are stored
                try {
                    $picture->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    echo 'error';
                }
    
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setPicture($newFilename);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }
        return $this->render('user/profile.html.twig',[
            'formUser'  =>  $form->createView(),
            'user'      =>  $userInfo
        ]); 
    }
}
