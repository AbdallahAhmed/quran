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
        $id = $request->get('surah_id');

        $lang = $request->get("lang", app()->getLocale());

        $surah = Surat::find($id);

        if ($surah) {

            $ayat = $surah->ayat()->get();

            if ($lang == "en") {
                $surah->name = $surah->englishname;
            }

            $surah->juz_name = $ayat[0]->juz_name;
            $surah->juz_name_en = juz_name($ayat[0]->juz_id, "en");
            $surah->juz_id = $ayat[0]->juz_id;
            $surah->pages = $ayat->groupBy('page_id');
            return $this->response($surah);
        }

        return $this->errorResponse('Surah not found');

    }


}
