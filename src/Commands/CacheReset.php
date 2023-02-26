<?php

namespace Asiifdev\EasyRole\Commands;

use Asiifdev\EasyRole\PermissionRegistrar;
use Illuminate\Console\Command;

class CacheReset extends Command
{
    protected $signature = 'easy-role:cache-reset';

    protected $description = 'Reset the permission cache';

    public function handle()
    {
        if (app(PermissionRegistrar::class)->forgetCachedPermissions()) {
            $this->info('Permission cache flushed.');
        } else {
            $this->error('Unable to flush cache.');
        }
    }
}
