<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    static public function notify($title, $body, $device_key){
        $url = "https://fcm.googleapis.com/fcm/send";

        $serverKey = env("FCM_SERVER_KEY", 'sync');


        $dataArr=[
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "status" => "done",
        ];

        $data = [
            "registration_ids" => [$device_key],
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default",
            ],
            "data" => $dataArr,
            "priority" => "high",
        ];

        $encodedData = json_encode($data);

        $headers = [
            "Authorization:key=" . $serverKey,
            "Content-Type: application/json",
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disable ssl certificate support temporary
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        $result = curl_exec($ch);

        if($result === FALSE){
            return [
                'message' => 'failed',
                'r' => $result,
                'success' => false,
            ];
        }

        else {
            return [
                'message' => 'success',
                'r' => $result,
                'success' => true,
            ];
        }
    }

    public function notifyApp(Request $request){
        // return [
        //     'title'=> $request->title,
        // ];
        // 
        return $this->notify($request->title, $request->body, $request->key);
    }
}
