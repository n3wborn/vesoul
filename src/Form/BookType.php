<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                // varchar 150
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'autofocus' => true,
                    'required' => true,
                ]
            ])
            ->add('isbn', TextType::class, [
                // varchar 100
                'label' => 'ISBN',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('author',  EntityType::class, [
                'label' => 'Auteur',
                'class' => Author::class,
                'required' => true,
                'choice_label' =>  function (Author $author) {
                    $first = $author->getFirstname();
                    $last = $author->getLastname();
                    $choices = $first. ' ' .$last;
                    return $choices;
                },
                'query_builder' => function(EntityRepository $author) {
                    return $author->createQueryBuilder('a')
                        ->orderBy('a.firstname','ASC');
                },
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                // double
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                // longtext
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('length', NumberType::class, [
                // int 11
                'label' => 'Nombre de pages',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('width', NumberType::class, [
                // int 11
                'label' => 'Largeur',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('height', NumberType::class, [
                // int 11
                'label' => 'Hauteur',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('year', NumberType::class, [
                // int 11
                'label' => 'Année de sortie',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('new', CheckboxType::class, [
                'label' => 'Nouveauté',
                'required' => false,
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])
            ->add('stock', NumberType::class, [
                // int 11
                'label' => 'Quantité',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'mt-1 mb-3 form-control'
                ]
            ])

            ->add('genras', ChoiceType::class, [
                'required' => true,
                'label' => 'Catégorie',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
                // 'choices' => $this->getChoices()
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Fichiers png/jpg/jpeg inférieurs à 2Mo',
                    ])
                ]
            ])

            // ->add('commands', ChoiceType::class, [
            //     'choices' => [],
            //     'label' => 'Commandes',
            //     'label_attr' => [
            //         'class' => 'font-weight-bold py-1 m-0 col-4'
            //     ],
            //     'attr' => [
            //         'class' => 'mt-1 mb-3 col-7'
            //     ]
            // ])

            ->add('submit', SubmitType::class, [
                'label' => 'enregistrer',
                'attr' => [
                    'class' => 'btn btn-secondary text-light my-2'
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
