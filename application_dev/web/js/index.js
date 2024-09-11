  // Import Firebase modules as needed
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
  import { getAuth, createUserWithEmailAndPassword, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-auth.js";
  import { getDatabase, ref, get, set } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

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

  function showModal(message) {
      const modal = document.getElementById('message-modal');
      const modalMessage = document.getElementById('modal-message');

      modalMessage.textContent = message;
      modal.style.display = 'block';

      setTimeout(() => {
          modal.style.display = 'none';
      }, 3000); // Hide after 2 seconds
  }

  // Replace alert calls with showModal function
  document.getElementById('register-form').addEventListener('submit', function(e) {
      e.preventDefault();

      // Get input values
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;

      const password = document.getElementById('reg-password').value;
      const confirmPassword = document.getElementById('conreg-password').value;
      const doin = document.getElementById('doin').value;

      // Clear previous error states
      document.querySelectorAll('.input-field').forEach(field => field.classList.remove('invalid-input'));

      // Validate fields
      let isValid = true;

      if (!name) {
          document.getElementById('name').classList.add('invalid-input');
          isValid = false;
      }
      if (!email) {
          document.getElementById('email').classList.add('invalid-input');
          isValid = false;
      }
      if (!phone) {
          document.getElementById('phone').classList.add('invalid-input');
          isValid = false;
      }

      if (!password) {
          document.getElementById('reg-password').classList.add('invalid-input');
          isValid = false;
      }
      if (!confirmPassword) {
          document.getElementById('conreg-password').classList.add('invalid-input');
          isValid = false;
      }
      if (!doin) {
          document.getElementById('doin').classList.add('invalid-input');
          isValid = false;
      }

      if (!isValid) {
          return;
      }

      if (password !== confirmPassword) {
          document.getElementById('reg-password').classList.add('invalid-input');
          document.getElementById('conreg-password').classList.add('invalid-input');
          isValid = false;
      } else {
          document.getElementById('reg-password').classList.remove('invalid-input');
          document.getElementById('conreg-password').classList.remove('invalid-input');
      }

      // Reference to the DOIN in the database
      const doinRef = ref(db, 'devices/' + doin);

      // Check if the DOIN exists and is available
      get(doinRef).then((snapshot) => {
          if (snapshot.exists()) {
              // DOIN exists, check if it's already in use
              if (snapshot.val().owner) {
                  showModal('This DOIN is already registered to an owner. Please contact the administrator.');
              } else {
                  // Function to sanitize the owner's name for folder creation
                  function sanitizeFolderName(name) {
                      return name.replace(/[^a-zA-Z0-9_-]/g, ''); // Remove special characters
                  }

                  // DOIN is available, proceed with registration
                  createUserWithEmailAndPassword(auth, email, password)
                      .then((userCredential) => {
                          const user = userCredential.user;

                          // Sanitize the owner's name for the folder
                          const folderName = sanitizeFolderName(name);

                          // After successful registration, save additional user info in the database
                          set(doinRef, {
                              owner: name,
                              email: email,
                              phone: phone,
                              uid: user.uid, // Store the unique user ID
                              folderName: folderName // Add sanitized folder name
                          }).then(() => {
                              // After saving user info, create a local directory
                              fetch('query/create_directory.php', {
                                      method: 'POST',
                                      headers: {
                                          'Content-Type': 'application/json'
                                      },
                                      body: JSON.stringify({
                                          ownerName: folderName // Send sanitized folder name
                                      })
                                  }).then(response => response.json())
                                  .then(data => {
                                      if (data.success) {
                                          showModal('Registration successful! Directory created.');
                                      } else {
                                          showModal('Registration successful, but failed to create directory.');
                                      }
                                  });

                              // Clear the input fields
                              document.getElementById('name').value = '';
                              document.getElementById('email').value = '';
                              document.getElementById('phone').value = '';
                              document.getElementById('reg-password').value = '';
                              document.getElementById('conreg-password').value = '';
                              document.getElementById('doin').value = '';

                              // After successful registration, switch to the login form
                              document.getElementById('register-box').style.display = 'none';
                              document.getElementById('login-box').style.display = 'flex';
                          }).catch((error) => {
                              console.error('Error writing to Firebase:', error);
                              showModal('Registration failed. Please try again.');
                          });
                      })
                      .catch((error) => {
                          console.error('Error during registration:', error.message);
                          if (error.code === 'auth/email-already-in-use') {
                              showModal('The email address is already in use. Please try a different email.');
                          } else {
                              showModal('Registration failed: ' + error.message);
                          }
                      });
              }
          } else {
              // DOIN does not exist
              showModal('Invalid D.O.I.N. Please check the number and try again. Please contact the administrator.');
          }
      }).catch((error) => {
          console.error('Error reading DOIN from Firebase:', error);
          showModal('Error reading DOIN. Please try again later.');
      });
  });



  document.getElementById('registerpage').addEventListener('click', function() {
      document.getElementById('login-box').style.display = 'none';
      document.getElementById('register-box').style.display = 'flex';
  });

  document.getElementById('loginpage').addEventListener('click', function() {
      document.getElementById('register-box').style.display = 'none';
      document.getElementById('login-box').style.display = 'flex';
  });



  document.getElementById('login-form').addEventListener('submit', function(e) {
      e.preventDefault();

      // Get input values
      const email = document.querySelector('.input-field[placeholder="Email"]').value;
      const password = document.getElementById('password').value;

      // Clear previous error states
      document.querySelectorAll('.input-field').forEach(field => field.classList.remove('invalid-input'));

      // Validate fields
      if (!email || !password) {
          if (!email) document.querySelector('.input-field[placeholder="Email"]').classList.add('invalid-input');
          if (!password) document.getElementById('password').classList.add('invalid-input');
          return;
      }

      // Sign in with Firebase Authentication
      signInWithEmailAndPassword(auth, email, password)
          .then((userCredential) => {
              const user = userCredential.user;
              const uid = user.uid;

              // Check if the user is in the admin section
              const adminRef = ref(db, '/admin');
              get(adminRef).then((snapshot) => {
                  let isAdmin = false;

                  snapshot.forEach((childSnapshot) => {
                      const adminData = childSnapshot.val();
                      if (adminData.uid === uid) {
                          isAdmin = true;
                          // Send admin data to PHP for session storage
                          fetch('query/session_handler.php', {
                                  method: 'POST',
                                  headers: {
                                      'Content-Type': 'application/json'
                                  },
                                  body: JSON.stringify({
                                      uid: adminData.uid,
                                      email: adminData.email,
                                      isAdmin: true // Indicate that this user is an admin
                                  })
                              })
                              .then(response => response.json())
                              .then((responseData) => {
                                  if (responseData.success) {
                                      // Redirect to admin dashboard
                                      window.location.href = 'core_pages/admin/dashboard.php';
                                  } else {
                                      showModal('Failed to create session. Please try again.');
                                  }
                              })
                              .catch(error => {
                                  console.error('Error during session creation:', error);
                                  showModal('Session creation failed. Please try again.');
                              });
                      }
                  });

                  // If not admin, check devices
                  if (!isAdmin) {
                      const deviceRef = ref(db, '/devices');
                      get(deviceRef).then((deviceSnapshot) => {
                          let deviceData = null;

                          deviceSnapshot.forEach((childSnapshot) => {
                              const data = childSnapshot.val(); // Get the data from the snapshot

                              if (data.uid === uid) {
                                  deviceData = data;

                                  // Send user data to PHP for session storage
                                  fetch('query/session_handler.php', {
                                          method: 'POST',
                                          headers: {
                                              'Content-Type': 'application/json'
                                          },
                                          body: JSON.stringify({
                                              uid: data.uid,
                                              email: data.email,
                                              folderName: data.folderName,
                                              owner: data.owner,
                                              phone: data.phone,
                                              doin: childSnapshot.key
                                          })
                                      })
                                      .then(response => response.json())
                                      .then((responseData) => {
                                          if (responseData.success) {
                                              // Redirect to client dashboard
                                              window.location.href = 'core_pages/client/home.php';
                                          } else {
                                              showModal('Failed to create session. Please try again.');
                                          }
                                      })
                                      .catch(error => {
                                          console.error('Error during session creation:', error);
                                          showModal('Session creation failed. Please try again.');
                                      });
                              }
                          });

                          if (!deviceData) {
                              showModal('You do not have access to any dashboards. Please contact an administrator.');
                          }
                      });
                  }
              }).catch((error) => {
                  console.error('Error reading admin data:', error);
                  showModal('Error retrieving admin data. Please try again later.');
              });
          })
          .catch((error) => {
              console.error('Error during login:', error);
              showModal('Invalid email or password. Please try again.');
          });
  });