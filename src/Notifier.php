<?php

namespace Piod\LaravelCommon;

class Notifier
{
    public function sayHi(string $sName)
    {
        return 'Hi ' . $sName;
    }

    public function getTestConfig()
    {
        return config('piod.x');
    }
}
