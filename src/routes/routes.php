<?php
// define end points here

Route::group(['namespace' => 'Vikash\PushNotification\Controllers','prefix' =>'push-notification'], function (){
    Route::post('service-worker', 'PushNotificationController@registerServiceWorker');
    Route::get('keys' ,'PushNotificationController@getKeys');
});
?>

