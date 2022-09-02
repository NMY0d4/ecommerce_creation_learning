<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'app_contact')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()){

            $this->addFlash('info', 'Merci de nous avoir contacté, notre équipe va vous répondre dans les meilleures délais.');

            $datas = $form->getData();
            dd($datas['message']);

            $mail = new Mail;            
            $mail->send('greg.marini@hotmail.fr', 'GM_Web', 'nouvelle demande de contact', $datas['message']);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ] );
    }
}
