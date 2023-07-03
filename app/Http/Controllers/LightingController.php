<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;

class LightingController extends Controller
{
    public function __construct()
    {
        // todosfv controllers __construct to test
        $this->middleware('auth');
        $this->middleware('permission:read',
            ['only' => ['data']]
        );
        $this->middleware('permission:edit',
            ['only' => ['index']]
        );
    }

    public function index()
    {
        $subscribeTopicMessage = $this->getSubscribeTopicMessage();

        return Inertia::render('Lighting/index', [
            'subscribeTopicMessage' => $subscribeTopicMessage
        ]);
    }

    public function getSubscribeTopicMessage(): array
    {
        (string)$subscribeLightingTopic = app('subscribeLightingTopic');
        (object)$subscribeLightingMessage = app('subscribeLightingMessage');

        return [
            'subscribeLightingTopic' => $subscribeLightingTopic,
            'subscribeLightingMessage' => $subscribeLightingMessage
        ];
    }

    public function setPublishTopicMessage(): void
    {
//        todosfv make in the same way of getSubscribeTopicMessage()
//        $publishLightingTopic = app('subscribeLightingTopic');
//        $publishLightingState = app('publishLightingState');
//
//        return [$subscribeLightingTopic, $subscribeLightingMessage];
    }
}
