<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Renseignez votre nom *',
                'help' => '25 caractères maximum',
            ])

            ->add('nickname', TextType::class, [
                'label' => 'Renseignez votre prénom *',
                'help' => '25 caractères maximum',
            ])

            ->add('email', EmailType::class, [
                'invalid_message' => 'Cet email n\'est pas valide.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un Email',
                    ]),
                ]
            ])

            ->add('title', TextType::class, [
                'label' => 'Renseignez le titre de votre publication *',
                'help' => '100 caractères maximum',
            ])

            ->add('content', TextareaType::class, [
                'label' => 'Définissez son contenu *',
                'help' => '',
            ])

            ->add('save', SubmitType::class, ['label' => 'Contactez-nous'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'required' => false,
        ]);
    }
}
