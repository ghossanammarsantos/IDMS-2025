<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    protected $table = 'access_menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;
}
