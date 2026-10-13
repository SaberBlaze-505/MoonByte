# Energy Monitor IoT

An IoT-based electrical energy monitoring system built with ESP32, PZEM-004T, MQTT, PHP, and MySQL.

## Overview

Energy Monitor IoT is a prototype that collects electrical measurements, transmits them over a network, stores them in a database, and presents the data through a web dashboard.

The project demonstrates the integration of embedded systems, electrical measurement, network communication, backend development, and database management in a single monitoring system.

## Features

- Collects electrical measurements through a PZEM-004T module.
- Uses an ESP32 microcontroller for data acquisition and Wi-Fi connectivity.
- Transmits measurement data using the MQTT messaging protocol.
- Stores received measurements in a MySQL database.
- Displays measurement data through a PHP-based web dashboard.
- Supports reviewing historical measurements and generating reports.

## System Architecture

```text
PZEM-004T
    |
    v
ESP32 Microcontroller
    |
    v
Wi-Fi Network
    |
    v
MQTT Broker
    |
    v
PHP MQTT Subscriber
    |
    v
MySQL Database
    |
    v
Web Dashboard and Reports
```

## Technology Stack

| Category | Technologies |
|---|---|
| Embedded system | ESP32, Arduino framework |
| Electrical measurement | PZEM-004T |
| Connectivity | Wi-Fi |
| Messaging | MQTT, Mosquitto |
| Backend | PHP |
| Database | MySQL |
| Web interface | HTML, CSS, JavaScript |
| Hardware documentation | Circuit schematic and PCB design files |

## Repository Structure

```text
energy-monitor-iot/
├── firmware/       # ESP32 firmware
├── dashboard/      # PHP web application and MQTT subscriber
├── database/       # Database schema and safe sample data
├── hardware/       # Circuit schematic and PCB documentation
├── docs/           # Architecture diagrams and screenshots
├── README.md
└── .gitignore
```

The repository structure may evolve as the project is organized and documented.

## Getting Started

### Prerequisites

- ESP32 development board
- PZEM-004T measurement module
- Arduino IDE or a compatible ESP32 development environment
- MQTT broker, such as Mosquitto
- PHP and a web server, such as Apache through XAMPP
- MySQL or a compatible database server
- Composer for installing PHP dependencies

### Setup

1. **Prepare the firmware.** Open the project in your ESP32 development environment and install the libraries required by the source code.
2. **Configure the network.** Set the appropriate Wi-Fi and MQTT broker settings for your environment. Do not commit private configuration or credentials.
3. **Start the MQTT broker.** Ensure the ESP32 and the PHP subscriber can reach the broker over the network.
4. **Prepare the database.** Create the required database and import the schema provided in `database/`.
5. **Configure the dashboard.** Set the local database connection details using an untracked configuration file based on the provided example, if available.
6. **Install PHP dependencies.** Run `composer install` in the dashboard directory.
7. **Start the application.** Run the web server and MQTT subscriber, then open the dashboard through your local development environment.

Configuration details may need adjustment depending on the hardware, network, and software environment.

## Limitations

This project is a prototype intended for educational and development purposes. Its operation depends on the measurement module, Wi-Fi network, MQTT broker, database, and web server. Measurement accuracy, data continuity, and long-term reliability should be verified before practical deployment.

## Safety Notice

Electrical measurement hardware may interface with hazardous mains voltage. Installation, wiring, and testing must follow appropriate electrical safety procedures and be performed with suitable supervision and equipment.

## Project Status

**Prototype / Educational Project**

## License and Usage

This repository does not currently grant permission to redistribute or reuse its contents. A license should only be added once ownership and permission to publish the project have been confirmed.
