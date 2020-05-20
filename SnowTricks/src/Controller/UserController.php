<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
    * @Route("/profile/{id}", name="profile")
    */
    public function profile(User $user,Request $request,EntityManagerInterface $em,
    SluggerInterface $slugger,UserRepository $repo,UserPasswordEncoderInterface $encoder)
    {
        $userInfo = $repo->find($user);
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if(strlen($user->getPassword()) != 60 || strlen($user->getPassword()) ==0)
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
        }
        if($form->isSubmitted() && $form->isValid())
        {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $nameFile = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($nameFile);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();
    
                try {
                    $picture->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo 'error';
                }
    
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
