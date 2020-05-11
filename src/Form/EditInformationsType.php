<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class EditInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class)
            ->add('username')
            ->add('tel')
            ->add('confirm_password', PasswordType::class)
            // ->add('confirm_password', null, array(
            // 'mapped' => false,
            // 'required'      => true,
            // 'multiple' => true,
            // 'class'         => 'UserBundle:User',
            // 'query_builder' => function(EntityRepository $er) {
                    // return $er->createQueryBuilder('u')
                        // ->where('u.type = 3');
                // }
            // ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
