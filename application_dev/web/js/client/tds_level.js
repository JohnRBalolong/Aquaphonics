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
const tdsRef = ref(db, `tds_level/${doin}`);

// Select elements
const tdsouterCircle = document.querySelector(".tdsouter-circle");
const tdsneedle = document.querySelector(".tdsneedle");
const tdslabel = document.querySelector(".tdslabel span");

// Array to store the last 10 TDS readings
let lastTdsReadings = [];
let lastDataTimestamp = 0; // Track when the last data was received

// Function to update the gauge
function updateGauge(value) {
    let angle;
    let gradient;

    if (value <= 100) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = -100 + (value / 100) * 20;
    } else if (value <= 200) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = -80 + ((value - 100) / 100) * 20;
    } else if (value <= 300) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = -60 + ((value - 200) / 100) * 20;
    } else if (value <= 400) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = -40 + ((value - 300) / 100) * 20;
    } else if (value <= 500) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = -20 + ((value - 400) / 100) * 20;
    } else if (value <= 600) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = 0 + ((value - 500) / 100) * 20;
    } else if (value <= 700) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = 20 + ((value - 600) / 100) * 20;
    } else if (value <= 800) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = 40 + ((value - 700) / 100) * 20;
    } else if (value <= 900) {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = 60 + ((value - 800) / 100) * 20;
    } else {
        gradient = "linear-gradient(to right, rgb(0 208 255), rgb(9 187 0) 20%, rgb(249 103 0) 75%, rgb(255 16 16) 89%)";
        angle = 80 + ((value - 900) / 100) * 20;
    }

    tdsouterCircle.style.backgroundImage = gradient;
    tdsneedle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    tdslabel.textContent = `${value}ppm`;
}

// Function to analyze the TDS readings and send a notification if necessary
function analyzeTdsReadings() {
    // Check if we have at least 10 readings
    if (lastTdsReadings.length >= 10) {
        let stable = true;

        // Analyze each TDS reading
        for (let i = 0; i < lastTdsReadings.length; i++) {
            const currentTDS = lastTdsReadings[i];

            if (currentTDS < 200) {
                sendNotification("tds", currentTDS, "TDS level is low, recommend monitoring and adjusting the water quality.");
                stable = false;
            } else if (currentTDS > 450) {
                sendNotification("tds", currentTDS, "High TDS level, recommend changing the water.");
                stable = false;
            }
        }

        // If stable, you can log it or do other tasks
        if (stable) {
            console.log("The TDS values are stable.");
        }

        // Clear the array after analysis to track the next set of 10 readings
        lastTdsReadings = [];
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
        lastTdsReadings = []; // Clear the readings since no valid data is incoming
    }
}

// Listen for changes in the TDS data
onValue(tdsRef, (snapshot) => {
    const data = snapshot.val();
    const latestTimestamp = Math.max(...Object.keys(data));
    const latestData = data[latestTimestamp];
    const latestTdsValue = latestData.tds; // Make sure this matches the key for your TDS data

    // Update last data timestamp
    lastDataTimestamp = Date.now();

    // Add the latest TDS value to the array of last 10 readings
    lastTdsReadings.push(latestTdsValue);

    // Keep the array at a maximum of 10 readings
    if (lastTdsReadings.length > 10) {
        lastTdsReadings.shift(); // Remove the oldest reading to maintain the size
    }

    // Update the gauge with the latest TDS value
    updateGauge(latestTdsValue);

    // Analyze the TDS readings after adding the latest one
    analyzeTdsReadings();
});

// Start checking every 15 seconds if new data is received
setInterval(checkDataActivity, 15000);