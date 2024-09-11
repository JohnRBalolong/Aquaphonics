<?php
require '../../query/auth.php';  // Assuming this handles authentication

 
$targetDirectory = '../../query/user_directories/' . $folderName . '/';

if (isset($_FILES['fileUpload'])) {
    $targetFile = $targetDirectory . basename($_FILES["fileUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["fileUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit it to 2MB)
    if ($_FILES["fileUpload"]["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if everything is OK before uploading
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $targetFile)) {
            // Redirect back to profile page or display a success message
            header("Location: profile.php");  // Adjust this as needed
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
