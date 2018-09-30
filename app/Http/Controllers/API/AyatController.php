<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AyatController extends APIController
{
    /**
     * GET /ayah
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $id = $request->get('ayah_id');
        $ayah = Ayat::find($id);
        if ($ayah) {
            $ayah->load('surah');
            return $this->response($ayah);
        } else {
            return $this->errorResponse(['Ayah Not Found']);
        }
    }

    /**
     * GET /search
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $q = trim(urldecode($request->get('q')));
        $limit = $request->get('limit', 15);
        $offset = $request->get('offset', 0);

        $ayat = array();
        $ayats = Ayat::where([
            ['text', 'like', '%' . $q . '%'],
            ['edition_id', 78]
        ])
            ->withoutGlobalScopes()
            ->offset($offset)
            ->limit($limit)
            ->get();

        $ayat['ayat'] = array();
        foreach ($ayats as $key => $ayah) {
            $ayat['ayat'][] = Ayat::with('surah')->where('number', $ayah->number)->first();
        }
        //$ayat['count'] = count($ayat['ayat']);
        return $this->response($ayat['ayat']);
    }
}
