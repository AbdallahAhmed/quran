<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AyatController extends APIController
{
    public function show($id){

        $ayah = Ayat::where([
            ['id','=', $id],
            ['edition_id', '=', 77]
        ])->first();
        if($ayah){
            $data = array();
            $data['number'] = $ayah->number;
            $data['text'] = $ayah->text;
            $data['surah'] = $ayah->surah;
            $data['juz'] = $ayah->juz;
            $data['page'] = $ayah->page;
            $data['hizbQuarter'] = $ayah->hizbQuarter_id;
            $data['numberInSurah'] = $ayah->numberinsurat;
            $data['sajda'] = $ayah->sajda_id ? true : false;

            return $this->response($data, true, 200);
        }else{
            return $this->errorResponse(['Ayah Not Found'], 400);
        }
    }
}
