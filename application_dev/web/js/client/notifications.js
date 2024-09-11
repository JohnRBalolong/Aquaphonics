import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
import { getDatabase, ref, onValue, update, get } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

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

// Fetch notifications from Firebase
function fetchNotifications() {
    const notificationsRef = ref(db, `notification/${doin}`);
    onValue(notificationsRef, (snapshot) => {
        const data = snapshot.val();
        const notificationDiv = document.getElementById('notification_div');

        // Clear existing notifications
        notificationDiv.innerHTML = '<i class="fas fa-arrow-left back-icon" onclick="goBack()"></i>';

        if (data) {
            // Loop through each notification and create HTML elements
            for (const [key, value] of Object.entries(data)) {
                const notification = value;

                // Combine date and time
                const dateTimeStr = `${notification.date} ${notification.time}`;
                const notificationTime = new Date(dateTimeStr); // Create Date object

                // Get the current time
                const currentTime = new Date();

                // Calculate the time difference in milliseconds
                const timeDifference = currentTime - notificationTime;
                const minutesAgo = Math.floor(timeDifference / (1000 * 60)); // convert ms to minutes
                const hoursAgo = Math.floor(timeDifference / (1000 * 60 * 60)); // convert ms to hours
                const daysAgo = Math.floor(timeDifference / (1000 * 60 * 60 * 24)); // convert ms to days

                let timeAgoText = "";
                if (daysAgo > 0) {
                    timeAgoText = `${daysAgo} day(s) ago`;
                } else if (hoursAgo > 0) {
                    timeAgoText = `${hoursAgo} hour(s) ago`;
                } else {
                    timeAgoText = `${minutesAgo} minute(s) ago`;
                }

                const notificationElement = document.createElement('div');
                notificationElement.className = `notification_display ${notification.seen ? 'read' : 'unread'}`;
                notificationElement.onclick = () => showFullNotification(notificationElement, key, notification.seen);

                notificationElement.innerHTML = `
                    <div class="notification_text">
                        ${notification.message}
                    </div>
                    <div class="notification_info">
                        <span class="notification_time">${timeAgoText}</span>
                        <span class="notification_dot" style="background-color: ${notification.seen ? 'green' : 'red'};"></span>
                    </div>
                `;

                notificationDiv.appendChild(notificationElement);
            }
        } else {
            notificationDiv.innerHTML = '<div class="no-notifications">No notifications available</div>';
        }
    });
}


// Show full notification
function showFullNotification(element, notificationKey, isSeen) {
    const notificationDiv = document.getElementById('notification_div');

    // Fetch notification data from Firebase
    const notificationRef = ref(db, `notification/${doin}/${notificationKey}`);

    get(notificationRef).then((snapshot) => {
        if (snapshot.exists()) {
            const notification = snapshot.val();
            const message = notification.message;

            // Check if the message exists and is not empty
            if (!message || message.trim() === '') {
                alert('Message content is missing or empty!');
                return; // Don't continue if the message is empty
            }

            console.log('Fetched message:', message);  // Log the fetched message

            // Ensure the notificationDiv is visible
            notificationDiv.classList.add('full-view');

            // Hide all other notifications and display only the selected one
            document.querySelectorAll('.notification_display').forEach((el) => {
                if (el !== element) {
                    el.classList.remove('full-notification');
                    el.style.display = 'none';
                }
            });

            // Expand the clicked notification
            element.classList.add('full-notification');
            element.classList.add('open');

            // Insert the full message into the notification's text
            let notificationText = element.querySelector('.notification_text');

            // If the text container doesn't exist, create one
            if (!notificationText) {
                notificationText = document.createElement('div');
                notificationText.classList.add('notification_text');
                element.appendChild(notificationText);
            }

            // Display the full message
            notificationText.style.display = 'block';
            notificationText.style.color = 'black'; // Make sure the text color is readable
            notificationText.innerText = message;

            // Mark the notification as seen when going back
            window.goBack = function() {
                const notificationDiv = document.getElementById('notification_div');

                // Mark the notification as seen only if it was previously unseen
                if (!isSeen) {
                    update(notificationRef, { seen: true })
                        .then(() => {
                            element.classList.remove('unread'); // Remove unread class
                            element.classList.add('read');   // Add read class

                            const dotElement = element.querySelector('.notification_dot');
                            if (dotElement) {
                                dotElement.style.backgroundColor = 'green';  // Update red dot to green
                            }
                        })
                        .catch((error) => {
                            console.error('Error updating seen status:', error);
                        });
                }

                // Hide the expanded notification and show all notifications
                document.querySelectorAll('.notification_display').forEach((el) => {
                    el.classList.remove('full-notification');
                    el.classList.remove('open');
                    el.style.display = 'flex';
                });

                notificationDiv.classList.remove('full-view');
            };
        } else {
            console.log('No data available');
        }
    }).catch((error) => {
        console.error('Error fetching data:', error);
    });
}

// Fetch notifications on page load
fetchNotifications();