#include <WiFi.h>
#include <WiFiManager.h>       
#include <PubSubClient.h>
#include <PZEM004Tv30.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include "config.h"  // Copy config.example.h to config.h and edit local settings.

// Pin Setup
#define PZEM_RX 16
#define PZEM_TX 17

// LCD Setup
#define SDA_PIN 21
#define SCL_PIN 22
#define LCD_ADDRESS 0x27  
LiquidCrystal_I2C lcd(LCD_ADDRESS, 16, 2);

PZEM004Tv30 pzem(&Serial2, PZEM_RX, PZEM_TX);

// WiFiManager
WiFiManager wm;

// MQTT Settings
const char* mqtt_server = MQTT_SERVER;
const int mqtt_port = MQTT_PORT;
WiFiClient espClient;
PubSubClient client(espClient);

// Timers
unsigned long lastMsg = 0;
unsigned long lastLCDUpdate = 0;
const long interval = 5000; // publish interval (ms)
const long lcdInterval = 10000; // LCD update interval (ms)

// MQTT Reconnect Function
void reconnectMQTT() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    if (client.connect("ESP32_PZEM_Client")) {
      Serial.println("connected!");
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("MQTT Connected");
      delay(1000);
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" retry in 5 seconds");
      delay(5000);
    }
  }
}

// menampilkan data di LCD
void displayLCDData(float voltage, float current, float power, float frequency) {
  unsigned long now = millis();
  static int displayMode = 0;
  
  if (now - lastLCDUpdate > lcdInterval) {
    lastLCDUpdate = now;
    displayMode++;
    if (displayMode > 2) displayMode = 0;
    
    lcd.clear();
    
    switch(displayMode) {
      case 0:
        // Tampilkan Voltage dan Current
        lcd.setCursor(0, 0);
        lcd.print("V:");
        lcd.print(voltage, 1);
        lcd.print("V");
        lcd.setCursor(8, 0);
        lcd.print("A:");
        lcd.print(current, 2);
        lcd.print("A");
        
        lcd.setCursor(0, 1);
        lcd.print("P:");
        lcd.print(power, 0);
        lcd.print("W");
        lcd.setCursor(8, 1);
        lcd.print("F:");
        lcd.print(frequency, 1);
        lcd.print("Hz");
        break;
        
      case 1:
        // Tampilkan info koneksi
        lcd.setCursor(0, 0);
        lcd.print("WiFi: ");
        if (WiFi.status() == WL_CONNECTED) {
          lcd.print("OK");
        } else {
          lcd.print("FAIL");
        }
        
        lcd.setCursor(0, 1);
        lcd.print("MQTT: ");
        if (client.connected()) {
          lcd.print("Connected");
        } else {
          lcd.print("Offline");
        }
        break;
        
      case 2:
        // Tampilkan IP Address
        lcd.setCursor(0, 0);
        lcd.print("IP: ");
        lcd.print(WiFi.localIP().toString().c_str());
        
        lcd.setCursor(0, 1);
        lcd.print("PZEM Active");
        break;
    }
  }
}

// Setup
void setup() {
  Serial.begin(115200);
  Serial.println("\n=== ESP32 + PZEM004T + MQTT + LCD ===");

  // Initialize I2C untuk LCD
  Wire.begin(SDA_PIN, SCL_PIN);
  
  // Initialize LCD
  lcd.begin();
  lcd.backlight();
  lcd.print("Starting...");
  delay(1000);

  // Initialize WiFiManager
  bool res;
  res = wm.autoConnect(WIFI_PORTAL_SSID, WIFI_PORTAL_PASSWORD); // Configure a unique local setup password.
  if (!res) {
    Serial.println("WiFi connection failed. Restarting...");
    lcd.clear();
    lcd.print("WiFi Failed!");
    delay(2000);
    ESP.restart();
  }
  Serial.println("WiFi connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi Connected");
  lcd.setCursor(0, 1);
  lcd.print(WiFi.localIP().toString().c_str());
  delay(2000);

  // Set MQTT broker
  client.setServer(mqtt_server, mqtt_port);

  // Initialize Serial2 for PZEM
  Serial2.begin(9600, SERIAL_8N1, PZEM_RX, PZEM_TX);
}

// Main Loop
void loop() {
  if (!client.connected()) {
    reconnectMQTT();
  }
  client.loop();

  unsigned long now = millis();
  
  if (now - lastMsg > interval) {
    lastMsg = now;

    // Read data from PZEM
    float voltage = pzem.voltage();
    float current = pzem.current();
    float power   = pzem.power();
    float energy  = pzem.energy();
    float frequency = pzem.frequency();
    float pf       = pzem.pf();

    if (!isnan(voltage)) {
      // Construct JSON payload
      String payload = "{";
      payload += "\"voltage\":" + String(voltage, 2) + ",";
      payload += "\"current\":" + String(current, 3) + ",";
      payload += "\"power\":" + String(power, 2) + ",";
      payload += "\"energy\":" + String(energy, 3) + ",";
      payload += "\"frequency\":" + String(frequency, 2) + ",";
      payload += "\"pf\":" + String(pf, 2);
      payload += "}";

      // Publish data
      client.publish("pzem/data", payload.c_str());
      Serial.println("📡 Published: " + payload);
      
      // Tampilkan data di LCD
      displayLCDData(voltage, current, power, frequency);
    } else {
      Serial.println("⚠️ Error reading PZEM sensor!");
      lcd.clear();
      lcd.print("PZEM Error!");
    }
  }
}