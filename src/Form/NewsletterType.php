<?php

namespace App\Form;

use App\Entity\Newsletter;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
/* TYPES */
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            'data_class' => Newsletter::class,
            'required' => false,
        ]);
    }
}