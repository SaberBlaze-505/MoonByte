# Energy Monitor IoT

An IoT-based electrical energy monitoring prototype built with ESP32, PZEM-004T, MQTT, PHP, and MySQL.

## Overview

The system collects electrical measurements, transmits them over a local network, stores received data in a database, and presents the measurements through a web dashboard. It combines embedded systems, electrical measurement, network communication, backend development, and database management.

## Features

- Reads voltage, current, power, energy, frequency, and power factor using a PZEM-004T module.
- Uses an ESP32 for data acquisition and Wi-Fi connectivity.
- Publishes JSON measurement messages over MQTT.
- Stores incoming measurements in MySQL through a PHP MQTT subscriber.
- Displays the latest measurements and historical data through a PHP dashboard.
- Provides a PDF report endpoint for recent measurements.
- Includes selected PCB Gerber outputs and hardware documentation.

## Architecture

```text
PZEM-004T -> ESP32 + LCD -> Wi-Fi / MQTT -> MQTT Broker
                                            |
                                            v
                                  PHP MQTT Subscriber
                                            |
                                            v
                                      MySQL Database
                                            |
                                            v
                                PHP Dashboard + Reports
```

See [`docs/architecture.md`](docs/architecture.md) for more detail.

## Technology Stack

| Area | Technologies |
|---|---|
| Embedded hardware | ESP32, PZEM-004T, I2C LCD |
| Firmware | Arduino framework, WiFiManager, PubSubClient, PZEM004Tv30 |
| Messaging | MQTT, Mosquitto-compatible broker |
| Backend | PHP, `php-mqtt/client`, Dompdf |
| Database | MySQL / MariaDB |
| Web UI | HTML, CSS, JavaScript, Chart.js |
| Hardware files | Selected Gerber manufacturing outputs |

## Repository Structure

```text
.
├── firmware/
│   ├── energy_monitor.ino
│   └── config.example.h
├── dashboard/
│   ├── assets/style.css
│   ├── config.example.php
│   ├── db.php
│   ├── composer.json
│   └── *.php
├── database/schema.sql
├── hardware/
│   └── pcb/gerber/  # selected fabrication outputs
└── docs/architecture.md
```

## Getting Started

### Requirements

- ESP32 development board, PZEM-004T module, and compatible LCD wiring.
- Arduino IDE or another ESP32-compatible development environment.
- MQTT broker reachable by both the ESP32 and PHP host.
- PHP with the `mysqli` extension and a local web server, such as Apache/XAMPP.
- MySQL or MariaDB.
- Composer.

### 1. Configure the ESP32 firmware

1. Copy `firmware/config.example.h` to `firmware/config.h`.
2. Set `MQTT_SERVER` to the IP address or hostname of your MQTT broker.
3. Change `WIFI_PORTAL_PASSWORD` to a unique local password.
4. Install the libraries used by the sketch, then build and flash `firmware/energy_monitor.ino`.
5. On first boot, use the Wi-FiManager setup access point to configure the ESP32 Wi-Fi connection.

### 2. Configure the database and dashboard

1. Create a database named `energy_monitor` and a local database user with appropriate permissions.
2. Import [`database/schema.sql`](database/schema.sql).
3. Copy `dashboard/config.example.php` to `dashboard/config.php` and update the database and MQTT connection settings.
4. From the `dashboard/` directory, run `composer install` to install PHP dependencies.
5. Place `dashboard/` under your PHP web server's document root or configure a local virtual host.
6. Start the MQTT broker and run `php mqtt_subscriber.php` while inside the `dashboard/` directory.
7. Open the dashboard through your local web server.

The dashboard loads Chart.js from a pinned CDN URL, so an internet connection is required for the chart library. The MQTT monitoring pipeline uses your configured local network.

## Configuration and Data Hygiene

Local connection settings are deliberately excluded from Git. Do not commit `firmware/config.h`, `dashboard/config.php`, real credentials, raw measurement dumps, local logs, or Composer's generated `vendor/` directory. The database file contains the schema only; historical measurement records are not included.

## Limitations and Safety

This is an educational prototype. Availability depends on the sensor, network, MQTT broker, PHP subscriber, and database. Validate measurement accuracy, units, and long-term reliability before relying on readings. Hardware connected to mains voltage can be hazardous; installation and testing must follow appropriate electrical safety procedures and supervision.

## Project Status

Prototype / educational project.

## Hardware Source Note

The repository currently contains selected Gerber output files, not the editable KiCad source project or a complete manufacturing package. Verify the required fabrication layers and review the design before ordering a board.

## License

No license is granted by this repository at this time. Confirm the right to redistribute the code and hardware files before sharing them outside the intended audience.
