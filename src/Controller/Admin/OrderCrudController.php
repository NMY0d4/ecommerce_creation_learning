<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $em, private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {  

        
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
        ->add('detail', $updatePreparation)
        ->add('detail', $updateDelivery)
        ->add('index', 'detail');
        
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();        
        if($order->getState() == 0)
        $this->addFlash('danger', "<span><strong>Attention la commande ".$order->getReference()." <u>n'a pas été payé</u>.</strong></span>");
        if($order->getState() == 1)
        $this->addFlash('info', "<span><strong>La commande ".$order->getReference()." est bien <u>en cours de préparation</u>.</strong></span>");
        $order->setState(2);
        $this->em->flush();

        $url = $this->adminUrlGenerator->setController(OrderCrudController::class)->setAction('index')->generateUrl();

        return $this->redirect($url);
        
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();        
        if($order->getState() == 2)
        $this->addFlash('success', "<span><strong>La commande ".$order->getReference()." est bien <u>en cours de livraison</u>.</strong></span>");
        $order->setState(3);
        $this->em->flush();

        $url = $this->adminUrlGenerator->setController(OrderCrudController::class)->setAction('index')->generateUrl();

        return $this->redirect($url);
        
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),  
            TextEditorField::new('delivery', 'Adresse de livraison')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'transporteur'),
            MoneyField::new('CarrierPrice', 'Frais de port')->setCurrency('EUR'),            
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payé' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3

            ]),
            ArrayField::new('orderDetails', 'produits achetés')->hideOnIndex()        
        ];
    }
    
}
