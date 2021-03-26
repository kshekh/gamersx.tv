<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('twitchId', null, [
                'attr' => ['readonly' => true, 'class' => 'twitch-id'],
            ])
            ->add('label', null,  [
                'required' => false,
                'attr' => ['readonly' => true, 'class' => 'twitch-label'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'inherit_data' => true,
        ]);
    }

}
