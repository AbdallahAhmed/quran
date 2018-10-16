<?php

namespace App\Http\Controllers\API;

use App\Models\Khatema;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class KhatemaController extends APIController
{
    /**
     * GET /khatemas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $khatemas = array();
        $khatemas['completed'] = fauth()->user()->CompletedKhatemas;
        $khatemas['pending'] = fauth()->user()->PendingKhatema;
        if(count($khatemas['completed']) == 0 && count($khatemas['pending']) == 0)
            return $this->errorResponse("You didn't start any Khatema");
        return $this->response($khatemas);
    }

    /**
     * GET /khatemas/create
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $khatema = Khatema::where([
           ['user_id', fauth()->id()],
           ['completed', 0]
        ])->first();
        if($khatema){
            return $this->errorResponse("User didn't finish last khatema");
        }

        $completed_at = $request->has('completed') && $request->get('completed') == 1 ? Carbon::now() : null;
        $khatema->remaining_pages = $request->get('remaining_pages');
        $khatema->completed_pages = $request->get('completed_pages');
        $khatema->completed = $request->get('completed', 0);
        $khatema->pages = $request->get('pages');
        $khatema->taken_hours = $request->get('taken_hours', 0);
        $khatema->completed_at = $completed_at;
        $khatema->completed = $request->get('completed', 0);
        $khatema->save();

        return $this->response("Khatema created successfully");
    }

    /**
     * GET /khatemas/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){

        $khatema = $request->get('id') ? Khatema::find($request->get('id')) : new Khatema();

        $completed_at = $request->has('completed') && $request->get('completed') == 1 ? Carbon::now() : null;
        $khatema->remaining_pages = $request->get('remaining_pages');
        $khatema->completed_pages = $request->get('completed_pages');
        $khatema->completed = $request->get('completed', 0);
        $khatema->pages = $request->get('pages');
        $khatema->taken_hours = $request->get('taken_hours', 0);
        $khatema->completed_at = $completed_at;
        $khatema->completed = $request->get('completed', 0);
        $khatema->save();

        return $this->response('Khatema Updated Successfully');
    }
}
