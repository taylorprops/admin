<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
    use HasFactory;

    public $table = 'password_resets';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

}
