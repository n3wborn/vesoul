<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('price')
            ->add('isbn')
            ->add('stock')
            ->add('title')
            ->add('commands', ChoiceType::class, [
                'choices' => [],
                'label' => 'Commandes',
            ])
            ->add('genras', ChoiceType::class, [
                // 'choices' => $this->getChoices(),
                'label' => 'Catégories',
            ])
            ->add('author', ChoiceType::class, [
                'choices' => ['firstname', 'lastname'],
                'label' => 'Auteur',
            ]);
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
