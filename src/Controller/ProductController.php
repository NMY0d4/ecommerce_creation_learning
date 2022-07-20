<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/nos-produits', name: 'app_products')]
    public function index(): Response
    {

        $products = $this->em->getRepository(Product::class)->findAll();
        
        return $this->render('product/index.html.twig', compact('products'));
    }

    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug): Response
    { 

        $product = $this->em->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product) {
            return $this->redirectToRoute('app_products');
        }
        
        return $this->render('product/show.html.twig', compact('product'));
    }
}
