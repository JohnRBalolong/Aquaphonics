<?php
require '../../query/auth.php';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Aquaphonics</title>
  <link rel="stylesheet" href="../../css/client/report.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


  <script type="module">


  google.charts.load('current', {'packages':['line']});
  google.charts.setOnLoadCallback(drawCharts);

  function drawCharts() {
    drawWaterLevelChart();
    drawTDSLevelChart();
    drawPHLevelChart();
    drawTemperatureChart();
  }

  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-database.js";

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




// ################################# getWaterLevelData
// ################################# getWaterLevelData
// ################################# getWaterLevelData
// ################################# getWaterLevelData

function getWaterLevelData(doin, callback) {
  const waterLevelRef = ref(db, `water_level/${doin}`);
  
  onValue(waterLevelRef, (snapshot) => {
    const data = snapshot.val();
    let dailyData = {};

    // Process the data to group by day and find the min/max
    Object.keys(data).forEach((timestamp) => {
      const entry = data[timestamp];
      const date = new Date(entry.date);
      const day = date.getDate(); // Extract day

      if (entry.water_level !== 0) { // Ignore 0 values
        if (!dailyData[day]) {
          dailyData[day] = { min: Infinity, max: -Infinity };
        }
        dailyData[day].min = Math.min(dailyData[day].min, entry.water_level || 1); // Ignore 0, use 1 as lowest
        dailyData[day].max = Math.max(dailyData[day].max, entry.water_level);
      }
    });

    callback(dailyData);
  });
}


function drawWaterLevelChart(doin) {
  getWaterLevelData(doin, (dailyData) => {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Day');
    data.addColumn('number', 'Low Water Level');
    data.addColumn('number', 'High Water Level');

    let today = new Date();
    
    // Fill data for the past 7 days, or use 0 if no data available
    for (let i = 6; i >= 0; i--) {
      let day = new Date(today);
      day.setDate(today.getDate() - i);
      const dayNum = day.getDate().toString(); // Get day of the month as string

      let low = dailyData[dayNum]?.min || 0;
      let high = dailyData[dayNum]?.max || 0;

      data.addRows([[dayNum, low, high]]);
    }

    const options = {
      chart: {
        title: 'Water Level 7 Days Report',
        subtitle: 'in percentage (0-100%)'
      },
      width: '100%',
      height: '100%',
      backgroundColor: { fill: 'transparent' },
      vAxis: { minValue: 0, maxValue: 100 },
      hAxis: { title: 'Day', textStyle: { fontSize: 12, bold: true } },
      series: { 0: { color: 'red' }, 1: { color: 'blue' } }
    };

    const chart = new google.charts.Line(document.getElementById('water_level_chart'));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
}



// ################################# getWaterLevelData
// ################################# getWaterLevelData
// ################################# getWaterLevelData




// ################################# getTDSLevelData
// ################################# getTDSLevelData
// ################################# getTDSLevelData

function getTDSLevelData(doin, callback) {
  const tdsLevelRef = ref(db, `tds_level/${doin}`);
  
  onValue(tdsLevelRef, (snapshot) => {
    const data = snapshot.val();
    let dailyData = {};

    Object.keys(data).forEach((timestamp) => {
      const entry = data[timestamp];
      const date = new Date(entry.date);
      const day = date.getDate();

      if (entry.tds !== 0) { // Ignore 0 values
        if (!dailyData[day]) {
          dailyData[day] = { min: Infinity, max: -Infinity };
        }

        dailyData[day].min = Math.min(dailyData[day].min, entry.tds || 1); // Ignore 0, use 1 as lowest
        dailyData[day].max = Math.max(dailyData[day].max, entry.tds);
      }
    });

    callback(dailyData);
  });
}


function drawTDSLevelChart(doin) {
  getTDSLevelData(doin, (dailyData) => {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Day');
    data.addColumn('number', 'Low TDS Level');
    data.addColumn('number', 'High TDS Level');

    let today = new Date();

    for (let i = 6; i >= 0; i--) {
      let day = new Date(today);
      day.setDate(today.getDate() - i);
      const dayNum = day.getDate().toString();

      let low = dailyData[dayNum]?.min || 0;
      let high = dailyData[dayNum]?.max || 0;

      data.addRows([[dayNum, low, high]]);
    }

    const options = {
      chart: {
        title: 'TDS Level 7 Days Report',
        subtitle: 'in ppm (0-1000 ppm)'
      },
      width: '100%',
      height: '100%',
      backgroundColor: { fill: 'transparent' },
      vAxis: { minValue: 0, maxValue: 1000 },
      hAxis: { title: 'Day', textStyle: { fontSize: 12, bold: true } },
      series: { 0: { color: 'red' }, 1: { color: 'blue' } }
    };

    const chart = new google.charts.Line(document.getElementById('tds_level_chart'));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
}

// ################################# getTDSLevelData
// ################################# getTDSLevelData
// ################################# getTDSLevelData




// ################################# getPHLevelData
// ################################# getPHLevelData
// ################################# getPHLevelData

function getPHLevelData(doin, callback) {
  const phLevelRef = ref(db, `ph_level/${doin}`);
  
  onValue(phLevelRef, (snapshot) => {
    const data = snapshot.val();
    let dailyData = {};

    Object.keys(data).forEach((timestamp) => {
      const entry = data[timestamp];
      const date = new Date(entry.date);
      const day = date.getDate();

      if (entry.ph !== 0) { // Ignore 0 values
        if (!dailyData[day]) {
          dailyData[day] = { min: Infinity, max: -Infinity };
        }

        dailyData[day].min = Math.min(dailyData[day].min, entry.ph || 1); // Ignore 0, use 1 as lowest
        dailyData[day].max = Math.max(dailyData[day].max, entry.ph);
      }
    });

    callback(dailyData);
  });
}


function drawPHLevelChart(doin) {
  getPHLevelData(doin, (dailyData) => {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Day');
    data.addColumn('number', 'Low pH Level');
    data.addColumn('number', 'High pH Level');

    let today = new Date();

    for (let i = 6; i >= 0; i--) {
      let day = new Date(today);
      day.setDate(today.getDate() - i);
      const dayNum = day.getDate().toString();

      let low = dailyData[dayNum]?.min || 0;
      let high = dailyData[dayNum]?.max || 0;

      data.addRows([[dayNum, low, high]]);
    }

    const options = {
      chart: {
        title: 'pH Level 7 Days Report',
        subtitle: 'pH Scale (1-14)'
      },
      width: '100%',
      height: '100%',
      backgroundColor: { fill: 'transparent' },
      vAxis: { minValue: 1, maxValue: 14 },
      hAxis: { title: 'Day', textStyle: { fontSize: 12, bold: true } },
      series: { 0: { color: 'red' }, 1: { color: 'blue' } }
    };

    const chart = new google.charts.Line(document.getElementById('ph_level_chart'));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
}

// ################################# getPHLevelData
// ################################# getPHLevelData
// ################################# getPHLevelData



// ################################# getTemperatureData
// ################################# getTemperatureData
// ################################# getTemperatureData

function getTemperatureData(doin, callback) {
  const temperatureRef = ref(db, `temperature/${doin}`);
  
  onValue(temperatureRef, (snapshot) => {
    const data = snapshot.val();
    let dailyData = {};

    // Process the data to group by day and find the min/max
    Object.keys(data).forEach((timestamp) => {
      const entry = data[timestamp];
      const date = new Date(entry.date);
      const day = date.getDate(); // Extract day

      if (entry.temperature !== 0) { // Ignore 0 values
        if (!dailyData[day]) {
          dailyData[day] = { min: Infinity, max: -Infinity };
        }
        
        dailyData[day].min = Math.min(dailyData[day].min, entry.temperature || 1); // Ignore 0, use 1 as lowest
        dailyData[day].max = Math.max(dailyData[day].max, entry.temperature);
      }
    });

    callback(dailyData);
  });
}


function drawTemperatureChart(doin) {
  getTemperatureData(doin, (dailyData) => {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Day');
    data.addColumn('number', 'Low Temperature');
    data.addColumn('number', 'High Temperature');

    let today = new Date();
    
    // Fill data for the past 7 days, or use 0 if no data available
    for (let i = 6; i >= 0; i--) {
      let day = new Date(today);
      day.setDate(today.getDate() - i);
      const dayNum = day.getDate().toString(); // Get day of the month as string

      let low = dailyData[dayNum]?.min || 0;
      let high = dailyData[dayNum]?.max || 0;

      data.addRows([[dayNum, low, high]]);
    }

    const options = {
      chart: {
        title: 'Temperature 7 Days Report',
        subtitle: 'in Celsius (0-60°C)'
      },
      width: '100%',
      height: '100%',
      backgroundColor: { fill: 'transparent' },
      vAxis: { minValue: 0, maxValue: 60 },
      hAxis: { title: 'Day', textStyle: { fontSize: 12, bold: true } },
      series: { 0: { color: 'red' }, 1: { color: 'blue' } }
    };

    const chart = new google.charts.Line(document.getElementById('temperature_chart'));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
}

// ################################# getTemperatureData
// ################################# getTemperatureData
// ################################# getTemperatureData


google.charts.load('current', { packages: ['line'] });
google.charts.setOnLoadCallback(() => {
  drawWaterLevelChart(doin);
  drawPHLevelChart(doin);
  drawTDSLevelChart(doin);
  drawTemperatureChart(doin);  // Assuming temperature data is static for now
});


  // Redraw charts when the window is resized
  window.addEventListener('resize', drawCharts);
</script>



<style>
    /* Add this to your CSS file or in a <style> tag */
.sensor-container {
  width: 100%;
  margin-bottom: 20px;
}

.sensor-wrapper {
  width: 100%;
}

.sensor-box {
  width: 100%;
  height: 300px; /* Adjust as needed */
}

@media (max-width: 768px) {
  .sensor-box {
    height: 250px; /* Adjust for smaller screens */
  }
  #notification{
    display: none;
  }
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
          <span class="report_text">Sensor's Data Report</span>
        </div>

        <!-- Water Level -->
        <div class="sensor-container">
          <div class="sensor-wrapper">
            <div class="sensor-box" id="water_level_chart">
              <!-- Water Level Chart -->
            </div>
            <div class="sensor-label">
              <span class="water_text">Water Level 7 days Report Data</span>
            </div>
          </div>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor4">
              <?php include 'water_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="water_text">Current Water Level</span>
            </div>
          </div>
        </div>

        <!-- TDS Level -->
        <div class="sensor-container">
          <div class="sensor-wrapper">
            <div class="sensor-box" id="tds_level_chart">
              <!-- TDS Level Chart -->
            </div>
            <div class="sensor-label">
              <span class="tds_text">TDS Level 7 days Report Data</span>
            </div>
          </div>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor4">
              <?php include 'tds_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="tds_text">Current TDS Level</span>
            </div>
          </div>
        </div>

        <!-- pH Level -->
        <div class="sensor-container">
          <div class="sensor-wrapper">
            <div class="sensor-box" id="ph_level_chart">
              <!-- pH Level Chart -->
            </div>
            <div class="sensor-label">
              <span class="ph_text">pH Level 7 days Report Data</span>
            </div>
          </div>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor4">
              <?php include 'ph_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="ph_text">Current pH Level</span>
            </div>
          </div>
        </div>

        <!-- Temperature Level -->
        <div class="sensor-container">
          <div class="sensor-wrapper">
            <div class="sensor-box" id="temperature_chart">
              <!-- Temperature Chart -->
            </div>
            <div class="sensor-label">
              <span class="temp_text">Temperature 7 days Report Data</span>
            </div>
          </div>
          <div class="sensor-wrapper">
            <div class="sensor-box" id="sensor4">
              <?php include 'temperature_level.php'; ?>
            </div>
            <div class="sensor-label">
              <span class="temp_text">Current Temperature</span>
            </div>
          </div>
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
