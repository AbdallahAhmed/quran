<?php

namespace App\Http\Controllers\API;

use App\Models\Juz;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JuzController extends APIController
{
    /**
     * GET /juz
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $id = $request->get('juz_id');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', PHP_INT_MAX);

        $juz = Juz::find($id);
        if($juz){
            $juz->load('surat');
            $juz['ayat'] = $juz->ayat()->take($limit)->offset($offset)->get();
            return $this->response($juz);
        }
        return $this->errorResponse('Juz not found');
    }
}
