<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UuidModel extends Model
{
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($instance) {
            $instance->id = Uuid::uuid4();
        });
    }
}