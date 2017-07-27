<?php
namespace PushNotification;
/**
 * Created by PhpStorm.
 * User: vikash.k
 * Date: 7/27/17
 * Time: 11:54 PM
 */
class PushUtilities
{
    public function getRedisUniqueKey()
    {
        $key = config('pushNotification.redis.key');

        if(auth()->user()){
            $key .= auth()->user()->id;
        }

        return $key;
    }
}