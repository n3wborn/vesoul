<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Command;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CommandType extends AbstractType
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private AddressRepository $repoAddrress;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        AddressRepository $repoAddress

    )
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->repoAddrress = $repoAddress;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $addresses = $this->repoAddrress->findBy(['user' => $user]);

        $builder
            ->add('livraison', EntityType::class, [
                'class' => Address::class,
                'choice_label' => function (Address $address){
                    return $address->getTitle();
                },
                'query_builder' => function (EntityRepository $repository) use ($user) {
                    return $repository->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user)
                    ;
                },
                'attr' => [
                    'class' => 'selectpicker custom-select',
                    'data-style' => 'btn-outline-secondary'
                ]
            ])
            ->add('facturation', EntityType::class, [
            'class' => Address::class,
            'choice_label' => function (Address $address){
                return $address->getTitle();
            },
            'query_builder' => function (EntityRepository $repository) use ($user) {
                return $repository->createQueryBuilder('a')
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
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
