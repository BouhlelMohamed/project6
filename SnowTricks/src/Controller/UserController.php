<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
    * @Route("/profile/{id}", name="profile")
    */
    public function profile(User $user,Request $request,EntityManagerInterface $em)
    {

        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($user);
            $em->flush();
            dump($user);

            //return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }
        return $this->render('user/profile.html.twig',[
            'formUser'  =>  $form->createView()
        ]); 
    }
}
