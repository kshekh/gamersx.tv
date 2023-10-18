<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RemoteFontValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\RemoteFont $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $url_data = parse_url($value);
        if(isset($url_data['host']) && $url_data['host'] == 'fonts.googleapis.com') {
            return;
        }
        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
