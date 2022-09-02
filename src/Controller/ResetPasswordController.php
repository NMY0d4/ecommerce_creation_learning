<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/mot-de-pass-oublie', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $email = $request->get('email');        

        if ($email) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($email);

            if($user){
                
                // 1 : Enregistrer en base la demande de reset_password avec user, token, createdAt.
                $reset_password = new ResetPassword;
                $reset_password->setRelation($user);
                $reset_password->setToken(Uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->em->persist($reset_password);
                $this->em->flush();

                // 2 : Envoyer un email à l'utilisateur avec un lien lui mermettant de maj son mdp

                $url = $this->generateUrl('app_update_password', ['token' => $reset_password->getToken()]);
                

                $content = "Bonjour ".$user->getFullName()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur notre site.<br/><br/>";
                $content.='Merci de bien vouloir cliquer sur le lien suivant pour <a href="'.$url.'" >mettre à jour votre mot de passe</a>.';                

                $mail = new Mail();                
                $mail->send($email, $user->getFullName(), 'Réinitialiser votre mot de passe sur GM_Web My Ecommerce', $content);
                $this->addFlash('info', 'Vous allez recevoir un mail avec la procédure pour réinitialiser votre mot de passe(ℹ valable 1h).');
            } else {
            $this->addFlash('info', 'Cette adresse email est inconnue.');

            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'app_update_password')]
    public function update($token, Request $request, UserPasswordHasherInterface $hasher)
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('app_reset_password');
        }

        // Vérifier si le createdAt = now - 3h
        $now = new DateTimeImmutable(); 

        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour')){
            $this->addFlash('info', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('app_reset_password');
        }

        // Rendre uen vue avec mot de passe et confirmez votre mdp
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData(); 
            $user = $reset_password->getRelation();
            
            // Encodage du mdp
            $password = $hasher->hashPassword($user, $new_pwd);
            $user->setPassword($password);                
            
            // flush
            $this->em->flush();

            // redirection user vers connexion
            $this->addFlash('info', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);

    }
}

