# System Architecture

```text
PZEM-004T
    | UART
    v
ESP32 + LCD
    | Wi-Fi / MQTT (`pzem/data`)
    v
MQTT Broker
    | MQTT subscriber (PHP)
    v
MySQL (`pzem_data`)
    | SQL queries
    v
PHP Dashboard + Chart.js + PDF Report
```

The ESP32 reads voltage, current, power, accumulated energy, frequency, and power factor from the PZEM module. It publishes a JSON payload to the MQTT topic. A PHP subscriber persists incoming messages in MySQL, where the web dashboard reads current and historical measurements.

Local network addresses and credentials belong in ignored local configuration files, not in the repository.
