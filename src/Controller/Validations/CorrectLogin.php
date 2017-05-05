<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 05/05/2017
 * Time: 14:03
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;

class CorrectLogin extends Constraint
{
    public $message = 'The string "{{ string }}" contains bullshit';

}