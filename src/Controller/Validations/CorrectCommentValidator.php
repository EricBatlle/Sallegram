<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 14/05/2017
 * Time: 0:56
 */

namespace SilexApp\Controller\Validations;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorrectCommentValidator extends ConstraintValidator
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
        return $correct;
    }
}