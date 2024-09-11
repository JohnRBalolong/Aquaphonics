<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/index.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="box-container">
        <div class="login-box" id="login-box">
            <div class="logo">
                <img src="asset/logo.png" alt="Logo">
                <h2>AQUAPHONICS</h2>
            </div>
            <form id="login-form">
                <input type="email" placeholder="Email" class="input-field" id="username">
                <div class="centerpass"> 
                    <div class="password-wrapper">
                        <input type="password" placeholder="password" class="password-field" id="password">
                        <span class="toggle-password" onclick="togglePassword('password', 'eye-icon')">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </span>
                    </div>
                </div>
                <p class="register">Don't have an account? <span class="regtext" id="registerpage">Register now</span></p>
                <button type="submit" class="login-btn">LOGIN</button>
            </form>
        </div>
        <div class="register-box" id="register-box">
            <div class="logo">
                <img src="asset/logo.png" alt="Logo">
                <h2>REGISTER</h2>
            </div>
            <form id="register-form">
                <input type="text" placeholder="Name" class="input-field" id="name">
                <input type="email" placeholder="Email" class="input-field" id="email">
                <input type="text" placeholder="Phone No." class="input-field" id="phone">
               
                <div class="centerpass" style="margin-top: -4px; margin-bottom: -10px;"> 
                    <div class="password-wrapper">
                        <input type="password" placeholder="Password" class="password-field" id="reg-password">
                        <span class="toggle-password" onclick="togglePassword('reg-password', 'reg-eye-icon')">
                            <i class="fas fa-eye" id="reg-eye-icon"></i>
                        </span>
                    </div>
                </div>
                <div class="centerpass" style="margin-top: -4px; margin-bottom: -4px;"> 
                    <div class="password-wrapper">
                        <input type="password" placeholder="Confirm Password" class="password-field" id="conreg-password">
                        <span class="toggle-password" onclick="togglePassword('conreg-password', 'conreg-eye-icon')">
                            <i class="fas fa-eye" id="conreg-eye-icon"></i>
                        </span>
                    </div>
                </div>
                <input type="text" placeholder="D.O.I.N" class="input-field" id="doin">
                <p class="register">Already have an account? <span class="regtext" id="loginpage">Login</span></p>
               
                <p class="note">Note: Ask your administrator for your Device Ownership Identification Number (DOIN)</p>
                <button type="submit" class="login-btn">REGISTER</button>
            </form>
        </div>
    </div>
    <div class="image-box">
        <img src="asset/inside_image.png" alt="Aquaponics Image">
    </div>
</div>

<div id="message-modal" class="modal">
    <div class="modal-content">
        <p id="modal-message"></p>
    </div>
</div>


<script>
     // Toggle password visibility
     function togglePassword(inputId, iconId) {
        const passwordField = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

<script type="module" src="js/index.js"></script>

</body>
</html>
