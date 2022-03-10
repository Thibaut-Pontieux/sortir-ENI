<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null,
                [
                    'label'=>'Pseudo',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control']
                ])
            ->add('plainPassword', PasswordType::class, [
                'label'=>'Mot de Passe',
                'label_attr' => ['class' => 'col-form-label input-group-text'],
                // On encode le MDP dans le controller
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nom', null,
                [
                    'label'=>'Nom',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control']
                ])
            ->add('prenom', null,
                [
                    'label'=>'Prénom',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control']
                ])
            ->add('telephone', null,
                [
                    'label'=>'Téléphone',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control']
                ])
            ->add('mail', null,
                [
                    'label'=>'Mail',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control']
                ])
            ->add('site', EntityType::class,
                [
                    'label' => 'Site',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                    'class' => Site::class, 'choice_label' => 'nom',
                    'choice_attr' => ['class' => 'form-select'],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
