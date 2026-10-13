#pragma once

// Copy this file to config.h and edit the values for your own network.
// config.h is intentionally excluded from version control.

// IP address or hostname of the MQTT broker reachable from the ESP32.
#define MQTT_SERVER "192.168.1.100"
#define MQTT_PORT 1883

// Wi-FiManager setup access point. Change the password to a unique value.
#define WIFI_PORTAL_SSID "EnergyMonitor-Setup"
#define WIFI_PORTAL_PASSWORD "change-this-setup-password"
