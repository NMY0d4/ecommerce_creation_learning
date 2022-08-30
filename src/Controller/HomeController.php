<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session): Response
    {        

        $products = $this->em->getRepository(Product::class)->findByIsBest(1);
        $headers = $this->em->getRepository(Header::class)->findAll();
        
        return $this->render('home/index.html.twig', compact('products', 'headers'));
    }
}
