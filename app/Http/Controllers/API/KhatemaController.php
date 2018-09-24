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
        $khatemas['pending'] = fauth()->user()->PendingKhatemas;
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
            return $this->errorResponse(['message' => "User didn't finish last khatema"]);
        }
        $completed_at = $request->has('completed') && $request->get('completed') == 1 ? Carbon::now() : null;
        Khatema::create([
            "user_id"         => fauth()->id(),
            "remaining_pages" => $request->get('remaining_pages'),
            "completed_pages" => $request->get('completed_pages'),
            "completed"       => $request->get('completed', 0),
            "taken_hours"     => $request->get('taken_hours'),
            "remaining_hours" => $request->get('remaining_hours'),
            "created_at"      => Carbon::now(),
            "completed_at"    => $completed_at
        ]);
        return $this->response(['message' => "Khatema created successfully"]);
    }

    /**
     * GET /khatemas/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){

        $khatema = Khatema::where('khatema_id', $request->get('khatema_id'));
        $completed_at = $request->has('completed') && $request->get('completed') == 1 ? Carbon::now() : null;
        $khatema->update([
            "remaining_pages" => $request->get('remaining_pages'),
            "completed_pages" => $request->get('completed_pages'),
            "completed"       => $request->get('completed', 0),
            "taken_hours"     => $request->get('taken_hours'),
            "remaining_hours" => $request->get('remaining_hours'),
            "completed_at"    => $completed_at
        ]);
        return $this->response(['message' => 'Updated Successfully']);
    }
}
