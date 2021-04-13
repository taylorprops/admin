<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $table = 'config';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

}
