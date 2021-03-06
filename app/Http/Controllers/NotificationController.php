<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Token;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Symfony\Component\Console\Exception\InvalidOptionException;

class NotificationController extends Controller
{
    public $title = "";

    public $body = "";

    public $optionBuilder;

    public $notificationBuilder;

    public $dataBuilder;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
        $this->optionBuilder = new OptionsBuilder();
        try {
            $this->optionBuilder->setTimeToLive(60 * 20);
        } catch (InvalidOptionException $e) {
        }
        $this->notificationBuilder = new PayloadNotificationBuilder($this->title);
        $this->notificationBuilder->setBody($this->body)->setSound('default')->setIcon('default');
        $this->dataBuilder = new PayloadDataBuilder();
        $this->dataBuilder->addData([]);
    }

    public function send($token, $array)
    {
        if($array['type'] == "contest_reminder" || $array['type'] == "contest_unread_reminder")
            $this->dataBuilder->addData(['type' => $array['type'], "contest_id" => $array['contest_id']]);
        else
            $this->dataBuilder->addData(['type' => $array['type']]);
        FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        return;
    }

    public function sendUser($user, $array)
    {

        $this->dataBuilder->addData($array);//['type' => $array['type'], 'route' => 'contest', "contest_id" => $array['contest_id']]);
        foreach ($user->devices as $device) {
            $token = $device->device_token;
            $tokenToDelete = FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            if ($tokenToDelete->tokensToDelete != null) {
                $device->delete();
            }
        }
    }

    public function sendAll($users, $array)
    {
        $api_tokens = array();
        foreach ($users as $user){
            $api_tokens[] = $user->api_token;
        }
        $this->dataBuilder->addData(['type' => $array['type'], 'route' => 'contest']);
        $tokens = Token::all();
        foreach ($tokens as $token) {
            app()->setLocale($token->user->lang);
            $this->title = trans('app.new_contest');
            $this->body = str_replace(":contest", $array['contest_name'], trans('app.new_contest_content'));
            $tokenToDelete = FCM::sendTo($token->device_token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            if ($tokenToDelete->tokensToDelete != null) {
               $token->delete();
            }
        }
    }

    public function sendGroup($tokens, $type)
    {
        $this->dataBuilder->addData(['type' => $type, 'route' => 'contest']);
        $tokenToDelete = FCM::sendTo($tokens, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        foreach ($tokenToDelete->tokensToDelete as $td){
            Token::where('device_token', $td)->delete();
        }
    }
}
