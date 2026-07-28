<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Department;
use App\User;
use App\Slides;

// One-time backfill from the legacy free-text `department` columns (on
// `users` and `slides_table`) into the new `departments` table + FK columns.
// Those legacy columns get dropped in a later migration, so this is a no-op
// on a fresh install — departments just start empty until created via /departments.
class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        if (!Schema::hasColumn('users', 'department') && !Schema::hasColumn('slides_table', 'department')) {
            return;
        }

        $names = collect();
        if (Schema::hasColumn('users', 'department')) {
            $names = $names->merge(User::whereNotNull('department')->pluck('department'));
        }
        if (Schema::hasColumn('slides_table', 'department')) {
            $names = $names->merge(Slides::whereNotNull('department')->pluck('department'));
        }

        $names = $names->map(function ($name) {
                return trim($name);
            })
            ->filter()
            ->unique(function ($name) {
                return strtolower($name);
            });

        $departments = [];
        foreach ($names as $name) {
            $department = Department::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $departments[strtolower($name)] = $department;
        }

        if (Schema::hasColumn('users', 'department')) {
            User::whereNotNull('department')->whereNull('department_id')->get()->each(function (User $user) use ($departments) {
                $department = $departments[strtolower(trim($user->department))] ?? null;
                if ($department) {
                    $user->update(['department_id' => $department->id]);
                }
            });
        }

        if (Schema::hasColumn('slides_table', 'department')) {
            Slides::whereNotNull('department')->whereNull('department_id')->get()->each(function (Slides $slide) use ($departments) {
                $department = $departments[strtolower(trim($slide->department))] ?? null;
                if ($department) {
                    $slide->update(['department_id' => $department->id]);
                }
            });
        }
    }
}
