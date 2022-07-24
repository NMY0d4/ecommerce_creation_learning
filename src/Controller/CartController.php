<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        $cartComplete = [];

        foreach ($cart->get() as $id => $quantity) {
            $cartComplete[] = [
                'product' => $this->em->getRepository(Product::class)->findOneById($id),
                'quantity' => $quantity
            ];            
        }
                
        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_add_to_cart')]
    public function add($id, Cart $cart): Response
    {
        $cart->add($id);   
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_remove_my_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();   
        
        return $this->redirectToRoute('app_products');
    }
}
