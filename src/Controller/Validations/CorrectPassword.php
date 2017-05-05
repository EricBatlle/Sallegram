<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 04/05/2017
 * Time: 12:19
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;

class CorrectPassword extends Constraint
{
    public $message = 'The string "{{ string }}" contains bullshit';
}