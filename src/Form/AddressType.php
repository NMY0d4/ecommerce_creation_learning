<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Donner un nom à votre adresse :',
                'attr' => ['placeholder' => 'Nommez votre adresse']
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'attr' => ['placeholder' => 'Entre votre nom']
            ])
            ->add('company', TextType::class, [
                'label' => 'Votre société :',
                'required' => false,
                'attr' => ['placeholder' => '(facultatif) Entrez le nom de votre société']
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse :',
                'attr' => ['placeholder' => '54 chemin de la réussite...']
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code Postal :',
                'attr' => ['placeholder' => 'Entrez votre code postale']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville :',
                'attr' => ['placeholder' => 'Paris...']
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays :',
                'attr' => ['placeholder' => 'France...']
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre téléphone :',
                'attr' => ['placeholder' => 'Numéro de téléphone']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-info']
            ])            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
