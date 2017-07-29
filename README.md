# WebPush
> Web Push library for PHP

WebPush can be used to send web push notification to browser maintaining the standard  [Web Push protocol](https://tools.ietf.org/html/draft-thomson-webpush-protocol-00).
It can be use as a laravel package

## Requirements
* PHP 5.6
  * gmp
  * mbstring
  * curl
  * openssl
  * set up redis server


## Installation
In your composer.json file add this key.

```$xslt
 "repositories": [
        {
            "type": "git",
            "url": "https://github.com/vikashkrkashyap/push-notification-laravel.git"
        }
    ],
```

Within the **require** key of your composer.json file you can add this

```        "push-notification-laravel": "dev-master",```

## Usage

Register service provider to Config/app.php

``        PushNotification\PushNotificationServiceProvider::class
``

Hit the route ```/push-notification/keys``` you will get public and private key. set those public and private keys value in .env file
In your .env file set these two values.

``PUSH_NOTIFICATION_PUBLIC_KEY={publickey}``

``PUSH_NOTIFICATION_PRIVATE_KEY={privatekey}``


Run the command ``php artisan vendor:publish``

In ``Config/pusNotification.php`` file, set the `allowed_origin` (origin website) of the website from where you client side service worker code is available.



Set up your client side of application, Download these files [push_notification.js](https://gist.github.com/vikashkrkashyap/28caed06acc004f7d1331cecc1c93f44) and [sw.js](https://gist.github.com/vikashkrkashyap/244963f0be7f20ec64a9523d1aef3067). keep both file in the same folder and include only ``push_notification.js`` file to your main sciript file of your website, where you want to see the notification.

You need to change two variables inside push_notification.js file. At the top of this file change below two javascript variable values


```var applicationServerPublicKey = {public key}``` [as same as .env value of key PUSH_NOTIFICATION_PUBLIC_KEY];

```var subscriptionUrl = yourserverdomain/push-notification/service-worker"```;


In your class from which you want to send push notification use this contract

````
public function __construct(PushContract $pushNotification)
{
       $this->pushNotification = $pushNotification;
}

````

finally call this function in your class

``
$this->pushNotification->sendPushNotification($title, $body, $icon = null, $link=null, $badge=null, $image=null);
``



