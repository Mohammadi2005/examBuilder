<?php

namespace App\Traits;

trait HasRules
{
    public static function rules(array $privateRules = []){
        return array_merge(self::$rules, $privateRules);
    }
}
