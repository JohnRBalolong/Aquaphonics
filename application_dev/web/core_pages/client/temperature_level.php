

<?php
ob_start(); // Start output buffering
?>
 <link rel="stylesheet" href="../../css/client/temperature_level.css">
<div class="container">
    <div class="meter">
        <div class="tempouter-circle center">
            <div class="tempinner-circle">
                <div class="tempneedle center"></div>
            </div>
        </div>
    </div>

    <div class="templabel center">
        <span>Level 0</span>
    </div>
</div>

<script type="module" src="../../js/client/temperature_level.js"></script>
<?php
$content = ob_get_clean(); // Get the buffered content and clean the buffer
echo $content; // Output the content
?>

