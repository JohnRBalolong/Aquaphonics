#define PH_OFFSET -1.00 // if there have offset

#define SensorPin A0        // the pH meter Analog output is connected with the Arduino’s Analog
unsigned long int avgValue;  // Store the average value of the sensor feedback
float b;
int buf[10], temp;

void setup() {
  pinMode(13, OUTPUT);  
  Serial.begin(9600);  
  Serial.println("Ready");    // Test the serial monitor
}

void loop() {
  for (int i = 0; i < 10; i++)       // Get 10 sample value from the sensor for smoothing the value
  { 
    buf[i] = analogRead(SensorPin);
    delay(10);
  }

  for (int i = 0; i < 9; i++)        // Sort the analog values from small to large
  {
    for (int j = i + 1; j < 10; j++) {
      if (buf[i] > buf[j]) {
        temp = buf[i];
        buf[i] = buf[j];
        buf[j] = temp;
      }
    }
  }

  avgValue = 0;
  for (int i = 2; i < 8; i++)  // Take the average value of 6 center samples
    avgValue += buf[i];

  float phValue = (float)avgValue * 5.0 / 1024 / 6;  // Convert the analog value into millivolts
  phValue = 3.128 * phValue;  // Adjust the conversion factor to map 2.56V to pH 7.0

  phValue = phValue + PH_OFFSET;

  Serial.print("    pH:");  
  Serial.print(phValue, 2);
  Serial.println(" ");
  digitalWrite(13, HIGH);       
  delay(800);
  digitalWrite(13, LOW); 
}
