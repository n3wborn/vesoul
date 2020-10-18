<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('isbn', TextType::class, [
                'label' => 'Code ISBN du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('author', textType::class, [
                'label' => 'Auteur du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'Date de sortie du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Nombre de livre en stock',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Couverture',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'placeholder' => 'Sélectionnez un fichier',
                    'class' => 'my-1 col-7'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '512m',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Fichier png/jpg/jpeg inférieur à 512Mo',
                    ])
                ],
            ])
            // ->add('commands', ChoiceType::class, [
            //     'choices' => [],
            //     'label' => 'Commandes',
            //     'label_attr' => [
            //         'class' => 'font-weight-bold py-1 m-0 col-4'
            //     ],
            //     'attr' => [
            //         'class' => 'my-1 col-7'
            //     ]
            // ])
            // ->add('genras', ChoiceType::class, [
            //     'label' => 'Catégories du livre',
            //     'label_attr' => [
            //         'class' => 'font-weight-bold py-1 m-0 col-4'
            //     ],
            //     'attr' => [
            //         'class' => 'my-1 col-7'
            //     ]
            //     // 'choices' => $this->getChoices()
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'enregistrer',
                'attr' => [
                    'class' => 'btn btn-secondary text-light'
                ]
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
