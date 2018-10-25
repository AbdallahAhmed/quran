<?php

namespace App\Http\Controllers\API;

use App\Models\Ayat;
use App\Models\UserAyat;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookmarkController extends APIController
{

    /**
     * GET /bookmarks
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response((fauth()->user()->ayat));
    }

    /**
     * POST /bookmarks/create
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $ayah_id = $request->get('ayah_id');
        fauth()->user()->ayat()->syncWithoutDetaching($ayah_id);
        return $this->response("Saved Successfully");
    }

    /**
     * POST /bookmarks/delete
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $ayah_id = $request->get('ayah_id');
        fauth()->user()->ayat()->detach($ayah_id);
        return $this->response("Deleted Successfully");
    }

    /**
     * POST /bookmarks/clear
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        fauth()->user()->ayat()->detach();
        return $this->response("Deleted Successfully");
    }
}
