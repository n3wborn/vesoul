<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genra;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
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
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'autofocus' => true,
                ]
            ])
            ->add('author',  EntityType::class, [
                'label' => 'Auteur',
                'class' => Author::class,
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
            ->add('genras', EntityType::class, [
                'required' => false,
                'label' => 'Catégorie',
                'class' => Genra::class,
                'choice_label' =>  function(Genra $genras) {
                    return $genras->getName();
                },
                'query_builder' => function(EntityRepository $genras) {
                    return $genras->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC')
                        ->distinct();
                },
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Quantité',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
                'label_attr' => [
                    'class' => 'font-weight-bold',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('length', NumberType::class, [
                'required' => false,
                'label' => 'Nombre de pages',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('year', NumberType::class, [
                'required' => false,
                'label' => 'Année de sortie',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('new', CheckboxType::class, [
                'required' => false,
                'label' => 'Nouveauté',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'label' => 'Hauteur',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => 'Largeur',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('images', FileType::class, [
                'required' => false,
                'label' => false,
                'multiple' => true,
                'mapped' => false,
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
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-secondary text-light m-2'
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
