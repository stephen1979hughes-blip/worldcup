<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function gender(): string
    {
        return session('gender', 'men');
    }
}
