<?php
namespace PushNotification\Contracts;

use Illuminate\Http\Request;

interface PushContract
{
    /**
     * @param string $title
     * @param string  $body
     * @param null $icon
     * @param null $link
     * @param null $badge
     * @param null $image
     * @return array
     */
    public function sendPushNotification($title, $body, $icon = null, $link=null, $badge=null, $image=null);
}

?>