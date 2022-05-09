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
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('objet')
            ->add('textarea')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function index(Request $request): Response
{
    $user = new User();

    $form = $this->createForm(UserType::class, $user);

    $form->handleRaquest($request);
    if ($form->isSuubmitted() && $form->isValid()){
        dump($user);die;
    }

    return $this->render('default/index.html.twig', [
        'form' => $form->createView()
    ]);
}
}
