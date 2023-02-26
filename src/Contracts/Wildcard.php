<?php

namespace Asiifdev\EasyRole\Contracts;

interface Wildcard
{
    /**
     * @param  string|Wildcard  $permission
     * @return bool
     */
    public function implies($permission): bool;
}
