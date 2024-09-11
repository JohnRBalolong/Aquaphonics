


// ############################################################################

// #include <OneWire.h>
// #include <DallasTemperature.h>

// // Pin definitions for Arduino Uno (ATmega328P)
// const int oneWireBus = 2;        // Pin 2 for DS18B20 temperature sensor
// #define PH_PIN A0               // Analog pin for pH sensor
// #define TDS_PIN A1              // Analog pin for TDS sensor
// #define WATER_LEVEL_PIN 3       // Digital pin for water level float switch

// #define VREF 5.0                // Analog reference voltage (Volt) of the Arduino Uno (5V)
// #define SCOUNT 30               // Number of samples for TDS reading

// float phValue = 0;
// int tdsBuffer[SCOUNT];          // Store TDS analog values
// float averageTdsVoltage = 0;
// float tdsValue = 0;
// float temperature = 0;
// bool waterLevel = false;        // Variable to store water level status

// OneWire oneWire(oneWireBus);    // Setup a oneWire instance
// DallasTemperature sensors(&oneWire);    // Pass oneWire reference to Dallas Temperature sensor

// void setup() {
//   Serial.begin(9600);            // Communication with ESP8266
//   pinMode(PH_PIN, INPUT);
//   pinMode(TDS_PIN, INPUT);
//   pinMode(WATER_LEVEL_PIN, INPUT_PULLUP); // Initialize the water level pin with internal pull-up resistor

//   sensors.begin();
// }

// void loop() {
//   readAndProcessPh();   // Read pH sensor data
//   readAndProcessTds();  // Read TDS sensor data
//   readWaterLevel();     // Read water level sensor data
  
//   sendDataToESP8266();  // Send data to ESP8266
  
//   delay(3000);          // Delay between readings
// }

// void readAndProcessPh() {
//   int phRaw = analogRead(PH_PIN);  // Read raw analog value from pH sensor
//   float phVoltage = (phRaw * VREF) / 1024.0;  // Convert the analog value into voltage
//   phValue = 2.734 * phVoltage;  // Convert voltage to pH value using calibration factor
//   Serial.print("pH Value: ");
//   Serial.println(phValue, 2);   // Print pH value to two decimal places
// }

// void readAndProcessTds() {
//   static unsigned long sampleTimepoint = millis();
//   if (millis() - sampleTimepoint > 40U)  // Sample TDS every 40 milliseconds
//   {
//     sampleTimepoint = millis();
//     static int tdsIndex = 0;
//     tdsBuffer[tdsIndex] = analogRead(TDS_PIN);
//     tdsIndex++;
//     if (tdsIndex == SCOUNT)
//       tdsIndex = 0;

//     averageTdsVoltage = getMedianNum(tdsBuffer, SCOUNT) * (float)VREF / 1024.0;

//     sensors.requestTemperatures();   // Request temperature from DS18B20
//     temperature = sensors.getTempCByIndex(0);   // Get temperature in Celsius

//     if (temperature != DEVICE_DISCONNECTED_C) {
//       float compensationCoefficient = 1.0 + 0.02 * (temperature - 25.0);
//       float compensationVoltage = averageTdsVoltage / compensationCoefficient;

//       // Convert voltage value to TDS value
//       tdsValue = (133.42 * compensationVoltage * compensationVoltage * compensationVoltage 
//                   - 255.86 * compensationVoltage * compensationVoltage 
//                   + 857.39 * compensationVoltage) * 0.5;

//       Serial.print("TDS Value: ");
//       Serial.print(tdsValue, 0);
//       Serial.println(" ppm");
//       Serial.print("Temperature: ");
//       Serial.print(temperature);
//       Serial.println(" °C");
//     } else {
//       Serial.println("Temperature sensor disconnected.");
//     }
//   }
// }

// void readWaterLevel() {
//   waterLevel = digitalRead(WATER_LEVEL_PIN); // Read the state of the float switch
  
//   int waterLevelPercentage = (waterLevel == LOW) ? 0 : 100;
  
//   Serial.print("Water Level: ");
//   Serial.print(waterLevelPercentage);
//   Serial.println(" %");
// }

// // Send data to ESP8266
// void sendDataToESP8266() {
//     Serial.print("pH:"); 
//     Serial.println(phValue, 2);  // Send pH with two decimal places
//     Serial.print("TDS:");
//     Serial.println(tdsValue, 2);  // Send TDS with two decimal places
//     Serial.print("WaterLevel:");
//     Serial.println((waterLevel == LOW) ? 0 : 100);  // Send water level as 0 or 100
//     Serial.print("Temperature:");
//     Serial.println(temperature, 2);  // Send temperature with two decimal places
//     Serial.println(); // Ensure a newline is sent after each set of data
// }


// int getMedianNum(int bArray[], int iFilterLen) {
//   int bTab[iFilterLen];
//   for (int i = 0; i < iFilterLen; i++)
//     bTab[i] = bArray[i];

//   // Sort array
//   for (int j = 0; j < iFilterLen - 1; j++) {
//     for (int i = 0; i < iFilterLen - j - 1; i++) {
//       if (bTab[i] > bTab[i + 1]) {
//         int temp = bTab[i];
//         bTab[i] = bTab[i + 1];
//         bTab[i + 1] = temp;
//       }
//     }
//   }

//   // Return median
//   if ((iFilterLen & 1) > 0)
//     return bTab[(iFilterLen - 1) / 2];
//   else
//     return (bTab[iFilterLen / 2] + bTab[iFilterLen / 2 - 1]) / 2;
// }



// ############################################################################



// ################################  ESP 8266 Connection
// ################################
// ################################  ESP 8266 Connection
// ################################
// ################################  ESP 8266 Connection

#include <ESP8266WiFi.h>
#include <Firebase_ESP_Client.h>
#include <time.h>

// Define Firebase objects
FirebaseData firebaseData;
FirebaseConfig config;
FirebaseAuth auth;

// Define Wi-Fi credentials
#define WIFI_SSID "Free_Wifi"
#define WIFI_PASSWORD "AlphaBeta"

// Define device ID
#define DOIN "0001"  // Device ID

// Flags to track whether new data is received for each sensor
bool newPHData = false;
bool newTDSData = false;
bool newWaterLevelData = false;
bool newTemperatureData = false;

// Variables to store sensor data
float currentPH = 0.0;
float currentTDS = 0.0;
int currentWaterLevel = 0;
float currentTemperature = 0.0;

void setup() {
  Serial.begin(9600);  // Communication with ATmega328P
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  // Connecting to Wi-Fi
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("Connected to Wi-Fi");

  // Initialize time setup
  setupTime();

  // Firebase setup
  config.host = "aquaphonics-b742b-default-rtdb.asia-southeast1.firebasedatabase.app";
  config.signer.tokens.legacy_token = "AIzaSyApEwYtsvJ0Kok67yA9QjEYS3bnoDlTlF4";

  // Initialize Firebase
  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);

  // Ensure device path exists
  String devicePath = "/devices/" + String(DOIN);
  if (!Firebase.RTDB.getString(&firebaseData, devicePath)) {
    FirebaseJson deviceJson;
    deviceJson.set("email", "");  // Add default or placeholder values if needed
    deviceJson.set("folderName", "");
    deviceJson.set("owner", "");
    deviceJson.set("phone", "");
    deviceJson.set("uid", "");

    Firebase.RTDB.setJSON(&firebaseData, devicePath, &deviceJson);  // Create or update device path
    Serial.println("Device path created in Firebase: " + devicePath);
  }

  // Ensure notification path exists
  // String notificationPath = "/notification/" + String(DOIN);
  // if (!Firebase.RTDB.getString(&firebaseData, notificationPath)) {
  //   FirebaseJson notificationJson;
  //   notificationJson.set("ph", 0);  // Placeholder values
  //   notificationJson.set("timestamp", 0);
    
  //   Firebase.RTDB.setJSON(&firebaseData, notificationPath, &notificationJson);  // Create or update notification path
  //   Serial.println("Notification path created in Firebase: " + notificationPath);
  // }
}

// Setup the NTP time and sync it
void setupTime() {
  configTime(8 * 3600, 0, "pool.ntp.org", "time.nist.gov");  // Set timezone to UTC+8
  Serial.print("Waiting for NTP time sync: ");
  time_t now = time(nullptr);
  while (now < 8 * 3600) {
    delay(500);
    Serial.print(".");
    now = time(nullptr);
  }
  Serial.println("");
  Serial.print("Current time: ");
  Serial.print(ctime(&now));
}

// Get the current date in the desired format
String getCurrentDate() {
  time_t now = time(nullptr);
  struct tm timeinfo;
  localtime_r(&now, &timeinfo);

  char dateBuffer[20];
  strftime(dateBuffer, sizeof(dateBuffer), "%B %d, %Y", &timeinfo);  // e.g., "September 08, 2024"

  return String(dateBuffer);
}

// Get the current time in the desired format
String getCurrentTime() {
  time_t now = time(nullptr);
  struct tm timeinfo;
  localtime_r(&now, &timeinfo);

  char timeBuffer[10];
  strftime(timeBuffer, sizeof(timeBuffer), "%H:%M:%S", &timeinfo);  // HH:MM:SS

  return String(timeBuffer);
}

// Send data to Firebase only if new data is received
void sendDataToFirebase() {
  // Get the current date and time
  String formattedDate = getCurrentDate();
  String formattedTime = getCurrentTime();

  // Create a FirebaseJson object
  FirebaseJson json;
  unsigned long epochTime = time(nullptr);  // Use epoch time as unique ID for Firebase path

  if (newPHData) {
    String path = "/ph_level/" + String(DOIN) + "/" + String(epochTime);
    json.set("ph", currentPH);
    json.set("date", formattedDate);
    json.set("time", formattedTime);
    Firebase.RTDB.setJSON(&firebaseData, path, &json);
    newPHData = false;  // Reset flag
  }

  if (newTDSData) {
    String path = "/tds_level/" + String(DOIN) + "/" + String(epochTime);
    json.clear();
    json.set("tds", currentTDS);
    json.set("date", formattedDate);
    json.set("time", formattedTime);
    Firebase.RTDB.setJSON(&firebaseData, path, &json);
    newTDSData = false;  // Reset flag
  }

  if (newWaterLevelData) {
    String path = "/water_level/" + String(DOIN) + "/" + String(epochTime);
    json.clear();
    json.set("water_level", currentWaterLevel);
    json.set("date", formattedDate);
    json.set("time", formattedTime);
    Firebase.RTDB.setJSON(&firebaseData, path, &json);
    newWaterLevelData = false;  // Reset flag
  }

  if (newTemperatureData) {
    String path = "/temperature/" + String(DOIN) + "/" + String(epochTime);
    json.clear();
    json.set("temperature", currentTemperature);
    json.set("date", formattedDate);
    json.set("time", formattedTime);
    Firebase.RTDB.setJSON(&firebaseData, path, &json);
    newTemperatureData = false;  // Reset flag
  }
}

void loop() {
  // Check if new sensor data is available from ATmega328P
  while (Serial.available()) {
    String sensorData = Serial.readStringUntil('\n');  // Read until newline character
    sensorData.trim();  // Remove extra spaces or line breaks

    // Display raw data
    Serial.println("Received Data: " + sensorData);

    // Parse sensor data and update flags
    if (sensorData.startsWith("pH:")) {
      currentPH = sensorData.substring(3).toFloat();  // Store latest pH value
      newPHData = true;  // Set flag to indicate new data
    }
    else if (sensorData.startsWith("TDS:")) {
      currentTDS = sensorData.substring(4).toFloat();  // Store latest TDS value
      newTDSData = true;  // Set flag to indicate new data
    }
    else if (sensorData.startsWith("WaterLevel:")) {
      currentWaterLevel = sensorData.substring(11).toInt();  // Store latest Water Level value
      newWaterLevelData = true;  // Set flag to indicate new data
    }
    else if (sensorData.startsWith("Temperature:")) {
      currentTemperature = sensorData.substring(12).toFloat();  // Store latest Temperature value
      newTemperatureData = true;  // Set flag to indicate new data
    } else {
      // If sensor data doesn't match known formats
      Serial.println("Unknown data format received: " + sensorData);
      continue;  // Skip this loop iteration
    }
  }

  // Send data to Firebase if new data was received in the last 15 seconds
  static unsigned long lastSendTime = 0;
  if (millis() - lastSendTime >= 15000) {
    sendDataToFirebase();  // Send only new data
    lastSendTime = millis();  // Update send time
  }
}


// //seond code .

// #include <ESP8266WiFi.h>
// #include <Firebase_ESP_Client.h>
// #include <time.h>

// // Define Firebase objects
// FirebaseData firebaseData;
// FirebaseConfig config;
// FirebaseAuth auth;

// // Define Wi-Fi credentials
// #define WIFI_SSID "Free_Wifi"
// #define WIFI_PASSWORD "AlphaBeta"

// // Define device ID
// #define DOIN "08841"  // Device ID

// // Flags to track whether new data is received for each sensor
// bool newPHData = false;
// bool newTDSData = false;
// bool newWaterLevelData = false;
// bool newTemperatureData = false;

// // Variables to store sensor data
// float currentPH = 0.0;
// float currentTDS = 0.0;
// int currentWaterLevel = 0;
// float currentTemperature = 0.0;

// void setup() {
//   Serial.begin(9600);  // Communication with ATmega328P
//   WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

//   // Connecting to Wi-Fi
//   while (WiFi.status() != WL_CONNECTED) {
//     delay(500);
//     Serial.print(".");
//   }
//   Serial.println("Connected to Wi-Fi");

//   // Initialize time setup
//   setupTime();

//   // Firebase setup
//   config.host = "aquaponics-4444c-default-rtdb.asia-southeast1.firebasedatabase.app";
//   config.signer.tokens.legacy_token = "AIzaSyBoGrU6sa2rNzKxdW2ErW-frhwdciI3Cds";

//   // Initialize Firebase
//   Firebase.begin(&config, &auth);
//   Firebase.reconnectWiFi(true);
// }

// // Setup the NTP time and sync it
// void setupTime() {
//   configTime(8 * 3600, 0, "pool.ntp.org", "time.nist.gov");  // Set timezone to UTC+8
//   Serial.print("Waiting for NTP time sync: ");
//   time_t now = time(nullptr);
//   while (now < 8 * 3600) {
//     delay(500);
//     Serial.print(".");
//     now = time(nullptr);
//   }
//   Serial.println("");
//   Serial.print("Current time: ");
//   Serial.print(ctime(&now));
// }

// // Get the current date in the desired format
// String getCurrentDate() {
//   time_t now = time(nullptr);
//   struct tm timeinfo;
//   localtime_r(&now, &timeinfo);

//   char dateBuffer[20];
//   strftime(dateBuffer, sizeof(dateBuffer), "%B %d, %Y", &timeinfo);  // e.g., "September 08, 2024"

//   return String(dateBuffer);
// }

// // Get the current time in the desired format
// String getCurrentTime() {
//   time_t now = time(nullptr);
//   struct tm timeinfo;
//   localtime_r(&now, &timeinfo);

//   char timeBuffer[10];
//   strftime(timeBuffer, sizeof(timeBuffer), "%H:%M:%S", &timeinfo);  // HH:MM:SS

//   return String(timeBuffer);
// }

// // Send notifications based on sensor values
// void sendNotification(String sensorType, float value, String message) {
//   FirebaseJson json;
//   json.set(sensorType, value);
//   json.set("date", getCurrentDate());
//   json.set("time", getCurrentTime());
//   json.set("message", message);

//   String notificationPath = "/notification/" + String(DOIN) + "/" + sensorType;
//   Firebase.RTDB.setJSON(&firebaseData, notificationPath, &json);
//   Serial.println("Notification sent: " + message);
// }

// // Evaluate sensor values and send notifications based on conditions
// void evaluateSensorData() {
//   if (newPHData) {
//     if (currentPH < 6) {
//       sendNotification("ph", currentPH, "pH level being low, not good for your fish, recommend changing your water.");
//     } else if (currentPH > 8.5) {
//       sendNotification("ph", currentPH, "pH level being highly acidic, not good for your fish, recommend changing your water.");
//     }
//     newPHData = false;
//   }

//   if (newTDSData) {
//     if (currentTDS < 200) {
//       sendNotification("tds", currentTDS, "TDS low, monitor if changes normally. If not, recommend changing water.");
//     } else if (currentTDS > 450) {
//       sendNotification("tds", currentTDS, "High TDS, recommend changing water.");
//     }
//     newTDSData = false;
//   }

//   if (newWaterLevelData) {
//     if (currentWaterLevel < 40) {
//       sendNotification("waterlevel", currentWaterLevel, "Water level being low, can cause low oxygen level, monitor water level.");
//     } else if (currentWaterLevel >= 80 && currentWaterLevel <= 100) {
//       sendNotification("waterlevel", currentWaterLevel, "Water level rising high.");
//     }
//     newWaterLevelData = false;
//   }

//   if (newTemperatureData) {
//     if (currentTemperature < 25) {
//       sendNotification("temperature", currentTemperature, "Cold water is not good for fish.");
//     } else if (currentTemperature > 32) {
//       sendNotification("temperature", currentTemperature, "Hot water is not good for fish, recommend changing water.");
//     }
//     newTemperatureData = false;
//   }
// }

// void loop() {
//   // Check if new sensor data is available from ATmega328P
//   while (Serial.available()) {
//     String sensorData = Serial.readStringUntil('\n');  // Read until newline character
//     sensorData.trim();  // Remove extra spaces or line breaks

//     // Display raw data
//     Serial.println("Received Data: " + sensorData);

//     // Parse sensor data and update flags
//     if (sensorData.startsWith("pH:")) {
//       currentPH = sensorData.substring(3).toFloat();  // Store latest pH value
//       newPHData = true;  // Set flag to indicate new data
//     }
//     else if (sensorData.startsWith("TDS:")) {
//       currentTDS = sensorData.substring(4).toFloat();  // Store latest TDS value
//       newTDSData = true;  // Set flag to indicate new data
//     }
//     else if (sensorData.startsWith("WaterLevel:")) {
//       currentWaterLevel = sensorData.substring(11).toInt();  // Store latest Water Level value
//       newWaterLevelData = true;  // Set flag to indicate new data
//     }
//     else if (sensorData.startsWith("Temperature:")) {
//       currentTemperature = sensorData.substring(12).toFloat();  // Store latest Temperature value
//       newTemperatureData = true;  // Set flag to indicate new data
//     } else {
//       // If sensor data doesn't match known formats
//       Serial.println("Unknown data format received: " + sensorData);
//       continue;  // Skip this loop iteration
//     }
//   }

//   // Send data and evaluate conditions every 15 seconds
//   static unsigned long lastSendTime = 0;
//   if (millis() - lastSendTime >= 15000) {
//     evaluateSensorData();  // Evaluate sensor data and send notifications
//     lastSendTime = millis();  // Update send time
//   }
// }



// The notifyIfNecessary function is not needed since we're not sending data to Firebase.

















































// #include <SoftwareSerial.h>

// SoftwareSerial espSerial(2, 3); // RX, TX

// void setup() {
//   Serial.begin(9600); // Serial monitor
//   espSerial.begin(115200); // ESP8266 default baud rate
  
//   Serial.println("Initializing...");
  
//   // Connect to Wi-Fi
//   connectToWiFi("Free_Wifi", "AlphaBeta");
// }

// void loop() {
//   // Check for data from the ESP8266
//   if (espSerial.available()) {
//     Serial.write(espSerial.read());
//   }

//   // Check for data from Serial Monitor
//   if (Serial.available()) {
//     espSerial.write(Serial.read());
//   }
// }

// void connectToWiFi(const char* ssid, const char* password) {
//   espSerial.println("AT+CWJAP=\"" + String(ssid) + "\",\"" + String(password) + "\"");
//   delay(2000);

//   while (espSerial.available()) {
//     Serial.write(espSerial.read());
//   }
  
//   Serial.println("Connected to WiFi");
// }