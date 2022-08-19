<?php

namespace App\Controller;

use App\Classe\Cart;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/create-checkout-session', name: 'app_stripe_create_session')]
    public function index(Cart $cart)
    {
         
        // $YOUR_DOMAIN = 'http://127.0.0.1:8000/public';
        
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach ($cart->getFull() as $product) {            
            
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' =>$product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ],

                ],
                'quantity' => $product['quantity'],
            ];
        }

        // This is your test secret API key.
         Stripe::setApiKey('sk_test_51LYSBdGQIU1otRZSbOUeUglgphNmw2sORhRy1XXvmOiTC3sNCsJucDX69ivOSQyLT7t5RgIWHk5xii7L5r25iYzc00KulN2w5I');

        $checkout_session = Session::create([
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        return $this->redirect($checkout_session->url);

    }
}
