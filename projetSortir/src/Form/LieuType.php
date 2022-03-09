<?php

namespace App\Form;

use App\Controller\GestionLieuxController;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null,
                [
                    'label' => 'Nom',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                ])
            ->add('rue', null,
                [
                    'label' => 'Rue',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                ])
            ->add('latitude', null,
                [
                    'label' => 'Latitude',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                ])
            ->add('longitude', null,
                [
                    'label' => 'Longitude',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                ])
            ->add('ville', EntityType::class,
                [
                    'label' => 'Ville',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                    'class'=>Ville::class,
                    'choice_label'=> 'nom',
                    'choice_attr' => ['class' => 'form-select'],

                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
