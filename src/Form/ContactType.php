<?php

namespace App\Form;

use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
/* TYPES */
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
/* CONSTRAINTS */
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'label' => 'Votre nom complet *',
                'help' => 'entre 5 et 70 caractères maximum',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre nom complet']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre nom complet doit contenir au moins {{ limit }} caractères',
                        'max' => 70,
                        'maxMessage' => 'Votre nom complet doit contenir au maximum {{ limit }} caractères'
                    ]),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Votre email  *',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre adresse email']),
                    new Email(['message' => 'L\'adresse email {{ value }} n\'est pas une adresse valide']),
                ],
            ])

            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 8, 'maxlength' => 2000],
                'label' => 'Le contenu de votre message *',
                'help' => 'entre 10 et 2000 caractères maximum',
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ doit contenir votre message']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères',
                        'max' => 2000,
                        'maxMessage' => 'Le message doit contenir au maximum {{ limit }} caractères'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }
}
