<?php

namespace App\Form;

use App\Entity\Permanences;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/* TYPES */
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class PermanencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', CKEditorType::class, [
                'config_name' => 'main_config',
                'label' => 'Définissez son contenu',
                'purify_html' => true,
                'help' => '',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permanences::class,
            'required' => false,
        ]);
    }
}
