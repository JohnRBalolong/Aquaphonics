// Import Firebase modules as needed
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
import { getDatabase, ref, onValue, set } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyApEwYtsvJ0Kok67yA9QjEYS3bnoDlTlF4",
    authDomain: "aquaphonics-b742b.firebaseapp.com",
    databaseURL: "https://aquaphonics-b742b-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "aquaphonics-b742b",
    storageBucket: "aquaphonics-b742b.appspot.com",
    messagingSenderId: "311872061907",
    appId: "1:311872061907:web:204355bfa9e7c33c4a2f26"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

// Assume 'doin' is a unique identifier you have defined earlier in your system
const phRef = ref(db, `ph_level/${doin}`);
const tempRef = ref(db, `temperature/${doin}`);

// Select elements for pH and temperature gauges
const phouterCircle = document.querySelector(".phouter-circle");
const phneedle = document.querySelector(".phneedle");
const phlabel = document.querySelector(".phlabel span");
const tempouterCircle = document.querySelector(".tempouter-circle");
const tempneedle = document.querySelector(".tempneedle");
const templabel = document.querySelector(".templabel span");

// Arrays to store the last 10 readings
let lastPhReadings = [];
let lastTempReadings = [];
let lastDataTimestamp = 0; // Track when the last data was received

// Function to update the pH gauge
function updatePhGauge(value) {
    let angle;
    let gradient;

    // Update pH gauge logic...

    phouterCircle.style.backgroundImage = gradient;
    phneedle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    phlabel.textContent = `${value} pHs`;
}

// Function to update the temperature gauge
function updateTempGauge(value) {
    let angle;
    let gradient;

    if (value <= 15) {
        gradient = "linear-gradient(to right, rgb(0 16 243) 7%, rgb(6 60 221) 15%, rgb(255 255 255) 50%, rgb(255, 96, 0) 85%, rgb(253, 24, 24) 96%)";
        angle = -100 + (value / 15) * 40; // Range: -100deg to -60deg
    } else if (value <= 30) {
        gradient = "linear-gradient(to right, rgb(0 16 243) 7%, rgb(6 60 221) 15%, rgb(255 255 255) 50%, rgb(255, 96, 0) 85%, rgb(253, 24, 24) 96%)";
        angle = -60 + ((value - 15) / 15) * 60; // Range: -60deg to 0deg
    } else if (value <= 45) {
        gradient = "linear-gradient(to right, rgb(0 16 243) 7%, rgb(6 60 221) 15%, rgb(255 255 255) 50%, rgb(255, 96, 0) 85%, rgb(253, 24, 24) 96%)";
        angle = 0 + ((value - 30) / 15) * 60; // Range: 0deg to 60deg
    } else {
        gradient = "linear-gradient(to right, rgb(0 16 243) 7%, rgb(6 60 221) 15%, rgb(255 255 255) 50%, rgb(255, 96, 0) 85%, rgb(253, 24, 24) 96%)";
        angle = 60 + ((value - 45) / 15) * 40; // Range: 60deg to 100deg
    }

    tempouterCircle.style.backgroundImage = gradient;
    tempneedle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    templabel.textContent = `${value}°C`;
}

// Function to analyze pH readings and send notifications if necessary
function analyzePhReadings() {
    if (lastPhReadings.length >= 10) {
        let stable = true;

        for (let i = 0; i < lastPhReadings.length; i++) {
            const currentPH = lastPhReadings[i];

            if (currentPH < 6) {
                sendNotification("ph", currentPH, "pH level being low, not good for your fish, recommend changing your water.");
                stable = false;
            } else if (currentPH > 8.5) {
                sendNotification("ph", currentPH, "pH level being highly acidic, not good for your fish, recommend changing your water.");
                stable = false;
            }
        }

        if (stable) {
            console.log("The pH values are stable.");
        }

        lastPhReadings = [];
    }
}

// Function to analyze temperature readings and send notifications if necessary
function analyzeTempReadings() {
    if (lastTempReadings.length >= 10) {
        let stable = true;

        for (let i = 0; i < lastTempReadings.length; i++) {
            const currentTemp = lastTempReadings[i];

            if (currentTemp < 25) {
                sendNotification("temperature", currentTemp, "Cold water is not good for fish.");
                stable = false;
            } else if (currentTemp > 32) {
                sendNotification("temperature", currentTemp, "Hot water is not good for fish, recommend changing water.");
                stable = false;
            }
        }

        if (stable) {
            console.log("The temperature values are stable.");
        }

        lastTempReadings = [];
    }
}

// Function to send a notification
function sendNotification(sensorType, value, message) {
    const notificationPath = `/notification/${doin}/${sensorType}`;
    const json = {
        [sensorType]: value,
        date: getCurrentDate(),
        time: getCurrentTime(),
        message: message,
        seen: false
    };

    const notificationRef = ref(db, notificationPath);
    set(notificationRef, json);

    console.log(`Notification sent: ${message}`);
}

// Utility functions to get current date and time
function getCurrentDate() {
    const today = new Date();
    return `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
}

function getCurrentTime() {
    const now = new Date();
    return `${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`;
}

// Function to check if data is coming within 15 seconds
function checkDataActivity() {
    const now = Date.now();
    const timeDifference = now - lastDataTimestamp;

    if (timeDifference > 15000) {
        console.log("No new data received within the last 15 seconds. Skipping notification.");
        lastPhReadings = [];
        lastTempReadings = [];
    }
}

// Listen for changes in the pH data
onValue(phRef, (snapshot) => {
    const data = snapshot.val();
    if (data) {
        const latestTimestamp = Math.max(...Object.keys(data));
        const latestData = data[latestTimestamp];
        const latestPhValue = latestData.ph;

        lastDataTimestamp = Date.now();
        lastPhReadings.push(latestPhValue);

        if (lastPhReadings.length > 10) {
            lastPhReadings.shift();
        }

        updatePhGauge(latestPhValue);
        analyzePhReadings();
    } else {
        console.error("No pH data available at the reference path.");
    }
});

// Listen for changes in the temperature data
onValue(tempRef, (snapshot) => {
    const data = snapshot.val();
    if (data) {
        const latestTimestamp = Math.max(...Object.keys(data));
        const latestData = data[latestTimestamp];
        const latestTemperatureValue = latestData.temperature;

        lastDataTimestamp = Date.now();
        lastTempReadings.push(latestTemperatureValue);

        if (lastTempReadings.length > 10) {
            lastTempReadings.shift();
        }

        updateTempGauge(latestTemperatureValue);
        analyzeTempReadings();
    } else {
        console.error("No temperature data available at the reference path.");
    }
});

// Start checking every 15 seconds if new data is received
setInterval(checkDataActivity, 15000);