<?php

namespace PushNotification\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use PushNOtification\library\VAPID;
use PushNOtification\library\WebPush;
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
            $swRegistration = $request->input('swRegister');

            // append to the existing data to redis
            $existingData = json_decode(Redis::get($this->redisKey), true);
            if (!is_null($existingData)) {
                json_encode(array_push($existingData, $swRegistration));
            } else {
                $existingData = [];
                $existingData[0] = $swRegistration;
            }

            Redis::set($this->redisKey, $swRegistration);
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
        $httpOrigin = request()->server('HTTP_ORIGIN');
        if(in_array($httpOrigin, $allowedOrigins)){
            header('Access-Control-Allow-Origin:'.$httpOrigin);
            return true;
        }
        else {
            return false;
        }
    }

}
