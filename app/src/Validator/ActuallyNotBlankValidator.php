<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ActuallyNotBlankValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ActuallyNotBlank) {
            throw new UnexpectedTypeException($constraint, ActuallyNotBlank::class);
        }

        /* @var ActuallyNotBlank $constraint */

        if (null == $value || !is_string($value) || '' === trim($value)) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value ?: '<null>')
                ->addViolation();
        }
    }
}
