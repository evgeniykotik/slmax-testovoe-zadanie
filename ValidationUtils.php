<?php
require_once "AllMessage.php";
require_once "Config.php";

class ValidationUtils
{
    const REG_EXP_LETTERS = "/^[a-zA-Z]+$/";
    const REG_EXP_DATE = "/\d{4}(-)\d{2}(-)\d{2}/";
    const ARRAY_OF_GENDER = [0, 1];
    const SYMBOLS = ['=', '>', '<', '>=', '<=', '!='];

    public static function regExpOnlyLetters($val)
    {
        return preg_match(ValidationUtils::REG_EXP_LETTERS, $val);
    }

    public static function regExpDate($val)
    {
        return preg_match(ValidationUtils::REG_EXP_DATE, $val);
    }

    public static function validGender($val)
    {
        return in_array($val, ValidationUtils::ARRAY_OF_GENDER);
    }

    public static function validID($val)
    {
        return is_int($val);
    }

    public static function isFieldValid($val, $length)
    {
        return (ValidationUtils::regExpOnlyLetters($val) && $val < $length);
    }

    public static function isSymbolValid($val)
    {
        return in_array($val, ValidationUtils::SYMBOLS);
    }
}