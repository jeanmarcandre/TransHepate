<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/* TYPES */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Renseignez le titre de votre publication',
                'help' => '100 caractères maximum',
            ])

            ->add('content', CKEditorType::class, [
                'config_name' => 'config_phone',
                'label' => 'Définissez son contenu ',
                'purify_html' => true,
                'help' => '',
            ])
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
