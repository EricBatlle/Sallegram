<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 04/05/2017
 * Time: 12:23
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorrectPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if($this->isValid($value)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string}}',$value)
                ->addViolation();
        }
    }

    public function isValid($value)
    {
        $correct = false;
        //Html - string conversion
        $value = htmlentities($value);
        //Password - 6-12 chars + mayus + minus + num
        $uppercase = preg_match('@[A-Z]@', $value);
        $lowercase = preg_match('@[a-z]@', $value);
        $number    = preg_match('@[0-9]@', $value);
        if(!$uppercase || !$lowercase || !$number || strlen($value) < 6 || strlen($value) > 12) {
            $correct = true;
        }
        return $correct;
    }
}