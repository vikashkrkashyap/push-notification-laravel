<?php
namespace PushNotification\Contracts;

use Illuminate\Http\Request;

interface PushContract
{
    public function registerServiceWorker(Request $request);
}

?>