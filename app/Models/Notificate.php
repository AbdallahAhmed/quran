<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Notificate extends Model
{
    protected $table = "notifications";

    protected $appends = ['message'];

    public function getMessageAttribute(){
        $type = $this->type;
        if($type == "new_contest_content"){
            return str_replace(":contest", $this->contest->name,trans('app.'.$type));
        }elseif ($type == "new_member_join_contest"){
            return str_replace(":name", User::find($this->from_id)->name, trans('app.'.$type));
        }
    }

    public function receiver(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function sender(){
        return $this->hasOne(User::class, 'id', 'from_id')->with('photo');
    }

    public function contest(){
        return $this->hasOne(Contest::class, 'id', 'contest_id');
    }
}
