<?php

namespace App\Http\Controllers\API;

use App\Models\Notificate;
use App\Not;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends APIController
{
    public function index(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $type = $request->get('type', "");

        $query = Notificate::where('user_id', fauth()->user()->id)
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'DESC');
        $query = $type ? $query->where("type", $type) : $query;

        $notifications = $query->get();
        return $this->response($notifications);
    }



}
