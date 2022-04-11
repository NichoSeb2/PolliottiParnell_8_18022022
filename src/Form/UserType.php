<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType {
    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur", 
                'empty_data' => "",
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email'
            ])
        ;

        if ($options['new']) {
            $builder->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => $options['new'],
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => "Vous devez entrer un mot de passe.", 
                        ]), 
                    ], 
                ],
                'second_options' => [
                    'label' => 'Tapez le mot de passe à nouveau',
                    'constraints' => [
                        new NotBlank([
                            'message' => "Vous devez entrer une confirmation de mot de passe.", 
                        ]), 
                    ], 
                ],
            ]);
        }

        if ($this->security->isGranted("ROLE_ADMIN")) {
            $builder
                ->add('roles', ChoiceType::class, [
                    'label' => "Roles de l'utilisateur",
                    'choices' => [
                        "Utilisateur" => "ROLE_USER",
                        "Administrateur" => "ROLE_ADMIN",
                    ],
                    'data' => [
                        "ROLE_USER"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Vous devez sélectionner au moins un rôle.", 
                        ]), 
                    ], 
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
            'new' => true,
        ]);
    }
}
