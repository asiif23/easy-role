<?php

namespace Asiifdev\EasyRole\Exceptions;

use InvalidArgumentException;

class WildcardPermissionNotImplementsContract extends InvalidArgumentException
{
    public static function create()
    {
        return new static('Wildcard permission class must implements Asiifdev\EasyRole\Contracts\Wildcard contract');
    }
}
