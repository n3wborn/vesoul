<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
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
            // TODO: à modifier
            ->add('author',  EntityType::class, [
                'label' => 'ID auteur',
                'class' => Author::class,
                'choice_label' =>  function (Author $author) {
                    $first = $author->getFirstname();
                    $last = $author->getLastname();
                    // peuple le select avec les auteurs existants
                    return $first. ' ' .$last;
                    $choices = $first. ' ' .$last;
                    return $choices;
                },
                'query_builder' => function(EntityRepository $author) {
                    return $author->createQueryBuilder('a')
                        ->orderBy('a.firstname','ASC');
                },
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
            ->add('length', NumberType::class, [
                'label' => 'Nombre de pages',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('width', NumberType::class, [
                'label' => 'Largeur',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4 align-top'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'Année de sortie du livre',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('new', NumberType::class, [
                'label' => 'Inédit ?',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Quantité',
                'label_attr' => [
                    'class' => 'font-weight-bold py-1 m-0 col-4'
                ],
                'attr' => [
                    'class' => 'my-1 col-7'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'images',
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
