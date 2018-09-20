<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $table = 'surat';

    public function ayat(){
        return $this->hasMany(Ayat::class, 'surat_id', 'id');
    }
}
