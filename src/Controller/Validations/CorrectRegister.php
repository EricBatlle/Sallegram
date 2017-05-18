<?php
/**
 * Created by PhpStorm.
 * User: nuria
 * Date: 18/05/2017
 * Time: 11:55
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;


class CorrectRegister extends Constraint
{
    public $message = 'The string "{{ string }}" contains bullshit';
}