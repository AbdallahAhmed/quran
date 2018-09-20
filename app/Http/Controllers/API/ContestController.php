<?php

namespace App\Http\Controllers\API;

use App\Models\Contest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContestController extends APIController
{

    /**
     * POST contests/create
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'goal' => 'required|min:10',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'expired_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        $contest = Contest::create([
            'name' => $request->get('name'),
            'goal' => $request->get('goal'),
            'start_at' => $request->get('start_at'),
            'expired_at' => $request->get('expired_at'),
            'user_id' => fauth()->id()
        ]);

        $contest->load(['creator','winner']);

        return $this->response(['contest' => $contest,'message'=>['Contest created successfully']]);
    }
}
