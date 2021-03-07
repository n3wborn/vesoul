<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
    ) {
        $this->security = $security;
        $this->addressRepo = $addressRepo;
    }

    /**
     * CartType is the last Form used to make an order.
     * Once submitted, we will have every infos needed to submit it to te seller.
     *
     * In fact, this form is only used to get missing infos, that is :
     *  - which delivery address to use
     *  - where to send the bill
     *  - which additional infos may be useful
     *
     * So, there's no product to add here as these are already in current cart
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add(
                'deliveryAddress', EntityType::class, [
                'class' => Address::class,
                'label' => 'Livraison',
                'label_attr' => [
                    'class' => 'm-0 pb-3 font-weight-bold',
                ],
                'choice_label' => function (Address $address) {
                    return $address->getTitle();
                },
                'query_builder' => function (AddressRepository $addressRepo) use ($user) {
                    return $addressRepo->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user);
                },
                'attr' => [
                    'class' => 'selectpicker custom-select',
                    'data-style' => 'btn-outline-secondary'
                ]
                ]
            )
            ->add(
                'billAddress', EntityType::class, [
                'class' => Address::class,
                'label' => 'Facturation',
                'label_attr' => [
                    'class' => 'm-0 pb-3 font-weight-bold',
                ],
                'choice_label' => function (Address $address) {
                    return $address->getTitle();
                },
                'query_builder' => function (AddressRepository $addressRepo) use ($user) {
                    return $addressRepo->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user);
                },
                'attr' => [
                    'class' => 'selectpicker custom-select',
                    'data-style' => 'btn-outline-secondary'
                ]
                ]
            )
            ->add(
                'delivery_instructions', TextareaType::class, [
                'label' => false,
                'required'   => false,
                'empty_data' => "",
                'attr' => [
                    'class' => 'mt-2 w-100',
                    'placeholder' => 'Indiquez ici vos instructions ...'
                ]
                ]
            )
            ->add(
                'submit', SubmitType::class, [
                'label' => 'Commander',
                'attr' => [
                    'class' => 'btn btn-info w-100'
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Order::class]);
    }
}
