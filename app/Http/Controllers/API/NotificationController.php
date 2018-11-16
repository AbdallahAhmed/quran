<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Symfony\Component\Console\Exception\InvalidOptionException;

class NotificationController extends Controller
{
    public function send(){

        $user = fauth()->user();
        dd($user->devices);
        $optionBuilder = new OptionsBuilder();
        try {
            $optionBuilder->setTimeToLive(60 * 20);
        } catch (InvalidOptionException $e) {
        }

        $notificationBuilder = new PayloadNotificationBuilder('فيديو جديد');
        $notificationBuilder->setBody('new new')
            ->setSound('default')->setClickAction();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['page' => 'video']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = "cED6TT3M_9Q:APA91bETsbYW9kkid5HNBhV_ES6gbDJ7xmAvs2R4q-q9gvQugzyn_ltwJ-vbliPFSsU77N-RyxDE_FeWogXCP-tFt6hTgr4H5i6-KtZq8sVPUSzvBC-Xp9Tzu8h_nzhr_ldonfAG7k3f  ";
        dd(FCM::sendTo($token, $option, $notification, $data));
        return;
    }
}
