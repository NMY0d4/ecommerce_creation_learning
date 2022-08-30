<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OrderController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('app_account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);        

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods:['POST', 'GET'])]
    public function add(Cart $cart, Request $request): Response
    {        

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();  
            $delivery_content.= '<br/>'.$delivery->getPhone();
                     
            if($delivery->getCompany()) $delivery_content.= '<br/>'.$delivery->getCompany();

            $delivery_content.= '<br/>'.$delivery->getAddress();
            $delivery_content.= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content.= ' - '.$delivery->getCountry();

            // Enregistrer ma commande order
            $order = new Order;
            $reference = $date->format('dmY').'-'.uniqid();


            $order->setReference($reference);

            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->em->persist($order);
            

            // Enregistrer mes produits orderderails            
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails;
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']); 
                $this->em->persist($orderDetails);                 
              
            }

            $this->em->flush();
           
            return $this->render('order/add.html.twig', [            
                'cart' => $cart->getFull(),
                'carrier' => $carriers, 
                'delivery' => $delivery_content,
                'reference' => $order->getReference()                
            ]);           

        }

        return $this->redirectToRoute('app_cart');
        
    }

}
