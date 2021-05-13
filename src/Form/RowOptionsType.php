<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class RowOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numEmbeds', NumberType::class, [
                'label' => 'Number of Embeds',
                'required' => false,
            ])
            ->add('filter', TopicType::class, [
                'searchType' => 'game',
                'required' => false,
            ])
            ->addModelTransformer(new CallbackTransformer(
                // Use the array in the form
                function ($valuesAsArray) {
                    return $valuesAsArray;
                },
                // Don't set empty values in the JSON later
                function ($valuesAsArray) {
                    if ($valuesAsArray['numEmbeds'] === null) {
                        unset($valuesAsArray['numEmbeds']);
                    }
                    if ($valuesAsArray['filter']['topicId'] === null) {
                        unset($valuesAsArray['filter']);
                    }
                    if (!empty($valuesAsArray)) {
                        return $valuesAsArray;
                    }
                }
            ))
            ;
    }
}
