<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Facades\MQTT;

class MqttController extends Controller
{
	protected const TOGGLE = 'TOGGLE';
	private const BASE_TOPIC = 'zigbee2mqtt/';
	private const DEVICES_CONNECTED_T0_BRIDGE_TOPIC = 'zigbee2mqtt/bridge/devices';
	private const PUBLISH_MESSAGE_TO_DEVICE = '/set';
	private const READ_VALUES_FROM_DEVICE = '/get';
	private const STATE_DEVICE_PAYLOAD = '{"state": ""}';

	protected function createSetTopic(string $topic): string
	{
		return $topic . self::PUBLISH_MESSAGE_TO_DEVICE;
	}

	protected function createStateJson(string $state): bool|string
	{
		return json_encode(['state' => $state]);
	}

	private function setConnection(): MqttClient
	{
		return MQTT::connection('default');
	}

	protected function fetchFriendlyNames(): Collection
	{
		try {
			$mqtt = $this->setConnection();
			$mqtt->subscribe(
				self::DEVICES_CONNECTED_T0_BRIDGE_TOPIC,
				function (string $topic, string $message) use ($mqtt, &$friendlyNames) {
					$deviceSubscribe = json_decode($message);
					$friendlyNames = collect($deviceSubscribe)->pluck('friendly_name')->except([0]);

					$mqtt->interrupt();
				},
				1
			);
			$mqtt->loop(false, true);

			return $friendlyNames;
		} catch (MqttClientException $e) {
			return ['error' => $e->getMessage()]; // todosfv
		}
	}

	protected function fetchSubscribeTopicMessage(string $friendlyName): array
	{
		try {
			$mqtt = $this->setConnection();
			$mqtt->subscribe(
				self::BASE_TOPIC . $friendlyName,
				function (string $topic, string $message) use ($mqtt, &$callback) {
					$callback = [
						'topic' => $topic,
						'message' => json_decode($message)
					];
					$mqtt->interrupt();
				},
				0
			);
			$mqtt->publish(
				self::BASE_TOPIC . $friendlyName . self::READ_VALUES_FROM_DEVICE,
				self::STATE_DEVICE_PAYLOAD
			);
			$mqtt->loop();

			return $callback;
		} catch (MqttClientException $e) {
			dd('errorGetSubscribeTopicMessage' . $e);
			return ['error' => $e->getMessage()];
		}
	}

	protected function publishMessage(string $topic, string $message): void
	{
		try {
			$mqtt = $this->setConnection();
			$mqtt->publish($topic, $message);
			$mqtt->disconnect();
			Session::flash('success', 'Success publish message!');
		} catch (MqttClientException $e) {
			Session::flash('error', $e->getMessage());
		}
	}
}
