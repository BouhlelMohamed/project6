<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Token;
use App\Form\ForgetType;
use App\Form\SecurityType;
use App\Form\UpdatePasswordType;
use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    UserPasswordEncoderInterface $encoder,NotifierInterface $notifier)
    {

        $user = new User();

        $form = $this->createForm(SecurityType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //dd($form->getData());
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setActive(false);
            $user->setPicture('avatar-default.png');

            $em->persist($user);
            $em->flush();
            $cryptId = $this->getCrypteText($user->getId(),'id_validate_acoount');

            // Create a Notification that has to be sent
            // using the "email" channel
            $notification = (new Notification('Validation de votre compte', ['email']))
                ->content(
                'Bonjour, voici le lien pour valider votre compte. 
                http://p6.mohamed-bouhlel.com/validate/'.$cryptId);
            // The receiver of the Notification
            $recipient = new AdminRecipient(
                $user->getEmail(),
            );

            // Send the notification to the recipient
            $notifier->send($notification, $recipient);

            $this->addFlash('success', 'Vous êtes bien inscrit, 
            Un email de validation vient de vous être envoyé !');

            return $this->redirectToRoute('login');
        }
            return $this->render('security/register.html.twig', [
            'formRegistration' => $form->createView(),
        ]);
    }

    /**
     * @Route("/validate/{token}", name="validate")
     */
    public function validateAccount(Request $request,EntityManagerInterface $em,
    UserRepository $repo)
    {
        $routeParameters = $request->attributes->get('_route_params');
        $decrypteId = $this->getDecrypteText($routeParameters['token'],'id_validate_acoount');
        $user = $repo->findOneBy(array('id' => $decrypteId));
        $user->setActive(true);
        $em->persist($user);
        $em->flush();

        return $this->render('security/validate.html.twig');
    }


    /**
     * @Route("/forget-password", name="forgetPassword")
     */
    public function forgetPassword(Request $request,EntityManagerInterface $em,
    UserRepository $repo,NotifierInterface $notifier)
    {
        //$user = new User();
        $form = $this->createForm(ForgetType::class,/*$user*/);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //dd($form->getData());
            $formResult = $form->getData();
            $value = $formResult['email'];
            $userInfo = $repo->findOneBy(array('email' => $value));
            if($userInfo != null)
            {
                $token = new Token();
                $cryptId = $this->getCrypteText($userInfo->getId(),'id_reset_password');
                $token->setUser($userInfo);
                $token->setCreatedAt(new DateTime('NOW'));
                $token->setExpireAt(new DateTime('tomorrow'));
                $token->setToken($cryptId);
                $em->persist($token);
                $em->flush();

                // Create a Notification that has to be sent
                // using the "email" channel
                $notification = (new Notification('Nouveau mot de passe', ['email']))
                ->content(
                'Bonjour, voici le lien pour changer votre mot de passe.  
                http://p6.mohamed-bouhlel.com/reset-password/'.$cryptId . "
                Attention le lien expire dans 24H!!");

                // The receiver of the Notification
                $recipient = new AdminRecipient(
                    $userInfo->getEmail(),
                );

                // Send the notification to the recipient
                $notifier->send($notification, $recipient);

                $this->addFlash('success', 'Un email vient de vous être envoyé !');

                return $this->redirectToRoute('login');

            }else {
                echo "Votre email n'existe pas";
            }
        }
        return $this->render('security/forgetPassword.html.twig',[
        'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/reset-password/{token}", name="resetPassword")
     */
    public function resetPassword(Request $request,UserRepository $repo,
    EntityManagerInterface $em,UserPasswordEncoderInterface $encoder,TokenRepository $repoToken)
    {
        $token = $request->attributes->get('token');
        $decrypteId = $this->getDecrypteText($token,'id_reset_password');
        $user = $repo->findOneBy(array('id' => $decrypteId));
        $token = $repoToken->findOneBy(array('token' => $token));
        if($token->getExpireAt() > new DateTime("Now"))
        {
            $tokenOnOff = 0;
        }
        else {
            $tokenOnOff = 1;
        }
        $form = $this->createForm(UpdatePasswordType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $em->remove($token);
            $em->flush();
            
            $this->addFlash('success', 'Votre mot de passe a bien été modifié !');

            return $this->redirectToRoute('login');
        }
        return $this->render('security/resetPassword.html.twig',[
            'form' => $form->createView(),
            'tokenOnOff'=>  $tokenOnOff
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Erreur');
    }

    // crypter un lien 
    function getCrypteText($texte, $cle) 
    {
        srand((double)microtime()*1000000);
        $cledencryptage = md5(rand(0,32000));
        $compteur=0;
        $variabletemp = "";
        for($ctr = 0; $ctr < strlen($texte); $ctr++) {
            if( $compteur == strlen($cledencryptage) ) {
                $compteur=0;
            }
            $variabletemp .= substr($cledencryptage, $compteur, 1).(substr($texte, $ctr, 1) ^ substr($cledencryptage, $compteur, 1));
            $compteur++;
        }
        return base64_encode($this->getGenerationCle($variabletemp, $cle));
    }
  
    // decrypter un lien 
    function getDecrypteText($texte, $cle) 
    {
        $texte = $this->getGenerationCle(base64_decode($texte), $cle);
        $variabletemp = "";
        for($ctr = 0; $ctr < strlen($texte); $ctr++) {
            $md5 = substr($texte, $ctr, 1);
            $ctr++;
            $variabletemp .= (substr($texte, $ctr, 1) ^ $md5);
        }
        return $variabletemp;
    }
  
    public function getGenerationCle($texte, $cledencryptage) 
    {
        $cledencryptage = md5($cledencryptage);
        $compteur = 0;
        $variabletemp = "";
        for($ctr = 0; $ctr < strlen($texte); $ctr++) {
            if( $compteur == strlen($cledencryptage) ) {
                $compteur=0;
            }
            $variabletemp .= substr($texte, $ctr, 1) ^ substr($cledencryptage, $compteur, 1);
            $compteur++;
        }
        return $variabletemp;
    }

}
