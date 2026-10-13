<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';

$config = require __DIR__ . '/config.php';
$mqttConfig = $config['mqtt'] ?? [];

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;

$mqttServer = $mqttConfig['host'] ?? '127.0.0.1';
$mqttPort   = (int) ($mqttConfig['port'] ?? 1883);
$mqttTopic  = $mqttConfig['topic'] ?? 'pzem/data';
$clientId   = 'PHP_MQTT_Listener';

$connectionSettings = (new ConnectionSettings())
    ->setKeepAliveInterval(60)
    ->setUseTls(false)
    ->setConnectTimeout(10)
    ->setSocketTimeout(10);

function startSubscriber($mqttServer, $mqttPort, $clientId, $connectionSettings, $conn, $mqttTopic)
{
    try {
        $mqtt = new MqttClient($mqttServer, $mqttPort, $clientId);
        $mqtt->connect($connectionSettings, true);

        echo "📡 Connected to MQTT broker at $mqttServer:$mqttPort\n";

        $mqtt->subscribe($mqttTopic, function ($topic, $message) use ($conn) {
            echo "[" . date('H:i:s') . "] Message: $message\n";
            $data = json_decode($message, true);
            if (!$data) {
                echo "⚠️ Invalid JSON.\n";
                return;
            }

            // Gunakan prepared statement (lebih cepat dan aman)
            $stmt = $conn->prepare("
                INSERT INTO pzem_data (voltage, current, power, energy, frequency, pf)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "dddddd",
                $data['voltage'],
                $data['current'],
                $data['power'],
                $data['energy'],
                $data['frequency'],
                $data['pf']
            );
            $stmt->execute();
            $stmt->close();

            echo "✅ Data inserted\n";
        }, 0);

        while (true) {
            $mqtt->loop(true, 1000); // loop tiap 1 detik
        }

        $mqtt->disconnect();
    } catch (MqttClientException $e) {
        echo "❌ MQTT Error: {$e->getMessage()}\n";
        sleep(5); // tunggu sebentar lalu reconnect
        startSubscriber($mqttServer, $mqttPort, $clientId, $connectionSettings, $conn, $mqttTopic);
    } catch (Throwable $t) {
        echo "💥 Unexpected error: {$t->getMessage()}\n";
        sleep(5);
        startSubscriber($mqttServer, $mqttPort, $clientId, $connectionSettings, $conn, $mqttTopic);
    }
}

startSubscriber($mqttServer, $mqttPort, $clientId, $connectionSettings, $conn, $mqttTopic);
