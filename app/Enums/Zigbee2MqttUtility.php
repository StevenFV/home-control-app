<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

enum Zigbee2MqttUtility: string
{
    use InvokableCases;


    case BASE_TOPIC = 'zigbee2mqtt/';
    case GET = '/get';
    case LIGHTING_TOPIC_FILTER = 'light';
    case SET = '/set';
    case STATE_DEVICE_PAYLOAD = '{"state": ""}';
    case TOGGLE = 'TOGGLE';
    case ZIGBEE2MQTT_BRIDGE_DEVICES = 'zigbee2mqtt/bridge/devices';
}
