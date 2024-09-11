<?php
require '../../query/auth.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Aquaphonics</title>
  <link rel="stylesheet" href="../../css/client/sensor.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <style>
    .desktop-view .sensor-container {
      margin-top: 40px;
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

    .sensor-label {
      text-align: center;
      margin-top: 10px; /* Space between box and label */
      font-size: 14px;
      color: #333;
    }

    .sensor-label span {
      display: block;
      font-weight: bold;
    }

   
    .mobile-view .button-container.hidden {
    display: none;
  }

  #sensor-label span {
    cursor: default; /* Default cursor for SENSORS */
  }

  #sensor-label .back {
    cursor: pointer; /* Pointer cursor for BACK */
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
      <li><a href="home.php">Home</a></li>
      <li><a href="#">About</a></li>
      <li id="notification"><a href="notifications.php">Notification</a></li>
      <li><a href="profile.php">Profile Info.</a></li>
    </ul>
  </nav>

  <section>
  <div class="desktop-view">
  <div class="main-content">
    <div class="sensorlebel">
      <span>SENSORS</span>
    </div>

    <div class="sensor-container">
      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor1">
          <?php include 'water_level.php'; ?>
        </div>
        <div class="sensor-label">
          <span class="water_text">Water Level</span>
        </div>
      </div>

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor2">
          <?php include 'tds_level.php'; ?>
        </div>
        <div class="sensor-label">
          <span class="tds_text">TDS Level</span>
        </div>
      </div>

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor3">
          <?php include 'ph_level.php'; ?>
        </div>
        <div class="sensor-label">
          <span class="ph_text">pH Level</span>
        </div>
      </div>

      <div class="sensor-wrapper">
        <div class="sensor-box" id="sensor4">
          <?php include 'temperature_level.php'; ?>
        </div>
        <div class="sensor-label">
          <span class="temp_text">Temperature</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Mobile View -->
<div class="mobile-view">
  <br><br>
  <div class="sensorlebel" id="sensor-label">
    <span>SENSORS</span>
  </div>
  <div class="button-container" id="button-container">
    <a href="mobile_sensor.php?sensor=water_level" class="button sensors">
      <img src="../../asset/water-level.png"> WATER LEVEL
    </a>
    <a href="mobile_sensor.php?sensor=tds_level" class="button notification">
      <img src="../../asset/tds-level.png"> TDS LEVEL
    </a>
    <a href="mobile_sensor.php?sensor=ph_level" class="button exit">
      <img src="../../asset/phmeter.png"> PH LEVEL
    </a>
    <a href="mobile_sensor.php?sensor=temperature" class="button sensors">
      <img src="../../asset/temperature.png"> TEMPERATURE
    </a>
  </div>
</div>

  </section>

  <!-- Pass PHP variable to JavaScript -->
<script>
    const doin = "<?php echo $doin; ?>"; // Embed the PHP variable into JavaScript
</script>

</body>
</html>