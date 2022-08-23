<?php

namespace App\Controller;

use App\Classe\Cart;
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
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if(!$order->isIsPaid()) {
            $cart->remove();
                      
            $order->setIsPaid(true);
            $this->em->flush();

            // Envoyer un email à notre client pour lui confirmer sa commande
        }

      
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
