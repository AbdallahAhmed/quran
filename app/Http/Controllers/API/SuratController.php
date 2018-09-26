<?php

namespace App\Http\Controllers\API;

use App\Models\Surat;
use Illuminate\Http\Request;

class SuratController extends APIController
{


    /**
     * GET /surah
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if($request->filled('surah_id')){
            return $this->errorResponse('surah_id is required.');
        }

        $id = $request->get('surah_id');

        $surah = Surat::find($id);

        if ($surah) {
            $ayat = $surah->ayat()->get();
            $surah->juz_name_en = $ayat[0]->juz_name_en;
            $surah->juz_name_ar = $ayat[0]->juz_name_ar;
            $surah->pages = $ayat->groupBy('page_id');
            return $this->response($surah);
        }

        return $this->errorResponse('Surah not found');

    }
}
