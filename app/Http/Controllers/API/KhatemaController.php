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
    public function index()
    {
        $khatemas = array();
        $khatemas['completed'] = fauth()->user()->CompletedKhatemas;
        $khatemas['pending'] = fauth()->user()->PendingKhatema()->first();
        if (count($khatemas['completed']) == 0 && !($khatemas['pending']))
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
        if ($khatema) {
            return $this->errorResponse("User didn't finish last khatema");
        }

        $completed_at = $request->has('completed') && $request->get('completed') == 1 ? Carbon::now() : null;

        $khatema->user_id = fauth()->id();
        $khatema->completed_pages = $request->get('completed_pages', 0);
        $khatema->completed = $request->get('completed', 0);
        $khatema->pages = json_encode($request->get('pages', []));
        $khatema->taken_hours = $request->get('taken_hours', 0);
        $khatema->remaining_hours = $request->get('remaining_hours', 500);
        $khatema->completed_at = $completed_at;

        return $this->response("Khatema created successfully");
    }

    /**
     * GET /khatemas/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $user = fauth()->user();

        $completed_at = $request->filled('completed') && $request->get('completed') == 1 ? Carbon::now() : null;

        $khatema = $user->PendingKhatema()->first();
        $khatema = $khatema ? $khatema : new Khatema();
        if ($request->filled('completed_pages')) {
            $khatema->completed_pages = $request->get('completed_pages', 0);
        }

        if ($request->filled('completed')) {
            $khatema->completed = $request->get('completed', 0);
        }


        $khatema->pages = json_encode($request->get('pages', []));

        if ($request->filled('taken_hours')) {
            $khatema->taken_hours = $request->get('taken_hours', 0);
        }


        if ($request->filled('remaining_hours')) {
            $khatema->remaining_hours = $request->get('remaining_hours', 500);
        }


        if ($request->filled('completed_at')) {
            $khatema->completed_at = $completed_at;

        }
        $khatema->user_id = fauth()->id();

        $khatema->save();

        return $this->response($khatema);
    }
}
