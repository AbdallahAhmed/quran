<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = "tokens";

    public $timestamps = false;

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
