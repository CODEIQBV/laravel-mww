<?php

namespace CodeIQ B.V.\LaravelMww\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CodeIQ B.V.\LaravelMww\LaravelMww
 */
class LaravelMww extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CodeIQ B.V.\LaravelMww\LaravelMww::class;
    }
}
