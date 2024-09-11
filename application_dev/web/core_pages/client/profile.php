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
    </style>
</head>
<body>


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
    
    <div class="option">
        <i class="fas fa-lock"></i>
        <span>Change password</span>
    </div>
    
    <div class="option">
        <i class="fas fa-bell"></i>
        <span>Notifications</span>
        <div class="toggle-btn">
            <i class="fas fa-toggle-on"></i><span>ON</span>
        </div>
    </div>
    
    <div class="option">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Alert</span>
        <div class="toggle-btn">
            <i class="fas fa-toggle-on"></i><span>ON</span>
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

</body>
</html>
