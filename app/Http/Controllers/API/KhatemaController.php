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
        $khatema->completed = $request->get('completed', 0);
        $khatema->pages = json_encode($request->get('pages', []));
        $khatema->remaining_pages = 604 - count(json_decode($request->get('pages', null)));
        $khatema->taken_hours = $request->get('taken_hours', 0);
        $khatema->remaining_hours = $request->get('remaining_hours', 500);
        $khatema->completed_at = $completed_at;

        return $this->response("Khatema created successfully");
    }

    /**
     * POST /khatemas/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $user = fauth()->user();

        $khatema = $user->PendingKhatema()->first();

        $khatema = $khatema ? $khatema : new Khatema();

        $pages = (json_decode($khatema->pages ? $khatema->pages : "[]", true));


        if ($request->filled('page_id')) {
            $pages[] = ['page_id' => $request->get('page_id'), 'time' => $request->get('time')];

            $pages = collect($pages)->unique('page_id')->toArray();
        }

        $pages = $pages ? $pages : [];

        $new_pages = $request->get('pages', []) ? json_decode($request->get('pages', "[]"), true) : [];

        if ($request->filled('pages')) {
            $pages = (array_merge($pages, $new_pages));
            $pages = collect($pages)->unique('page_id')->toArray();
        }

        if ($request->filled('completed') || count($pages) >= 604) {
            $khatema->completed = $request->get('completed', 1);
            $khatema->completed_at = Carbon::now();
        }

        $khatema->pages = json_encode(array_values($pages));
        $khatema->remaining_pages = 604 - count($pages);

        $khatema->user_id = fauth()->id();

        $khatema->save();

        return $this->response($khatema);
    }
}
