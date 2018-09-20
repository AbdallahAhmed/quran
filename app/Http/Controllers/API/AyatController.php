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
    public function show(Request $request)
    {
        $id = $request->get('ayah_id');
        $ayah = Ayat::find($id);
        $ayah->load('surah');
        if ($ayah) {
            return $this->response(['ayah' => $ayah]);
        } else {
            return $this->errorResponse(['Ayah Not Found']);
        }
    }
}
