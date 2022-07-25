<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class Cart
{   
    public function __construct(private RequestStack $stack, private EntityManagerInterface $em)
    {
        $this->session =$stack->getSession();
    }

    public function add($id)
    {        
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id) {

        $cart = $this->session->get('cart', []);
        unset($cart[$id]);

        return $this->session->set('cart', $cart);

    }

    public function decrease($id) {
        $cart = $this->session->get('cart', []);

        if($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);                        
        }

        return $this->session->set('cart', $cart); 

    }

    public function getFull() {
        
        $cartComplete = [];

        if($this->get()) {            
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->em->getRepository(Product::class)->findOneById($id);
                if(!$product_object) {
                    $this->delete($id);
                    continue;
                }
                
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];            
            }
        }

        return $cartComplete;
            
    }
}