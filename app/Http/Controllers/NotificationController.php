<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\UsersTokens;
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
        $this->notificationBuilder->setBody($this->body)->setSound('default');
        $this->dataBuilder = new PayloadDataBuilder();
        $this->dataBuilder->addData([]);;
    }

    public function send($token)
    {
        $tokenToDelete = FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        if ($tokenToDelete->tokensToDelete != null) {
            $device = Token::where('device_token', $token)->first();
            fauth()->user->devices()->detach($device->id);
            $device->delete();
        }
        return;
    }

    public function sendUser($user)
    {
        foreach ($user->devices as $device) {
            $token = $device->device_token;
            $tokenToDelete = FCM::sendTo($token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            if ($tokenToDelete->tokensToDelete != null) {
                $user->devices()->detach($device->id);
                $device->delete();
            }
        }
    }

    public function sendAll()
    {
        $tokens = Token::all();
        foreach ($tokens as $token) {
            $tokenToDelete = FCM::sendTo($token->device_token, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
            if ($tokenToDelete->tokensToDelete != null) {
                $token->delete();
            }
        }
    }

    public function sendGroup($tokens)
    {
        $tokenToDelete = FCM::sendTo($tokens, $this->optionBuilder->build(), $this->notificationBuilder->build(), $this->dataBuilder->build());
        foreach ($tokenToDelete->tokensToDelete as $td){
            $token = Token::where('device_token', $td)->first();
            UsersTokens::where('token_id', $token->id)->delete();
            $token->delete();
        }
    }
}
