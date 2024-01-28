<?php

namespace App\Http\Controllers;

use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends MqttController
{
    private const LIGHTING_TOPIC_FILTER = 'light';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (
                $request->user()->can(PermissionRole::ADMIN->value) ||
                $request->user()->can(PermissionName::CONTROL_LIGHTING->value)
            ) {
                return $next($request);
            }

            if (
                $request->user()->can(PermissionName::VIEW_LIGHTING->value) && optional(
                    $request->route()
                )->getActionMethod() !== 'index'
            ) {
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        });
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
