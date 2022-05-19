<?php

namespace App\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('username')
        ->add('roles', ChoiceType::class, [
            'label' => 'Modifier le rôle de l\'utilisateur',
            'placeholder' => false,
            'required' => true,
            'multiple' => false,
            'expanded' => true,
            'choices'  => [
                'ADMIN' => 'ROLE_ADMIN',
                'USER' => 'ROLE_USER'
            ],
        ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array(json) to a string
                    return implode(', ', $rolesArray);
                },
                function ($rolesString) {
                    // transform the string back to an array(json)
                    return explode(', ', $rolesString);
                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

//     public function index(Request $request): Response
// {
//     $user = new User();

//     $form = $this->createForm(UserType::class, $user);

//     $form->handleRaquest($request);
//     if ($form->isSuubmitted() && $form->isValid()){
//         dump($user);die;
//     }

//     return $this->render('default/index.html.twig', [
//         'form' => $form->createView()
//     ]);
// }
}
