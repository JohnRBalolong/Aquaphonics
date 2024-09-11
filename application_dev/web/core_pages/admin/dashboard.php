<?php
require '../../query/admin_auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devices Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #3cb371, #77ff00d1);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
          /* Logout button */
          .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #0c4253;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logout-btn i {
            font-size: 20px;
        }

        /* Device Summary Grid */
        .device-summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .device-summary .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 200px;
        }

        .device-summary .card i {
            font-size: 30px;
            margin-bottom: 10px;
            color: #3cb371;
        }

        .device-summary .card h3 {
            margin: 0;
            font-size: 24px;
        }

        .device-summary .card p {
            font-size: 16px;
            color: gray;
        }

        /* Table Styles */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #3cb371;
            color: white;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .device-summary {
                grid-template-columns: 1fr; /* Single column on mobile */
                gap: 10px;
            }

            table {
                width: 95%; /* Reduce table width on smaller screens */
            }

            th, td {
                padding: 8px; /* Adjust padding for better mobile fit */
                font-size: 14px; /* Reduce font size */
            }
        }

        @media (max-width: 480px) {
            .device-summary .card h3 {
                font-size: 20px;
            }

            .device-summary .card p {
                font-size: 14px;
            }

            th, td {
                font-size: 12px; /* Further reduce font size on smaller devices */
            }
        }

    </style>
</head>
<body>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="location.href='../../query/logout.php';">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <!-- Device Summary Section -->
    <div class="device-summary">
        <div class="card">
            <i class="fas fa-tools"></i>
            <h3 id="total-devices">0</h3>
            <p>Devices Created</p>
        </div>
        <div class="card">
            <i class="fas fa-laptop"></i>
            <h3 id="used-devices">0</h3>
            <p>Used Devices</p>
        </div>
        <div class="card">
            <i class="fas fa-tablet-alt"></i>
            <h3 id="unused-devices">0</h3>
            <p>Unused Devices</p>
        </div>
    </div>

    <!-- Devices Table -->
    <table>
        <thead>
            <tr>
                <th>Device ID</th>
                <th>Owner</th>
            </tr>
        </thead>
        <tbody id="devices-table-body">
            <!-- Rows will be inserted here dynamically -->
        </tbody>
    </table>

    <!-- Firebase Script -->
    <script type="module">
        // Import Firebase modules as needed
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
        import { getDatabase, ref, get } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

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

        // Fetch device data from Firebase and update the dashboard
        const fetchDeviceData = async () => {
            const devicesRef = ref(db, 'devices');
            const snapshot = await get(devicesRef);
            
            if (snapshot.exists()) {
                const devices = snapshot.val();
                let totalDevices = 0;
                let usedDevices = 0;
                let unusedDevices = 0;
                let tableBody = '';

                for (let deviceId in devices) {
                    totalDevices++;
                    const device = devices[deviceId];
                    const owner = device.owner || ''; // Get the owner or an empty string if none
                    
                    // Determine if the device is used or unused
                    if (owner) {
                        usedDevices++;
                        tableBody += `<tr><td>${deviceId}</td><td>${owner}</td></tr>`;
                    } else {
                        unusedDevices++;
                        tableBody += `<tr><td>${deviceId}</td><td>Unused</td></tr>`;
                    }
                }

                // Update the dashboard with the counts
                document.getElementById('total-devices').textContent = totalDevices;
                document.getElementById('used-devices').textContent = usedDevices;
                document.getElementById('unused-devices').textContent = unusedDevices;

                // Update the table with the device data
                document.getElementById('devices-table-body').innerHTML = tableBody;
            } else {
                console.log("No devices data found.");
            }
        };

        // Call the function to fetch device data and update the dashboard
        fetchDeviceData();
    </script>
</body>
</html>