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
    public function index(){
        $bookmarks = UserAyat::where('user_id', fauth()->id())->get();
        $ayat = array();
        foreach ($bookmarks as $key => $bookmark){
            $ayat[] = Ayat::find($bookmark->ayah_id);
            $ayat[$key]->load('surah');
        }
        return $this->response($ayat);
    }

    /**
     * POST /bookmarks/create
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request){
        $ayah_id = $request->get('ayah_id');
        $bookmark = UserAyat::where([
            ['user_id', fauth()->id()],
            ['ayah_id', $ayah_id]
        ])->first();
        if($bookmark){
            return $this->response("Already Exist");
        }else{
            UserAyat::create([
               'user_id' => fauth()->id(),
               'ayah_id' => $ayah_id
            ]);
            return $this->response("Saved Successfully");
        }
    }

    /**
     * POST /bookmarks/delete
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        $ayah_id = $request->get('ayah_id');
        UserAyat::where([
            ['user_id', fauth()->id()],
            ['ayah_id', $ayah_id]
        ])->delete();
        return $this->response("Deleted Successfully");
    }
    /**
     * POST /bookmarks/clear
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(){
        UserAyat::where('user_id', fauth()->id())->delete();
        return $this->response("Deleted Successfully");
    }
}
