<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAddressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "form-control mb-4",
                    'placeholder' => 'Libellé'
                ]
                ]
            )
            ->add(
                'lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "form-control mb-4",
                    'placeholder' => 'Nom'
                ]
                ]
            )
            ->add(
                'firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "form-control mb-4",
                    'placeholder' => 'Prénom'
                ]
                ]
            )
            ->add(
                'number', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Type (bis, ter, ...)'
                ]
                ]
            )
            ->add(
                'type', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Type'
                ]
                ]
            )
            ->add(
                'street', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Rue'
                ]
                ]
            )
            ->add(
                'city', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Ville'
                ]
                ]
            )
            ->add(
                'cp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Code Postal'
                ]
                ]
            )
            ->add(
                'country', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => "col-5 form-control mb-4",
                    'placeholder' => 'Pays'
                ]
                ]
            )
            ->add(
                'additional', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => "form-control mb-4",
                    'placeholder' => 'Informations supplémentaires'
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Address::class]);
    }
}
