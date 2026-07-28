<?php

namespace App\Support;

// Cosmetic metadata (label/description/icon) for the permission slugs this
// app ships with. Purely presentational — new permissions created from the
// admin Roles screen still work fine, they just fall back to a
// prettified version of their slug instead of a curated description.
class PermissionCatalog
{
    protected static $catalog = [
        'manage-users' => [
            'label' => 'Manage Users',
            'description' => 'Create, edit, and delete user accounts; view the users list.',
            'icon' => 'mdi-account-multiple',
        ],
        'manage-roles' => [
            'label' => 'Manage Roles & Permissions',
            'description' => 'Create and edit roles, assign permissions, and manage the permission list.',
            'icon' => 'mdi-shield-crown',
        ],
        'review-slides' => [
            'label' => 'Review Slides',
            'description' => 'Approve or reject slides waiting in the pending queue.',
            'icon' => 'mdi-check-decagram',
        ],
        'manage-slides' => [
            'label' => 'Manage Slides',
            'description' => 'Upload, edit, reorder, and delete announcement slides.',
            'icon' => 'mdi-filmstrip',
        ],
        'manage-departments' => [
            'label' => 'Manage Departments',
            'description' => 'Create, rename, and delete departments.',
            'icon' => 'mdi-domain',
        ],
        'view-all-departments' => [
            'label' => 'View All Departments',
            'description' => "See and manage every department's slides, not just your own.",
            'icon' => 'mdi-eye-outline',
        ],
        'view-all-activity-logs' => [
            'label' => 'View All Activity Logs',
            'description' => "See every user's activity log entries, not just your own.",
            'icon' => 'mdi-format-list-bulleted',
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
        return static::$catalog[$name]['icon'] ?? 'mdi-key-outline';
    }
}
