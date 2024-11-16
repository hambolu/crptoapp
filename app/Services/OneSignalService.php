<?php

namespace App\Services;

use Berkayk\OneSignal\OneSignalClient;

class OneSignalService
{
    protected $client;

    public function __construct()
    {
        $this->client = new OneSignalClient(
            config('onesignal.app_id'),
            config('onesignal.rest_api_key'),
            config('onesignal.user_auth_key')
        );
    }

    public function sendNotificationToUser($userId, $title, $message, $url = null, $data = [])
    {
        return $this->client->sendNotificationToExternalUser(
            $message,
            $userId,
            $url,
            $data,
            $title
        );
    }


    // public function sendNotificationToExternalUser($player_id, $title, $message, $url = null, $data = [])
    // {
    //     return $this->client->sendNotificationToExternalUser(
    //         $message,
    //         $player_id,
    //         $url,
    //         $data,
    //         $title
    //     );
    // }

    public function sendNotificationToAll($title, $message, $url = null, $data = [])
    {
        return $this->client->sendNotificationToAll(
            $message,
            $url,
            $data,
            $title
        );
    }
}
