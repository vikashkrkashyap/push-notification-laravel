<?php

namespace PushNotification\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use PushNotification\library\VAPID;
use PushNotification\library\WebPush;
use PushNotification\Contracts\PushContract;
use PushNotification\PushUtilities;

class PushNotificationController extends Controller implements PushContract
{
    /**
     * Display a listing of the resource.
     *
     */

    private $redisKey;


    public function __construct()
    {
        config(['database.redis.push-notification' => [
            'host' => config('pushNotification.redis.host'),
            'password' => config('pushNotification.redis.password'),
            'port' => config('pushNotification.redis.port'),
            'database' => config('pushNotification.redis.database')
        ]]);

        Redis::connection('push-notification');

        $this->redisKey = (new PushUtilities())->getRedisUniqueKey();
    }

    public function registerServiceWorker(Request $request)
    {


        if ($this->allowedCrossOriginRequest()) {
            $swRegistration = json_decode($request->input('swRegister'), true);

            // append to the existing data to redis
            $existingData = is_null(Redis::get($this->redisKey)) ? [] : json_decode(Redis::get($this->redisKey), true);
            if(!in_array($swRegistration['keys']['auth'], $existingData)){
                $existingData[$swRegistration['keys']['auth']] =  $swRegistration;
            }

            Redis::set($this->redisKey, json_encode($existingData));
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getKeys()
    {
        return VAPID::createVapidKeys();
    }

    public function sendPushNotification($title, $body, $icon = null, $link = null, $badge = null, $image = null)
    {
        $payload = [
            "title" => $title,
            "body" => $body
        ];

        if (isset($icon)) {
            $payload['icon'] = $title;
        }

        if (isset($link)) {
            $payload['link'] = $link;
        }

        if (isset($badge)) {
            $payload['badge'] = $badge;
        }

        if (isset($image)) {
            $payload['image'] = $image;
        }

        $payload = json_encode($payload);

        // fetch data from redis
        $swRegistrations = json_decode(Redis::get($this->redisKey), true);
        Log::info($swRegistrations);

        try {
            foreach ($swRegistrations as $swRegistration) {
                $endPoints = $swRegistration['endpoint'];
                $authVAPID = [
                    'VAPID' => [
                        'subject' => 'mailto:vikashkrkashyap@gmail.com',
                        'publicKey' => config('pushNotification.publicKey'),
                        'privateKey' => config('pushNotification.privateKey')
                    ]
                ];

                $webPush = new WebPush($authVAPID);
                $webPush->sendNotification($endPoints, $payload, $swRegistration['keys']['p256dh'], $swRegistration['keys']['auth'], true, [], $authVAPID);
                unset($swRegistrations[0]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return [
                "status" => "Failed",
                "message" => $error,
                "code" => 500
            ];
        }

        return [
            "status" =>  "Success",
            "message" => "Push notification sent successfully",
            "code"  => 200
        ];
    }

    private function allowedCrossOriginRequest()
    {
        $allowedOrigins = config('pushNotification.allowedOrigins');
        $httpOrigin = $this->getHttpOrigin();

        if(in_array($httpOrigin, $allowedOrigins)){
            header('Access-Control-Allow-Origin:'.$httpOrigin);
            return true;
        }
        else {
            return false;
        }
    }

    private function getHttpOrigin()
    {
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }
        else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $origin = $_SERVER['HTTP_REFERER'];
        } else {
            $origin = $_SERVER['REMOTE_ADDR'];
        }

        return $this->url($origin);
    }

    private function url($url) {
        $result = parse_url($url);
        return $result['scheme']."://".$result['host'];
    }

}
