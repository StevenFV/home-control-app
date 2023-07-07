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
        (object)$lightingSubscribeMessage = app('lightingSubscribeMessage');

        return [
            'lightingSubscribeMessage' => $lightingSubscribeMessage
        ];
    }

    public function setPublishTopicMessage(): void
    {
//        todosfv make in the same way of getSubscribeTopicMessage()
//        $publishLightingState = app('publishLightingState');
//
//        return [$lightingSubscribeMessage];
    }
}
