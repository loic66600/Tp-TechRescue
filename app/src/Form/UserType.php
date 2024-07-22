<?php

namespace App\Form;

use Assert\Email;
use App\Entity\User;
use Assert\NotBlank;
use Doctrine\DBAL\Types\JsonType;
use App\Entity\ContactInformation;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, 
            [
                'label' => 'Email :',
                'label_attr' => [
                'class' => 'form-label'],
                'attr' => ['placeholder' => 'Votre addresse email', 'class' => 'form-control'],
                ],
                )
            
            ->add('Password', PasswordType::class,
            [
                'label' => 'Mot de passe :',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => ['placeholder' => 'Votre mot de passe', 'class' => 'form-control'],
            ])
            ->add('Roles', CollectionType::class, 
            [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label_attr' => [
                    'style' => 'display:none;'
                ],
                'attr' => [
                    'style' => 'display:none;'
                ],
            ])

            
            ->add('ContactInformation', ContactInformationType::class,
            [ 'label' => false,
                'label_attr' => ['class' => 'form-control'],
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
