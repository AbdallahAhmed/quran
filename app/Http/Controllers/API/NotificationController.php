<?php

namespace App\Http\Controllers\API;

use App\Not;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends APIController
{
    public function index(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $notifications = Not::where('user_id', fauth()->user()->id)
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->response($notifications);
    }

}
