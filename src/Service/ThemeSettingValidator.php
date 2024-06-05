<?php

namespace App\Service;

use App\Helper\ValidationMessageHelper;
use App\Validator\RemoteFont;
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
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
            'border_radius_top_left' =>  [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
            'border_radius_top_right' =>  [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
            'border_radius_bottom_right' =>  [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
            'border_radius_bottom_left' =>  [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
            'mobile_embed_height' =>  [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero()
            ],
        ];
        if(isset($requestParams['font_type']) && $requestParams['font_type'] == 'remote') {
            $validate_arr['remote_font_url'] = [
                new RemoteFont(),
            ];
        }
        if(isset($requestParams['header_logo'])) {
            $validate_arr['header_logo'] = [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'This file is not a valid image.'
                ]),
            ];
        }

        if(isset($requestParams['header_background_type']) && $requestParams['header_background_type']=='image' && isset($requestParams['header_background'])) {
            $validate_arr['header_background'] = [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'This file is not a valid image.'
                ]),
            ];
        }

        if(isset($requestParams['body_background_type']) && $requestParams['body_background_type']=='image' && isset($requestParams['body_background'])) {
            $validate_arr['body_background'] = [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'This file is not a valid image.'
                ]),
            ];
        }

        if(isset($requestParams['footer_background_type']) && $requestParams['footer_background_type']=='image' && isset($requestParams['footer_background'])) {
            $validate_arr['footer_background'] = [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'This file is not a valid image.'
                ]),
            ];
        }

        $req_arr = [
            'border' => $requestParams['border'],
            'border_radius_top_left' => $requestParams['border_radius_top_left'],
            'border_radius_top_right' => $requestParams['border_radius_top_right'],
            'border_radius_bottom_right' => $requestParams['border_radius_bottom_right'],
            'border_radius_bottom_left' => $requestParams['border_radius_bottom_left'],
            'mobile_embed_height' => $requestParams['mobile_embed_height'],
        ];

        if(isset($requestParams['header_logo'])) {
            $req_arr['header_logo'] = $requestParams['header_logo'];
        }
        if(isset($requestParams['header_background_type']) && $requestParams['header_background_type']=='image' && isset($requestParams['header_background'])) {
            $req_arr['header_background'] = $requestParams['header_background'];
        }
        if(isset($requestParams['body_background_type']) && $requestParams['body_background_type']=='image' && isset($requestParams['body_background'])) {
            $req_arr['body_background'] = $requestParams['body_background'];
        }
        if(isset($requestParams['footer_background_type']) && $requestParams['footer_background_type']=='image' && isset($requestParams['footer_background'])) {
            $req_arr['footer_background'] = $requestParams['footer_background'];
        }
        if(isset($requestParams['font_type']) && $requestParams['font_type'] == 'remote') {
            $req_arr['remote_font_url'] = $requestParams['remote_font_url'];
        }

        $constraint = new Assert\Collection($validate_arr);
        $validationResult = $this->validator->validate($req_arr, $constraint);
        return ValidationMessageHelper::prepareMessage($validationResult);
    }
}