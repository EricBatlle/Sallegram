<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 14/05/2017
 * Time: 0:55
 */

namespace SilexApp\Controller\Validations;

use Symfony\Component\Validator\Constraint;


class CorrectComment extends Constraint
{
    public $message = 'The string "{{ string }}" contains bullshit';

}