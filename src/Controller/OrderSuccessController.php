<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Classe\RetServ;
use App\Entity\Order;
use COM;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index(Cart $cart, $stripeSessionId, RetServ $retServ): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if($order->getState() == 0) {
            $cart->remove();
                      
            $order->setState(1);
            $this->em->flush();

            $apikey =  $retServ->getApiSecretKey();

            // Envoyer un email à notre client pour lui confirmer sa commande
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFullName()."<br>Merci pour votre commande, Cras ultricies ligula sed magna dictum porta. Nulla porttitor accumsan tincidunt. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Vivamus suscipit tortor eget felis porttitor volutpat. Quisque velit nisi, pretium ut lacinia in, elementum id enim.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullName(), 'Votre Commande sur My ecommerce est validée!', $content, $apikey);
        }
      
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
