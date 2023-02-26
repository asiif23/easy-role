<?php

namespace Asiifdev\EasyRole\Commands;

use Asiifdev\EasyRole\Contracts\Permission as PermissionContract;
use Asiifdev\EasyRole\Contracts\Role as RoleContract;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\TableCell;

class Show extends Command
{
    protected $signature = 'easy-role:show
            {guard? : The name of the guard}
            {style? : The display style (default|borderless|compact|box)}';

    protected $description = 'Show a table of roles and permissions per guard';

    public function handle()
    {
        $permissionClass = app(PermissionContract::class);
        $roleClass = app(RoleContract::class);
        $team_key = config('easyrole.column_names.team_foreign_key');

        $style = $this->argument('style') ?? 'default';
        $guard = $this->argument('guard');

        if ($guard) {
            $guards = Collection::make([$guard]);
        } else {
            $guards = $permissionClass::pluck('guard_name')->merge($roleClass::pluck('guard_name'))->unique();
        }

        foreach ($guards as $guard) {
            $this->info("Guard: $guard");

            $roles = $roleClass::whereGuardName($guard)
                ->with('permissions')
                ->when(config('easyrole.teams'), function ($q) use ($team_key) {
                    $q->orderBy($team_key);
                })
                ->orderBy('name')->get()->mapWithKeys(function ($role) use ($team_key) {
                    return [$role->name.'_'.($role->$team_key ?: '') => ['permissions' => $role->permissions->pluck('id'), $team_key => $role->$team_key]];
                });

            $permissions = $permissionClass::whereGuardName($guard)->orderBy('name')->pluck('name', 'id');

            $body = $permissions->map(function ($permission, $id) use ($roles) {
                return $roles->map(function (array $role_data) use ($id) {
                    return $role_data['permissions']->contains($id) ? ' ✔' : ' ·';
                })->prepend($permission);
            });

            if (config('easyrole.teams')) {
                $teams = $roles->groupBy($team_key)->values()->map(function ($group, $id) {
                    return new TableCell('Team ID: '.($id ?: 'NULL'), ['colspan' => $group->count()]);
                });
            }

            $this->table(
                array_merge([
                    config('easyrole.teams') ? $teams->prepend('')->toArray() : [],
                    $roles->keys()->map(function ($val) {
                        $name = explode('_', $val);

                        return $name[0];
                    })
                    ->prepend('')->toArray(),
                ]),
                $body->toArray(),
                $style
            );
        }
    }
}
