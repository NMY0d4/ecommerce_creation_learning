<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Classe\RetServ;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{    

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, RetServ $retServ): Response
    {
        $notification = null;

        $user = new User;
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $search_email = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email){
                $password = $hasher->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                $this->em->persist($user);
                $this->em->flush();

                $apikey =  $retServ->getApiSecretKey(); 

                $mail = new Mail();
                $content = "Bonjour ".$user->getFullName()."<br>Bienvenue sur notre site pour servir nos clients de la meilleure façon.";
                $mail->send($user->getEmail(), $user->getFullName(), 'Bienvenue sur My ecommerce', $content, $apikey );

                $notification = 'Votre inscription c\'est correctement déroulée. Vous pouvez vous connectez à votre compte.';            
                
            } else {                
                $notification = 'L\'email que vous avez entré existe déjà.';   
            }
        }        

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
