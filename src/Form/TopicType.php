<?php

namespace App\Form;

use App\Entity\HomeRowItem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('searchType', HiddenType::class, [
                'label' => false,
                'mapped' => false,
                'data' => $options['searchType']
            ])
            ->add('topicId', null, [
                'attr' => ['readonly' => true, 'class' => 'topic-id'],
                'label' => 'Selected Topic ID',
            ])
            ->add('label', null,  [
                'required' => false,
                'attr' => ['readonly' => true, 'class' => 'topic-label'],
                'label' => 'Selected Topic Item\'s Label',
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
