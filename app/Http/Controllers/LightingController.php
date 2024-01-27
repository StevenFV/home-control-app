<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends MqttController
{
    private const LIGHTING_TOPIC_FILTER = 'light';

    public function __construct()
    {
        // todosfv controllers __construct to test
        $this->middleware('auth');
        // todosfv configure permissions
//        $this->middleware(
//            'permission:admin',
//            ['only' => ['data']]
//        );
//        $this->middleware(
//            'permission:user',
//            ['only' => ['index']]
//        );
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'subscribeTopicMessage' => $this->fetchLightingTopicMessage()
        ]);
    }

    private function fetchLightingTopicMessage(): array
    {
        $lightingFriendlyNames = $this->fetchLightingFriendlyNames();
        $lightingTopicsMessages = [];

        foreach ($lightingFriendlyNames as $lightingFriendlyName) {
            $lightingTopicsMessages[] = $this->fetchSubscribeTopicMessage($lightingFriendlyName);
        }

        return $lightingTopicsMessages;
    }

    private function fetchLightingFriendlyNames(): array
    {
        return $this->fetchFriendlyNames()->filter(function ($name) {
            return str_starts_with($name, self::LIGHTING_TOPIC_FILTER);
        })->toArray();
    }

    public function publishLightingToggle(Request $request): void
    {
        $topic = $request['topic'];
        $this->publishMessage($this->createSetTopic($topic), $this->createStateJson(MqttController::TOGGLE));
    }
}
