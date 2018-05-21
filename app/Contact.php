<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // mention field mass input(1)
    protected $fillable = ['name', 'email'];
}
