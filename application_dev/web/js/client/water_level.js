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
const db = getDatabase();

// Assume 'doin' is a unique identifier you have defined earlier in your system
const waterLevelRef = ref(db, `water_level/${doin}`);

// Select elements for the water level gauge
const outerCircle = document.querySelector(".outer-circle");
const needle = document.querySelector(".needle");
const label = document.querySelector(".label span");

// Array to store the last 10 water level readings
let lastWaterLevelReadings = [];
let lastDataTimestamp = 0; // Track when the last data was received

// Function to update the gauge based on a value (0-100)
function updateGauge(value) {
    let angle;
    let gradient;

    if (value <= 25) { // 0% to 25%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = -100 + (value / 25) * 40; // Range: -100deg to -60deg
    } else if (value <= 35) { // 25% to 35%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = -60 + ((value - 25) / 10) * 20; // Range: -60deg to -40deg
    } else if (value <= 50) { // 35% to 50%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = -40 + ((value - 35) / 15) * 40; // Range: -40deg to 0deg
    } else if (value <= 65) { // 50% to 65%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = 0 + ((value - 50) / 15) * 40; // Range: 0deg to 40deg
    } else if (value <= 85) { // 65% to 85%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = 40 + ((value - 65) / 20) * 40; // Range: 40deg to 80deg
    } else { // 85% to 100%
        gradient = "linear-gradient(to right, rgb(247, 33, 33) -1%, rgb(231, 94, 26) 14%, rgb(11 249 0) 30%, rgb(11 249 0) 71%, rgb(255, 96, 0) 86%, rgb(253, 24, 24) 100%)";
        angle = 80 + ((value - 85) / 15) * 20; // Range: 80deg to 100deg
    }

    outerCircle.style.backgroundImage = gradient;
    needle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    label.textContent = `${value}%`; // Display the percentage value
}

// Function to analyze the water level readings and send a notification if necessary
function analyzeWaterLevelReadings() {
    // Check if we have at least 10 readings
    if (lastWaterLevelReadings.length >= 10) {
        let stable = true;

        // Analyze each water level reading
        for (let i = 0; i < lastWaterLevelReadings.length; i++) {
            const currentWaterLevel = lastWaterLevelReadings[i];

            if (currentWaterLevel < 40) {
                sendNotification("waterlevel", currentWaterLevel, "Water level being low, can cause low oxygen level, monitor water level.");
                stable = false;
            } else if (currentWaterLevel >= 80 && currentWaterLevel <= 100) {
                sendNotification("waterlevel", currentWaterLevel, "Water level rising high.");
                stable = false;
            }
        }

        // If stable, you can log it or do other tasks
        if (stable) {
            console.log("The water level values are stable.");
        }

        // Clear the array after analysis to track the next set of 10 readings
        lastWaterLevelReadings = [];
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

    // Push the notification to Firebase
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

    // If more than 15 seconds have passed without new data, do not send notifications
    if (timeDifference > 15000) {
        console.log("No new data received within the last 15 seconds. Skipping notification.");
        lastWaterLevelReadings = []; // Clear the readings since no valid data is incoming
    }
}

// Listen for changes in the water level data
onValue(waterLevelRef, (snapshot) => {
    const data = snapshot.val();

    // Ensure data is not null and has timestamps
    if (data) {
        const latestTimestamp = Math.max(...Object.keys(data));
        const latestData = data[latestTimestamp];
        const latestWaterLevelValue = latestData.water_level; // Use correct key for water level

        // Update last data timestamp
        lastDataTimestamp = Date.now();

        // Add the latest water level value to the array of last 10 readings
        lastWaterLevelReadings.push(latestWaterLevelValue);

        // Keep the array at a maximum of 10 readings
        if (lastWaterLevelReadings.length > 10) {
            lastWaterLevelReadings.shift(); // Remove the oldest reading to maintain the size
        }

        // Update the gauge with the latest water level value
        updateGauge(latestWaterLevelValue);

        // Analyze the water level readings after adding the latest one
        analyzeWaterLevelReadings();
    } else {
        console.error("No water level data available at the reference path.");
    }
});

// Start checking every 15 seconds if new data is received
setInterval(checkDataActivity, 15000);