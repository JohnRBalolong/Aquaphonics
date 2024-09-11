<?php
require '../../query/auth.php';


// Full path to the user's folder
$userDirectory = '../../query/user_directories/' . $folderName . '/';

// Function to get the latest image in the user's directory
function getLatestImage($directory) {
    $files = scandir($directory, SCANDIR_SORT_DESCENDING);  // List files in descending order
    foreach ($files as $file) {
        // Filter only image files (jpg, jpeg, png, gif)
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            return $file;  // Return the first image found (which is the latest due to sorting)
        }
    }
    return 'default.jpg';  // Default image if no image is found
}

$latestImage = getLatestImage($userDirectory);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile UI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #3cb371, #77ff00d1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .profile-container {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .profile-container img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .profile-container h2 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }
        .profile-container .btn {
            background-color: #6495ed;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .profile-container .btn:hover {
            background-color: #4169e1;
        }
        .profile-container .option {
            background-color: #e6f2ff;
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
          
        }
   
        .profile-container .option:hover {
            background-color: #cce6ff;
        }
        .profile-container .option i {
            margin-right: 10px;
        }
        .profile-container .option span {
            flex-grow: 1;
            text-align: left;
            color: #333;
        }
        .profile-container .option .toggle-btn {
            display: inline-flex;
            align-items: center;
        }
        .profile-container .option .toggle-btn span {
            margin-left: 5px;
            color: #00c851;
            font-weight: bold;
        }
        .profile-container .footer {
            font-size: 12px;
            margin-top: 20px;
            color: #888;
        }

        i#notification-icon {
    cursor: pointer;
}

.back-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: white;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .back-icon:hover {
            color: #ccc;
        }


         /* Modal styles */
         .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .cpass{
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Back Icon -->
    <i class="fas fa-arrow-left back-icon" onclick="goBackToHome()"></i>


<div class="profile-container">



    <img src="<?php echo $userDirectory . htmlspecialchars($latestImage); ?>" alt="Profile Picture">
    <h2><?php echo htmlspecialchars($owner); ?></h2>

    
    <!-- Edit Profile button -->
<a href="#" class="btn" onclick="document.getElementById('fileUpload').click();">Edit Profile</a>

<!-- Hidden file input to upload the image -->
<form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
    <input type="file" name="fileUpload" id="fileUpload" style="display: none;" onchange="document.getElementById('uploadForm').submit();">
</form>
    
    <div class="option">
        <i class="fas fa-envelope"></i>
        <span><?php echo htmlspecialchars($email); ?></span>
    </div>
    <div class="option">
        <i class="fas fa-phone"></i>
        <span><?php echo htmlspecialchars($phone); ?></span>
    </div>
    
    <div class="option cpass" onclick="openPasswordModal()">
    <i class="fas fa-lock"></i>
    <span>Change password</span>
</div>

    
    <div class="option">
    <i class="fas fa-bell"></i>
    <span>Notifications</span>
    <div class="toggle-btn" onclick="toggleNotification()">
        <i id="notification-icon" class="fas fa-toggle-on"></i>
        <span id="notification-status">ON</span>
    </div>
</div>  
    
    <div class="option">
        <i class="fas fa-info-circle"></i>
        <span>About</span>
    </div>
    
    <a style="color: black" href="../../query/logout.php" class="option">
    <i class="fas fa-sign-out-alt"></i>
    <span>Logout</span>
</a>
    
    <div class="footer">
        Privacy & Policy
    </div>
</div>

  <!-- Modal for password change -->
  <div id="passwordModal" class="modal">
        <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
            <h2>Password Reset</h2>
            <p>A request to change your password has been sent to your email: <strong><?php echo $email; ?></strong></p>
            <p>Please check your inbox and follow the instructions to reset your password.</p>
        </div>
    </div>

<script>
    // Function to go back to home.php
    function goBackToHome() {
            window.location.href = "home.php"; // Redirect to home.php
        }

     
    // Attach the toggleNotification function to the window object
window.toggleNotification = function() {
    // Toggle the current status
    isNotificationOn = !isNotificationOn;

    // Update the UI based on the current status
    const statusElement = document.getElementById("notification-status");
    const iconElement = document.getElementById("notification-icon");

    if (isNotificationOn) {
        statusElement.textContent = "ON";
        iconElement.classList.remove("fa-toggle-off");
        iconElement.classList.add("fa-toggle-on");
    } else {
        statusElement.textContent = "OFF";
        iconElement.classList.remove("fa-toggle-on");
        iconElement.classList.add("fa-toggle-off");
    }

    // Update the status in Firebase
    updateNotificationStatus(isNotificationOn ? "on" : "off");
};

</script>



<script type="module">
    // Import Firebase dependencies
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
    import { getDatabase, ref, update, get } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";
    import { getAuth, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-auth.js";

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
    const auth = getAuth(app);

    let isNotificationOn = true; // Initial state

    // Function to send password reset email
    function sendPasswordReset(userEmail) {
        sendPasswordResetEmail(auth, userEmail)
            .then(() => {
                // alert('Password reset email sent! Please check your inbox.');
            })
            .catch((error) => {
                console.error("Error sending password reset email:", error);
                alert('Failed to send password reset email.');
            });
    }

    // Function to open the password modal and send password reset email
    window.openPasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'block';
        const userEmail = "<?php echo $email; ?>";  // Assuming the email is coming from PHP session
        sendPasswordReset(userEmail);
    };

    // Function to close the modal
    window.closeModal = function() {
        document.getElementById('passwordModal').style.display = 'none';
    };

    // Attach the toggleNotification function to the window object
    window.toggleNotification = function() {
        isNotificationOn = !isNotificationOn;
        const statusElement = document.getElementById("notification-status");
        const iconElement = document.getElementById("notification-icon");

        if (isNotificationOn) {
            statusElement.textContent = "ON";
            iconElement.classList.remove("fa-toggle-off");
            iconElement.classList.add("fa-toggle-on");
        } else {
            statusElement.textContent = "OFF";
            iconElement.classList.remove("fa-toggle-on");
            iconElement.classList.add("fa-toggle-off");
        }

        updateNotificationStatus(isNotificationOn ? "on" : "off");
    };

    // Function to update the notification status in Firebase
    function updateNotificationStatus(status) {
        const notificationRef = ref(db, 'notification_status/my_action/');
        update(notificationRef, { status: status })
            .then(() => {
                console.log("Notification status updated to:", status);
            })
            .catch((error) => {
                console.error("Error updating notification status:", error);
            });
    }

    // Fetch the current notification status from Firebase when the page loads
    window.onload = function() {
        const notificationRef = ref(db, 'notification_status/my_action/status');
        get(notificationRef).then((snapshot) => {
            if (snapshot.exists()) {
                const currentStatus = snapshot.val();
                isNotificationOn = currentStatus === "on";

                const statusElement = document.getElementById("notification-status");
                const iconElement = document.getElementById("notification-icon");

                if (isNotificationOn) {
                    statusElement.textContent = "ON";
                    iconElement.classList.remove("fa-toggle-off");
                    iconElement.classList.add("fa-toggle-on");
                } else {
                    statusElement.textContent = "OFF";
                    iconElement.classList.remove("fa-toggle-on");
                    iconElement.classList.add("fa-toggle-off");
                }
            }
        }).catch((error) => {
            console.error("Error fetching notification status:", error);
        });
    };
</script>



</body>
</html>
