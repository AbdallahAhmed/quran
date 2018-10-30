<?php

namespace App\Http\Controllers\API;

use App\Models\Contest;
use App\Models\ContestMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if (count(fauth()->user()->contest) > 0) {
            return $this->errorResponse(['You have to get out from current contest']);
        }

        $contest = Contest::create([
            'name' => $request->get('name'),
            'goal' => $request->get('goal'),
            'start_at' => $request->get('start_at'),
            'expired_at' => $request->get('expired_at'),
            'user_id' => fauth()->id()
        ]);

        $contest->load(['creator', 'winner']);

        ContestMember::create([
            'contest_id' => $contest->id,
            'member_id' => fauth()->id()
        ]);

        return $this->response($contest);
    }


    /**
     * POST /contests/join
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function join(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'contest_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        $contest = Contest::where('id', $request->get('contest_id'))->first();

        if ($contest->is_expired) {
            return $this->errorResponse(['Ooh  you can\'t join in this contest because it is expired']);
        }

        if (count(fauth()->user()->contest) > 0) {
            ContestMember::where(['contest_id' => fauth()->user()->contest()->first()->id, 'member_id' => fauth()->id()])->delete();
            //return $this->errorResponse(['You have to get out from current contest']);
        }

        ContestMember::create([
            'contest_id' => $request->get('contest_id'),
            'member_id' => fauth()->id()
        ]);

        return $this->response('You join to contest successfully');
    }


    /**
     * POST /contests/leave
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leave(Request $request)
    {
        $contest = Contest::find($request->get('contest_id'));
        if ($contest->is_expired) {
            return $this->errorResponse(['This contest expires can\'t leave it.']);
        }


        $result = ContestMember::where(['contest_id' => $request->get('contest_id'), 'member_id' => fauth()->id()])->delete();


        if ($result == 0) {
            return $this->errorResponse(['You are not join in this contests']);
        }

        return $this->response('Leaving successfully');
    }


    /**
     * GET /contests
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 8);
        $offset = $request->get('offset', 0);

        $contests = [];
        if ($request->filled('status')) {
            $status = explode('|', $request->get('status'));
            foreach ($status as $singleStatus) {
                $query = Contest::with(['creator', 'winner'])->take($limit)->offset($offset);
                switch ($singleStatus) {
                    case 'coming';
                        $query = $query->coming();
                        break;
                    case 'opened';
                        $query = $query->opened();
                        break;
                    case 'expired';
                        $query = $query->expired();
                        break;
                    case 'all';
                        $query = $query->where('expired_at', '>', Carbon::now());
                        break;
                    case 'joined';
                        $query = $query->whereHas('members', function ($query) {
                            $query->where('users.id', fauth()->id());
                        });
                        break;
                    case 'current';
                        $query = $query->whereHas('members', function ($query) {
                            $query->where('users.id', fauth()->id());
                        })->where('expired_at', '<', Carbon::now());
                        $contests['current'] = fauth()->user()->contest;
                        break;
                }
                $contests[$singleStatus] = $query->get();
            }
        } else {
            $user = fauth()->user();
            if ($user && count($user->contest) > 0) {
                $contests['current'] = $user->contest;
            }
            $contests = Contest::with(['creator', 'winner'])->take($limit)->offset($offset)->get();
        }
        return $this->response($contests);
    }


    /**
     * GET /contests/details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(Request $request)
    {
        $contest = Contest::with(['creator', 'winner', 'members'])->where('id', $request->query('contest_id'))->first();
        if (!$contest) {
            return $this->errorResponse(['Contest not founded'], 404);
        }
        if(fauth()->user() && count(fauth()->user()->contest) > 0){
            $contest['current'] = fauth()->user()->contest;
        }

        return $this->response($contest);
    }


    /**
     * GET /contests/current
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {

        $contest = fauth()->user()->contest;
        if (count($contest) > 0) {
            $contest[0]->load(['creator', 'winner']);
            return $this->response($contest[0]);
        }
        return $this->errorResponse(['You are not joined to any opened Contests']);
    }
}
