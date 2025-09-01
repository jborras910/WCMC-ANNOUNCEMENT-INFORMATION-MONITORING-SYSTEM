<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slides extends Model
{

    protected $table = "slides_table";

    protected $fillable = ['title', 'description', 'file', 'added_by_email','department','status'];

    protected $attributes = [
        'title' => '',
        'description' => '',
        'status' => 'pending'
    ];

}
