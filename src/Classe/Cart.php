<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;


class Cart
{
   
    public function __construct(private RequestStack $stack)
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
}