<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity_logs extends Model
{
    //

    protected $table = "activity_logs";

    protected $fillable = ['name', 'email', 'activity'];
 
}
