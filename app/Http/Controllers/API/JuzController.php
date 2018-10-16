<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\Juz;
use App\Models\Surat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JuzController extends APIController
{
    /**
     * GET /juz
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $id = $request->get('juz_id');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', PHP_INT_MAX);

        $juz = Juz::find($id);
        if ($juz) {
            //$juz->load('surat');
            $juz = $juz->ayat()->take($limit)->offset($offset)->get()->load('surah');
            return $this->response($juz);
        }
        return $this->errorResponse('Juz not found');
    }

    /**
     * GET juz/sections
     * @return \Illuminate\Http\JsonResponse
     */
    public function sections(){
        return response()->json(json_decode(file_get_contents(public_path('api/juz_surat.json'))));
    }
}
