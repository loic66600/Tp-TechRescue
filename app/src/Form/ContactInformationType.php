<?php

namespace App\Form;

use App\Entity\ContactInformation;
use App\Entity\User;
use Faker\Provider\ar_JO\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName' , TextType::class,
            [
                'label' => 'Nom :',
                'label_attr' => [
                    'class' => 'form-label mt-3'

                ],
                'attr' => ['placeholder' => 'Votre nom', 'class' => 'form-control'],
            ])
            ->add('firstName', TextType::class,
            [
                'label' => 'Prénom :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre prénom', 'class' => 'form-control'],
            ])
            ->add('phoneNumber', NumberType::class,
            [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre numéro de téléphone', 'class' => 'form-control'],
            ])
            ->add('address', TextType::class,
            [
                'label' => 'Adresse :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre adresse', 'class' => 'form-control'],
            ])
            ->add('city', TextType::class,
            [
                'label' => 'Ville :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre ville', 'class' => 'form-control'],
            ])
            ->add('zipCode', NumberType::class,
            [
                'label' => 'Code postal :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre code postal', 'class' => 'form-control'],
            ])
            
            ->add('country', TextType::class,
            [
                'label' => 'Pays :',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'attr' => ['placeholder' => 'Votre pays', 'class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactInformation::class,
        ]);
    }
}
