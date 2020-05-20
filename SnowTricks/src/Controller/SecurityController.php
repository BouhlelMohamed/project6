<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SecurityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
    * @Route("/login", name="login")
    */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function registration(Request $request, EntityManagerInterface $em,
    UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(SecurityType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setActive(0);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }
            return $this->render('security/register.html.twig', [
            'formRegistration' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
