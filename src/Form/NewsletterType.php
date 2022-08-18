<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
/* TYPES */
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Votre Prenom  *',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre Prénom']),
                ],
            ])

            ->add('nom', TextType::class, [
                'label' => 'Votre Nom  *',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre Nom']),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Votre email  *',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre adresse email']),
                ],
            ])

            // ->add('content', CKEditorType::class, [
            //     'config_name' => 'my_config',
            //     'label' => 'Définissez son contenu ',
            //     'purify_html' => true,
            //     'help' => '',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Newsletter::class,
            'required' => false,
        ]);
    }
}