<?php

namespace App\Support;

// Cosmetic metadata (label/description/icon/color) for the role names this
// app ships with. Custom roles created from the admin Roles screen still
// work fine — they just fall back to a prettified slug and a neutral look.
class RoleCatalog
{
    protected static $catalog = [
        'master_admin' => [
            'label' => 'Master Admin',
            'description' => 'Full access — user accounts, roles & permissions, and slides.',
            'icon' => 'mdi-crown',
            'color' => 'danger',
        ],
        'admin' => [
            'label' => 'Admin',
            'description' => 'Reviews the slide queue and manages announcement slides.',
            'icon' => 'mdi-shield-account',
            'color' => 'primary',
        ],
        'faculty' => [
            'label' => 'Faculty',
            'description' => 'Submits slides for approval.',
            'icon' => 'mdi-account',
            'color' => 'secondary',
        ],
    ];

    public static function label(string $name): string
    {
        return static::$catalog[$name]['label'] ?? ucwords(str_replace(['-', '_'], ' ', $name));
    }

    public static function description(string $name): ?string
    {
        return static::$catalog[$name]['description'] ?? null;
    }

    public static function icon(string $name): string
    {
        return static::$catalog[$name]['icon'] ?? 'mdi-account-outline';
    }

    public static function color(string $name): string
    {
        return static::$catalog[$name]['color'] ?? 'secondary';
    }
}
