<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\UsersTokens;
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

    public function send($token)
    {
        $this->dataBuilder->addData(['type' => "", 'user_token' => fauth()->user()->api_token]);
        $tokenToDelete = FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        if ($tokenToDelete->tokensToDelete != null) {
            $device = Token::where('device_token', $token)->first();
            $device->delete();
        }
        return;
    }

    public function sendUser($user)
    {
        $this->dataBuilder->addData(['type' => "new_member_join_contest", 'user_token' => $user->api_token]);
        foreach ($user->devices as $device) {
            $token = $device->device_token;
            $tokenToDelete = FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            if ($tokenToDelete->tokensToDelete != null) {
                $device->delete();
            }
        }
    }

    public function sendAll($users)
    {
        $api_tokens = array();
        foreach ($users as $user){
            $api_tokens[] = $user->api_token;
        }
        $this->dataBuilder->addData(['type' => "contest_winner", 'users_tokens' => $api_tokens]);
        $tokens = Token::all();
        foreach ($tokens as $token) {
            $tokenToDelete = FCM::sendTo($token->device_token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            dd($tokenToDelete);
            if ($tokenToDelete->tokensToDelete != null) {
               // $token->delete();
            }
        }
    }

    public function sendGroup($tokens)
    {
        $tokenToDelete = FCM::sendTo($tokens, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        foreach ($tokenToDelete->tokensToDelete as $td){
            Token::where('device_token', $td)->delete();
        }
    }
}
