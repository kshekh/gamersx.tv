<?php

namespace App\Form;

use App\Entity\HomeRow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\{ NumberType, ChoiceType };
use Symfony\Component\Form\FormBuilderInterface;

class ContainerizerOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxEmbeds', NumberType::class, [
                'label' => 'Maximum Containers',
                'required' => false,
            ])
            ->add('itemSortType', ChoiceType::class, [
                'choices' => [
                    'Ascending Popularity' => HomeRow::SORT_ASC,
                    'Descending Popularity' => HomeRow::SORT_DESC,
                    'Fixed Order' => HomeRow::SORT_FIXED,
                ]
            ])
            ->add('topic', TopicType::class, [
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
                    if ($valuesAsArray['maxEmbeds'] === null) {
                        unset($valuesAsArray['maxEmbeds']);
                    }
                    if ($valuesAsArray['itemSortType'] === null) {
                        unset($valuesAsArray['itemSortType']);
                    }
                    if ($valuesAsArray['topic']['topicId'] === null) {
                        unset($valuesAsArray['topic']);
                    }
                    if (!empty($valuesAsArray)) {
                        return $valuesAsArray;
                    }
                }
            ))
            ;
    }
}
