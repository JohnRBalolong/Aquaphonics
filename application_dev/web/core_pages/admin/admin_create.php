<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase User Registration</title>
    <script type="module">
        // Import Firebase modules as needed
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
        import { getAuth, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-auth.js";
        import { getDatabase, ref, set } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

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
        const auth = getAuth();

        // Define the registerUser function in the global scope
        window.registerUser = async function() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
           

            try {
                // Create a new user with email and password
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;

                // Save the user data to the database (including the password)
                await set(ref(db, 'admin/' + user.uid), {
                   
                    email: email,
                    uid: user.uid
                });

                console.log('User registered and data saved successfully!');
                alert('User registered successfully!');
            } catch (error) {
                console.error('Error registering user:', error);
                alert('Error: ' + error.message);
            }
        };
    </script>
</head>
<body>
    <h1>Register User</h1>
    <form onsubmit="event.preventDefault(); registerUser();">
      

        <label for="email">Email:</label>
        <input type="email" id="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
