<?php

namespace App\Traits;

trait HasMessages
{
    public static function messages(array $privateMessages = []){
        return array_merge(self::$messages, $privateMessages);
    }
}
