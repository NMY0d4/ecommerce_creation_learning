<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/compte/addresses', name: 'app_account_address')]
    public function index(): Response
    {        
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add( Cart $cart, Request $request): Response
    { 
        $address = new Address;
        $form = $this->createForm( AddressType::class, $address);  
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush(); 
            if ($cart->get()) {
                return $this->redirectToRoute('app_order');           

            }
            return $this->redirectToRoute('app_account_address');           
        }
        
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function edit(Request $request, $id): Response
    { 
        $address = $this->em->getRepository(Address::class)->findOneById($id);
        $form = $this->createForm( AddressType::class, $address);  

        if(!$address || $address->getUser() != $this->getUser()) 
        return $this->redirectToRoute('app_account_address');
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {            
            $this->em->flush(); 
            return $this->redirectToRoute('app_account_address');           
        }
        
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function delete($id): Response
    { 
        $address = $this->em->getRepository(Address::class)->findOneById($id);    
        

        if($address && $address->getUser() === $this->getUser()) {            
            $this->em->remove($address);
            $this->em->flush(); 
        } 
        
        return $this->redirectToRoute('app_account_address');  

    }
}
