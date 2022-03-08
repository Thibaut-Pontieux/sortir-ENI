<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*
         * Création d'une variable avec un tableau d'option.
         */
        //$isCPEditable = $options['isCPEditable'];

        $builder
            ->add('nom', null,
            [
                'label' => 'nom',
                'label_attr' => ['class' => 'col-form-label input-group-text'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cp', null,
                [
                    'label' => 'code postal',
                    'label_attr' => ['class' => 'col-form-label input-group-text'],
                    'attr' => ['class' => 'form-control'],
                ])
        ;

        /*
         * Si la variable existe, le formulaire pour le CP est créé.
         * (Supprimer du builder du dessus pour que ça fonctionne)
         */
        /*if ($isCPEditable) {
            $builder
                ->add('cp', null,
                    [
                        'label' => 'code postal',
                        'label_attr' => ['class' => 'col-form-label input-group-text'],
                        'attr' => ['class' => 'form-control'],
                    ])
            ;
        }*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
            //'isCPEditable' => true,
        ]);
    }
}
