<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/create-checkout-session/{reference}', name: 'app_stripe_create_session')]
    public function index(EntityManagerInterface $em ,Cart $cart, $reference)
    {
         
        // $YOUR_DOMAIN = 'http://127.0.0.1:8000/public';
        
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $em->getRepository(Order::class)->findOneByReference($reference);

        if (!$order) new JsonResponse(['error' => 'order']); 
      

        foreach ($order->getOrderDetails()->getValues() as $product) {             
            
            $product_object = $em->getRepository(Product::class)->findOneByName($product->getProduct());
            
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' =>$product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],

                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' =>$order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],

            ],
            'quantity' => 1,
        ];

        // This is your test secret API key.
         Stripe::setApiKey('sk_test_51LYSBdGQIU1otRZSbOUeUglgphNmw2sORhRy1XXvmOiTC3sNCsJucDX69ivOSQyLT7t5RgIWHk5xii7L5r25iYzc00KulN2w5I');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        
        $em->flush();

        return $this->redirect($checkout_session->url);

    }
}
