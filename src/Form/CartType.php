<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    private Security $security;
    private AddressRepository $addressRepo;

    public function __construct(
        Security $security,
        AddressRepository $addressRepo
    )
    {
        $this->security = $security;
        $this->addressRepo = $addressRepo;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('items', CollectionType::class)
            ->add('deliveryAddress', EntityType::class, [
                'class' => Address::class,
                'choice_label' => function (Address $address){
                    return $address->getTitle();
                },
                'query_builder' => function (AddressRepository $adressRepo) use ($user) {
                    return $adressRepo->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user)
                    ;
                },
                'attr' => [
                    'class' => 'selectpicker custom-select',
                    'data-style' => 'btn-outline-secondary'
                ]
            ])
            ->add('billAddress', EntityType::class, [
                'class' => Address::class,
                'choice_label' => function (Address $address){
                    return $address->getTitle();
                },
                'query_builder' => function (AddressRepository $adressRepo) use ($user) {
                    return $adressRepo->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user)
                    ;
                },
                'attr' => [
                    'class' => 'selectpicker custom-select',
                    'data-style' => 'btn-outline-secondary'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Order::class]);
    }
}
