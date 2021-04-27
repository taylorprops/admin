<?php

namespace App;

use App\Notifications\PasswordReset;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password', 'group', 'super_user', 'active', 'signature', 'photo_location'
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

    public function task_members() {
        return $this -> hasMany(\App\Models\Tasks\TasksMembers::class, 'user_id', 'id');
    }

    public function calendar_events() {
        return $this -> hasMany(\App\Models\Calendar\Calendar::class, 'user_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this -> notify(new PasswordReset($token));
    }
}
