<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    // Canonical role names used by the default seeder — actual roles/permissions
    // are stored in the database and can be changed from the admin Roles screen.
    const ROLE_MASTER_ADMIN = 'master_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_FACULTY = 'faculty';

    protected $table = "users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'middle_name', 'email', 'username', 'password','status','department_id'
    ];
    protected $attributes = [
        'middle_name' => '',

    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isMasterAdmin(): bool
    {
        return $this->hasRole(self::ROLE_MASTER_ADMIN);
    }

    // "Admin" abilities (slide review, etc.) are granted to master_admin too.
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([self::ROLE_MASTER_ADMIN, self::ROLE_ADMIN]);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
