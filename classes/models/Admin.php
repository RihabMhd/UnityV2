<?php

namespace Models;

use InvalidArgumentException;

class Admin extends User
{
    private string $permissions;

    public function __construct()
    {
        parent::__construct();
        $this->role = self::ROLE_ADMIN;
        $this->permissions = '';
    }

    public function getPermissions(): string
    {
        return $this->permissions;
    }

    public function setPermissions(string $permissions): void
    {
        $this->permissions = trim($permissions);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions_array = explode(',', $this->permissions);
        return in_array(trim($permission), array_map('trim', $permissions_array));
    }

    public function addPermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            $permissions_array = $this->permissions ? explode(',', $this->permissions) : [];
            $permissions_array[] = trim($permission);
            $this->permissions = implode(',', $permissions_array);
        }
    }

    public function removePermission(string $permission): void
    {
        $permissions_array = explode(',', $this->permissions);
        $permissions_array = array_filter($permissions_array, function($perm) use ($permission) {
            return trim($perm) !== trim($permission);
        });
        $this->permissions = implode(',', $permissions_array);
    }

    public function __toString(): string
    {
        return sprintf(
            "Admin #%d | Username: %s | Email: %s | Permissions: %s",
            $this->id ?? 0,
            $this->username ?? 'N/A',
            $this->email ?? 'N/A',
            $this->permissions ?: 'None'
        );
    }
}