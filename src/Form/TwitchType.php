<?php

namespace App\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('twitchId', null, [
                'attr' => ['readonly' => true, 'class' => 'twitch-id'],
                'label' => 'Selected Twitch ID',
            ])
            ->add('label', null,  [
                'required' => false,
                'attr' => ['readonly' => true, 'class' => 'twitch-label'],
                'label' => 'Selected Twitch Item\'s Label',
            ])
            ->add('searchType', HiddenType::class, [
                'label' => false,
                'mapped' => false,
                'data' => $options['searchType']
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'searchType' => null
        ]);
    }

}
