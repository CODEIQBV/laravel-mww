<?php

namespace YourNamespace\MyOnlineStore\Facades;

use Illuminate\Support\Facades\Facade;

class MyOnlineStore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'myonlinestore';
    }
} 