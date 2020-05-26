<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Token;
use App\Form\ForgetType;
use App\Form\SecurityType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Notifier\Notification\Notification;
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
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setActive(false);

            $em->persist($user);
            $em->flush();
            $cryptId = $this->getCrypteText($user->getId(),'id_validate_acoount');

            // Create a Notification that has to be sent
            // using the "email" channel
            $notification = (new Notification('Validation de votre compte', ['email']))
                ->content('Bonjour, voici le lien pour valider votre compte. <br>  
                http://localhost:8000/validate/'.$cryptId);

            // The receiver of the Notification
            $recipient = new AdminRecipient(
                $user->getEmail(),
            );

            // Send the notification to the recipient
            $notifier->send($notification, $recipient);

            return $this->redirectToRoute('login');

        }

            return $this->render('security/register.html.twig', [
            'formRegistration' => $form->createView(),
        ]);
    }

    /**
     * @Route("/validate/{id}", name="validate")
     */
    public function validateAccount(Request $request,EntityManagerInterface $em,
    UserRepository $repo)
    {
        $routeParameters = $request->attributes->get('_route_params');
        $decrypteId = $this->getDecrypteText($routeParameters['id'],'id_validate_acoount');
        $user = $repo->findOneBy(array('id' => $decrypteId));
        $user->setActive(true);
        $em->persist($user);
        $em->flush();

        return $this->render('security/validate.html.twig');
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

    /**
     * @Route("/forget-password", name="forgetPassword")
     */
    public function forgetPassword(Request $request,EntityManagerInterface $em,UserRepository $repo)
    {
        $form = $this->createForm(ForgetType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            foreach($form->getData() as $value)
            {
                $email = $repo->findOneBy(array('email' => $value));
                if($email != null)
                {
                    $user = new User();
                    $token = new Token();
                    $token->setUser($user);
                    $em->persist($token);
                    $em->flush();
                }else {
                    echo "notification pas trouvé email";
                }
            }


        }

        return $this->render('security/forgetPassword.html.twig',[
        'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/reset-password", name="resetPassword")
     */
    public function resetPassword()
    {
        
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
