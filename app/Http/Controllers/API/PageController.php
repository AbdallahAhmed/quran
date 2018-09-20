<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\Page;
use App\Models\Surat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends APIController
{

    /**
     * GET /page
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {

        $id = $request->get('page_id');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 1000);

        $page = Page::find($id);
        if ($page) {
            $ayahs = $page->ayat()
                ->take($limit)
                ->offset($offset)
                ->get();
            foreach ($ayahs as $key => $ayah) {
                $ayahs[$key]['surah'] = Surat::find($ayah->surat_id);
            }
            $page->load('surat');
            $page->ayahs = $ayahs;
        return $this->response($page, true, 200);
        } else {
            return $this->errorResponse(['Page not found'], 400);
        }
    }

}
