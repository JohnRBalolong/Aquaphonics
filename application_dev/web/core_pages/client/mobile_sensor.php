<?php
require '../../query/auth.php';
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : '';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Aquaphonics</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/client/mobile_sensor.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
  <nav>
    <img src="../../asset/logo.png" alt="Logo Image" class="logo-img">
    <label class="logo">AQUAPHONICS</label>
  </nav>

  <section>
    <!-- Mobile View -->
    <div class="mobile-view">
      <br><br>
      <div class="sensorlebel" id="sensor-label">
        <a href="sensor.php"><span>BACK</span></a>
      </div>
      <div class="button-container" id="button-container">
        <?php if ($sensor == 'water_level'): ?>
          <a  class="button sensors">
            <img src="../../asset/water-level.png"> WATER LEVEL
          </a>
        <?php elseif ($sensor == 'tds_level'): ?>
          <a  class="button notification">
            <img src="../../asset/tds-level.png"> TDS LEVEL
          </a>
        <?php elseif ($sensor == 'ph_level'): ?>
          <a  class="button exit">
            <img src="../../asset/phmeter.png"> PH LEVEL
          </a>
        <?php elseif ($sensor == 'temperature'): ?>
          <a  class="button sensors">
            <img src="../../asset/temperature.png"> TEMPERATURE
          </a>
        <?php else: ?>
          <p>Invalid sensor selection.</p>
        <?php endif; ?>
      </div>

      <div class="center_gauge">
      <div class="sensor-container">
        <?php if ($sensor == 'water_level'): ?>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor1">
              <?php include 'water_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="water_text">Water Level</span>
            </div>
          </div>
        <?php elseif ($sensor == 'tds_level'): ?>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor2">
              <?php include 'tds_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="tds_text">TDS Level</span>
            </div>
          </div>
        <?php elseif ($sensor == 'ph_level'): ?>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor3">
              <?php include 'ph_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="ph_text">pH Level</span>
            </div>
          </div>
        <?php elseif ($sensor == 'temperature'): ?>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor4">
              <?php include 'temperature_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="temp_text">Temperature</span>
            </div>
          </div>
        <?php endif; ?>
      </div>
      </div>

    </div>
  </section>

    <!-- Pass PHP variable to JavaScript -->
<script>
    const doin = "<?php echo $doin; ?>"; // Embed the PHP variable into JavaScript
</script>
</body>
</html>
