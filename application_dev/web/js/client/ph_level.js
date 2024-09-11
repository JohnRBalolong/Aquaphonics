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
const phRef = ref(db, `ph_level/${doin}`);

// Select elements
const phouterCircle = document.querySelector(".phouter-circle");
const phneedle = document.querySelector(".phneedle");
const phlabel = document.querySelector(".phlabel span");

// Array to store the last 10 pH readings
let lastPhReadings = [];
let lastDataTimestamp = 0; // Track when the last data was received

// Function to update the gauge
function updateGauge(value) {
    let angle;
    let gradient;

    if (value <= 3) {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = -120 + ((value - 1) / 2) * 40;
    } else if (value <= 5) {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = -80 + ((value - 3) / 2) * 40;
    } else if (value <= 7) {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = -40 + ((value - 5) / 2) * 40;
    } else if (value <= 9) {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = 0 + ((value - 7) / 2) * 40;
    } else if (value <= 12) {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = 40 + ((value - 9) / 3) * 40;
    } else {
        gradient = "linear-gradient(to right, rgb(255 24 24) 5%, rgb(255 201 174) 34%, rgb(31, 255, 234) 50%, rgb(255 49 247) 74%, rgb(112 0 231) 98%)";
        angle = 80 + ((value - 12) / 2) * 40;
    }

    phouterCircle.style.backgroundImage = gradient;
    phneedle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    phlabel.textContent = `${value} pHs`;
}

// Function to analyze the pH readings and send a notification if necessary
function analyzePhReadings() {
    // Check if we have at least 10 readings
    if (lastPhReadings.length >= 10) {
        let stable = true;

        // Analyze each pH reading
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

        // If stable, you can log it or do other tasks
        if (stable) {
            console.log("The pH values are stable.");
        }

        // Clear the array after analysis to track the next set of 10 readings
        lastPhReadings = [];
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
        lastPhReadings = []; // Clear the readings since no valid data is incoming
    }
}

// Listen for changes in the pH data
onValue(phRef, (snapshot) => {
    const data = snapshot.val();
    const latestTimestamp = Math.max(...Object.keys(data));
    const latestData = data[latestTimestamp];
    const latestPhValue = latestData.ph;

    // Update last data timestamp
    lastDataTimestamp = Date.now();

    // Add the latest pH value to the array of last 10 readings
    lastPhReadings.push(latestPhValue);

    // Keep the array at a maximum of 10 readings
    if (lastPhReadings.length > 10) {
        lastPhReadings.shift(); // Remove the oldest reading to maintain the size
    }

    // Update the gauge with the latest pH value
    updateGauge(latestPhValue);

    // Analyze the pH readings after adding the latest one
    analyzePhReadings();
});

// Start checking every 15 seconds if new data is received
setInterval(checkDataActivity, 15000);