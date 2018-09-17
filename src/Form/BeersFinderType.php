<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeersFinderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude', NumberType::class, array(
                'label' => 'Set latitude',
                'help' => 'Write longitude in decimal degree',
                'invalid_message' => 'Must be a number',
                'scale' => 8,
                //DELETE DELETE
                'required' =>false,
                'empty_data' => 51.742503,
            ))
            ->add('longitude', NumberType::class, array(
                'label' => 'Set longitude',
                'help' => 'Write longitude in decimal degree',
                'invalid_message' => 'Must be a number',
                'scale' => 8,
                //DELETE DELETE
                'required' =>false,
                'empty_data' => 19.432956,

            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Find some beer!',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
