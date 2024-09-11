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
      gap: 10px;
    }

    .sensor-wrapper {
      display: flex;
      flex-direction: column;
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
      margin-top: 10px;
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
      cursor: default;
    }

    #sensor-label .back {
      cursor: pointer;
    }

    @media (max-width: 858px){
      .mobile-view {
          padding: 30px !important;
      }
      #notification{
        display: none;
      }
    }
 
    .search-container {
      display: flex;
      align-items: center;
      background-color: #f4f4f4;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-icon {
      width: 32px;
      height: 32px;
      margin-right: 10px; 
    }

    .search-input {
      flex-grow: 1;
      border: none;
      background: none;
      outline: none;
      padding: 5px;
      font-size: 16px;
    }

    .search-icon {
      font-size: 18px;
      cursor: pointer;
      margin-left: 10px;
    }

    .notification_div {
      height: calc(80vh - 70px);
      overflow-y: auto;
      margin-top: 20px;
      background-color: #ffffff;
      padding: 30px;
    }

    .notification_display {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
      background-color: #dbdbdb;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      font-size: 16px;
      color: #333;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      margin-bottom: 10px;
      max-height: 45px;
      overflow: hidden;
    }

    .notification_display.open {
      max-height: none;
      white-space: normal;
    }

    .notification_text {
      flex-grow: 1;
      white-space: nowrap; /* Initially restrict to one line */
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: calc(100% - 70px); /* Adjust this to fit your design */
    }

    .notification_display.open .notification_text {
      white-space: normal; /* Allow wrapping */
      overflow: visible;
      text-overflow: clip;
      max-width: calc(100% - 0px);
    }

    .notification_display:not(.open) .notification_text {
  max-width: calc(100% - 70px); /* Restore the max-width when not open */
}

    .notification_info {
      display: flex;
      align-items: center;
    }

    .notification_time {
      font-size: 12px;
      color: #666;
      margin-right: 10px;
    }

    .notification_dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
    }

    .unread .notification_dot {
      background-color: red !important;
    }

    .read .notification_dot {
      background-color: green !important;
    }

    .notification_display:active {
      transform: scale(0.95); 
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); 
    }

    .full-notification {
      max-height: 60vh; /* Set a maximum height for expanded notifications */
      overflow-y: auto; /* Enable vertical scrolling */
      white-space: normal; /* Allow text wrapping */
      word-wrap: break-word; /* Break long words */
    }

    .back-icon {
      display: none;
      font-size: 18px;
      cursor: pointer;
      margin-bottom: 20px;
    }

    .notification_div.full-view .back-icon {
      display: block;
    }

    .notification_div.full-view .notification_display {
      display: none;
    }

    .notification_div.full-view .notification_display.full-notification {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      max-height: none;
      overflow-y: auto;
      padding: 20px;
      font-size: 18px;
      white-space: normal; /* Allow wrapping */
      word-wrap: break-word; /* Ensure long words break */
    }

    .notification_div.full-view .notification_info {
      display: none; /* Hide time and dot in expanded view */
    }

    span {
      padding: 0 !important;
      font-weight: 100 !important;
      background: none !important;
      border: 0 !important;
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
      <div class="main-content"></div>
      
    </div>

    <!-- Mobile View -->
    <div class="mobile-view">
      <div class="search-container">
        <!-- Left Side: Notification Icon Image -->
        <img src="../../asset/notification.png" alt="Notification Icon" class="notification-icon">
        
        <!-- Search Input Field -->
        <input type="text" placeholder="Search Notification . . . ." class="search-input">
        
        <!-- Right Side: FontAwesome Search Icon -->
        <i class="fas fa-search search-icon"></i>
      </div>

      <!-- Notification List -->
      <div class="notification_div" id="notification_div">
      
       
      </div>
    </div>
  </section>

  <script>
    const doin = "<?php echo $doin; ?>";
  </script>

<script  type="module" src="../../js/client/notifications.js"></script>

</body>
</html>
