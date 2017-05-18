<?php
/**
 * Created by PhpStorm.
 * User: nuria
 * Date: 18/05/2017
 * Time: 11:56
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorrectRegisterValidator extends ConstraintValidator
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
        $value = htmlentities($value);
        $alfanum = preg_match('/^[a-zA-Z0-9]+$/', $value);
        if(!$alfanum || strlen($value) > 20){
            $correct = true;
        }

        return $correct;
    }
}