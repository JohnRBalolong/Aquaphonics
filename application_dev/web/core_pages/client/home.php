<?php
require '../../query/auth.php';
?>

<!DOCTYPE html>
<!-- Created By CodingNepal - www.codingnepalweb.com -->
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Aquaphonics</title>
  <link rel="stylesheet" href="../../css/client/home.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

  <style>

.sensor-container {
      margin-top: 30px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 10px; /* Space between boxes */
    }

    
    .sensor-wrapper {
      display: flex;
      flex-direction: column; /* Arrange sensor-box and sensor-label in a column */
      flex: 1;
    }

    .sensor-box {
      background-color: #f4f4f4;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      border-radius: 5px;
      justify-content: center;
      display: flex;
    }

    .sensor-box img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 0 auto; /* Center the image horizontally */
}


   
  </style>
</head>
<body>
  <nav>
    <input type="checkbox" id="check">
    <label for="check" class="checkbtn">
      <i class="fas fa-bars"></i>
    </label>
    <img src="../../asset/logo.png" alt="Logo Image" class="logo-img">
    <label class="logo">AQUAPHONICS</label>
    <ul>
      <li><a class="active" href="home.php">Home</a></li>
      <li><a href="#">About</a></li>
      <li id="notification"><a href="notifications.php">Notification</a></li>
      <li><a href="profile.php">Profile Info.</a></li>
    </ul>
  </nav>
  <section>
  <div class="desktop-view">
  <div class="main-content">
            <div class="button-container">
                <a href="sensor.php" class="button sensors">SENSORS</a>
                <a href="report.php" class="button reports">REPORTS</a>
            </div>

        <div class="sensor-container">

        <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor1">
          <img src="../../asset/pond3.jpg" alt="Sensor 1 Image">
        </div>     
      </div>

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor2">
          <img src="../../asset/pond2.jpg" alt="Sensor 2 Image">
        </div>     
      </div>

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor3">
          <img src="../../asset/pond1.png" alt="Sensor 3 Image">
        </div>     
      </div>


        </div>

        <div class="sensor-container">

       

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor3">
          <img src="../../asset/farmland.jpg" alt="Sensor 3 Image">
        </div>     
      </div>


        </div>
      

    
      

        
        </div>
     
        </div>


     <!-- Mobile View -->
     <div class="mobile-view">
        <div class="logo-mobile">
            <img src="../../asset/logo.png" >
        </div>
        <div class="button-container">
            <a href="sensor.php" class="button sensors">
                <img src="../../asset/circuit.png" > SENSORS
            </a>
            <a href="report.php" class="button report">
                <img src="../../asset/report.png" > REPORT DATA
            </a>
            <a href="notifications.php" class="button notification">
                <img src="../../asset/bell.jpg" > NOTIFICATION
            </a>
            
            <a href="../../query/logout.php" class="button exit">
                <img src="../../asset/exit.jpg" > Exit
            </a>
        </div>
    </div>
  </section>
</body>
</html>
