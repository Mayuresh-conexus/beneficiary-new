<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermission($permission);
    }

    public function hasPermission($permission)
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }
        return (bool)$this->roles->map->permissions->flatten()->contains('name', $permission);
    }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
            if (is_string($role)) {
                return Role::where('name', $role)->first();
            }
            return $role;
        })
            ->filter()
            ->all();

        $this->roles()->syncWithoutDetaching($roles);

        return $this;
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        $this->roles()->detach($role);
        return $this;
    }
}
