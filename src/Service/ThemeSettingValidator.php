<?php

namespace App\Service;

use App\Helper\ValidationMessageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class ThemeSettingValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateData(array $requestParams): array
    {

        $validate_arr = [
            'border' =>  [
                new Assert\NotBlank()
            ],
            'border_radius' =>  [
                new Assert\NotBlank()
            ],

        ];
        if(isset($requestParams['header_logo'])) {
            $validate_arr['header_logo'] = [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'This file is not a valid image.'
                ]),
            ];
        }

        $req_arr = [
            'border' => $requestParams['border'],
            'border_radius' => $requestParams['border_radius'],
        ];
        if(isset($requestParams['header_logo'])) {
            $req_arr['header_logo'] = $requestParams['header_logo'];
        }

        $constraint = new Assert\Collection($validate_arr);
        $validationResult = $this->validator->validate($req_arr, $constraint);
        return ValidationMessageHelper::prepareMessage($validationResult);
    }
}