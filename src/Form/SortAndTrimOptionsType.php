<?php

namespace App\Form;

use App\Entity\HomeRow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, NumberType };
use Symfony\Component\Form\FormBuilderInterface;

class SortAndTrimOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('itemSortType', ChoiceType::class, [
                'choices' => [
                    'Ascending Popularity' => HomeRow::SORT_ASC,
                    'Descending Popularity' => HomeRow::SORT_DESC,
                    'Fixed Order' => HomeRow::SORT_FIXED,
                ]
            ])
            ->add('maxContainers', NumberType::class, [
                'label' => 'Max Number of Containers',
                'required' => false,
            ])
            ->add('maxLive', NumberType::class, [
                'label' => 'Max Number of Live Embeds',
                'required' => false,
            ])
            ->addModelTransformer(new CallbackTransformer(
                // Use the array in the form
                function ($valuesAsArray) {
                    return $valuesAsArray;
                },
                // Don't set empty values in the JSON later
                function ($valuesAsArray) {
                    if ($valuesAsArray['maxContainers'] === null) {
                        unset($valuesAsArray['maxContainers']);
                    }
                    if ($valuesAsArray['maxLive'] === null) {
                        unset($valuesAsArray['maxLive']);
                    }
                    if (!empty($valuesAsArray)) {
                        return $valuesAsArray;
                    }
                }
            ))
            ;
    }
}
