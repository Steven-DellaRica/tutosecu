<?php
namespace App\Service;

class UtilsService{
    /**
     * @param string $string
     * @return string
     */
    public static function cleanInput(string $input){
        return htmlspecialchars(strip_tags(trim($input, ENT_NOQUOTES)));
    }
}