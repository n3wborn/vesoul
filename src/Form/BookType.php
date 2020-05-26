<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du livre'
            ])          
            ->add('isbn', TextType::class, [
                'label' => 'Code ISBN du livre'
            ])
            ->add('author', ChoiceType::class, [
                'label' => 'Auteur du livre',
                'choices' => ['firstname', 'lastname']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du livre'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix du livre'
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Nombre de livre en stock'
            ])
            ->add('commands', ChoiceType::class, [
                'choices' => [],
                'label' => 'Commandes',
            ])
            ->add('genras', ChoiceType::class, [
                'label' => 'Catégories du livre',
                // 'choices' => $this->getChoices()
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'enregistrer',
                'attr' => [
                    'class' => 'btn btn-secondary text-light'
                ]
            ])

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }

    //     private function getChoices(){
    //         $choices = 
    //     }
}
