<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = ['key', 'value'];

    // You can define a scope or method to retrieve values by key if needed
    public static function getValue($key)
    {
        return self::where('key', $key)->first()->value ?? null;
    }
}
