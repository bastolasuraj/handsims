<?php

namespace App\Helpers;

class ActiveDirectory
{
    public function isEnabled()
    {
        return false;
    }

    public function authenticate($user, $pass)
    {
        return false;
    }
}
