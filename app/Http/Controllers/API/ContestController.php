<?php

namespace App\Http\Controllers\API;

use App\Events\ContestCreated;
use App\Events\ContestJoin;
use App\Events\ContestWinner;
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
        //app()->setLocale($request->get('lang'));
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
            /*'goal' => 'required|min:10',*/
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
            'user_id' => fauth()->id(),
            'juz_from' => $request->get('juz_from'),
            'juz_to' => $request->get('juz_to'),
            'type' => $request->get('type')
        ]);

        event(new ContestCreated($contest));
        if($contest->type == "surah"){
            $contest->surat()->sync($request->get("surat"));
        }
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
       // app()->setLocale($request->get('lang'));


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

        if ($contest->winner_id == 0) {
            $contest->winner_id = fauth()->user()->id;
            $contest->save();
        }
        ContestMember::create([
            'contest_id' => $request->get('contest_id'),
            'member_id' => fauth()->id()
        ]);
        event(new ContestJoin(fauth()->user(), $contest->user_id, $contest->id));

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
                        $query = $query->coming()->orderBy('created_at', 'DESC');
                        break;
                    case 'opened';
                        $query = $query->opened()->orderBy('created_at', 'DESC');
                        break;
                    case 'expired';
                        $query = $query->expired()->orderBy('created_at', 'DESC');
                        break;
                    case 'all';
                        $query = $query->where('expired_at', '>', Carbon::now())->orderBy('created_at', 'DESC');
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
                        break;
                }
                $contests[$singleStatus] = $query->get();
                $contests['current'] = fauth()->user() ? fauth()->user()->contest->load('creator') : null;
            }
        } else {
            $user = fauth()->user();
            if ($user && count($user->contest) > 0) {
                $contests['current'] = $user->contest->load('creator');
            }
            $contests = Contest::with(['creator', 'winner'])->take($limit)->offset($offset)->orderBy('created_at')->get();
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
        if($contest->type == "surah")
            $contest->load('surat');
        if (!$contest) {
            return $this->errorResponse(['Contest not founded'], 404);
        }
        if (fauth()->user() && count(fauth()->user()->contest) > 0) {
            $contest['current'] = fauth()->user()->contest->load('creator');
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


    /**
     * GET /contests/updates
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updates(Request $request)
    {

        $contest = fauth()->user()->contest;
        if (count($contest) > 0) {
            $contest = $contest[0];
            $pages = json_decode($contest->pivot->pages ? $contest->pivot->pages : '[]');

            if ($request->filled('page_id')) {
                $pages[] = $request->get('page_id');
                $pages = array_unique($pages);
            }

            $pages = $pages ? $pages : [];
            $new_pages = $request->get('pages', []) ? json_decode($request->get('pages', [])) : [];
            if ($request->filled('pages')) {
                $pages = array_unique(array_merge($pages, $new_pages));
            }

            $contest->pivot->pages = json_encode($pages);

            $contest->pivot->save();


            $this->checkWinner($pages, $contest);

            return 'done';
        }

        return 'no contest to update';
    }

    /**
     * GET /contests/updates
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkWinner($pages, $contest)
    {
        $winner_pages = $contest->winner_id != 0 ?  ContestMember::where([
                ['contest_id', $contest->id],
                ['member_id', $contest->winner_id]
            ]
        )->first()->pages : "[]";

        $winner_pages = json_decode($winner_pages) ? json_decode($winner_pages) : [];

        if (count($winner_pages) < count($pages)) {
            $juz_from = (int)$contest->juz_from;
            $juz_to = (int)$contest->juz_to;
            $contest_pages = array();
            if($contest->type == "juz"){
               /* $juz_pages = json_decode(file_get_contents(public_path('api/juz_pages.json')));
                foreach ($juz_pages as $key => $value) {
                    if ($key >= $juz_from && $key <= $juz_to) {
                        $contest_pages = array_merge($contest_pages, $value);
                    }
                }*/
                $contest_pages = get_contest_pages($juz_from, $juz_to);//array_unique($contest_pages);
            }else{
                $contest_pages = array_values(array_unique($contest->pages));
            }
            sort($contest_pages);
            sort($pages);

            if ($contest_pages == $pages) {
                Contest::where('id', $contest->id)->update([
                    'winner_id' => fauth()->user()->id,
                    'closed_to_winner' => 1
                ]);
                event(new ContestWinner($contest));
            }else{
                Contest::where('id', $contest->id)->update([
                    'winner_id' => fauth()->user()->id,
                ]);
            }
        }

    }
}
