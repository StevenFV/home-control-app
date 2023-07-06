<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

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
        (string)$lightingSubscribeTopic = app('lightingSubscribeTopic');
        (object)$lightingSubscribeMessage = app('lightingSubscribeMessage');

        return [
            'lightingSubscribeTopic' => $lightingSubscribeTopic,
            'lightingSubscribeMessage' => $lightingSubscribeMessage
        ];
    }

    public function setPublishTopicMessage(): void
    {
//        todosfv make in the same way of getSubscribeTopicMessage()
//        $publishLightingTopic = app('lightingSubscribeTopic');
//        $publishLightingState = app('publishLightingState');
//
//        return [$lightingSubscribeTopic, $lightingSubscribeMessage];
    }
}
