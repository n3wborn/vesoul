<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'data' => 0,
                'choices' => [ 'Homme' => 0, 'Femme' => 1],
                'expanded' => true,
                'label' => 'Civilités'
            ])
            ->add('firstname')      
            ->add('lastname')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('username', EmailType::class) 
            ->add('tel')
            ->add('newsletter')
            ->add('birth', DateType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd-mm-yyyy',
                
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
