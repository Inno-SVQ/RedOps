<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmail;
    use Notifiable;
    use HasRoles;

	public $jobNotifications = null;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($instance) {
            $instance->rid = Uuid::uuid4();
        });
    }

    public function jobNotifications()
    {
        if($this->jobNotifications == null) {
            $this->jobNotifications = $this->hasMany('App\JobNotification')->get();
        }
        return $this->jobNotifications;
    }
}
