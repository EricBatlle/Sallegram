<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 05/04/2017
 * Time: 19:00
 */

namespace SilexApp\Model\Services;


class Calculator
{
    public function add(int $firstNumber, int $secondNumber){
        return $firstNumber + $secondNumber;
    }
}