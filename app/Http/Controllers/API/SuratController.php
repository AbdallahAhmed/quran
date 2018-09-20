<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuratController extends APIController
{
    /**
     * GET /surah
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $id = $request->get('surah_id');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 1000);

        $surah = Surat::find($id);
        if($surah){
        $surah->ayat = $surah->ayat()->take($limit)->offset($offset)->get();
        return $this->response($surah);
        }
        return $this->errorResponse('Surah not found');

    }
}
