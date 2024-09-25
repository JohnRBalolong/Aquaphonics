

<?php
ob_start(); // Start output buffering
?>
 <link rel="stylesheet" href="../../css/client/water_level.css">
<div class="container">
    <div class="meter">
        <div class="outer-circle center">
            <div class="inner-circle">
                <div class="needle center"></div>
            </div>
        </div>
    </div>

    <div class="label center">
        <span>Level 0</span>
    </div>
    <p class="normal_label">Note: Water level will only notify when reach the rising level of 100% this will closely over flowing pond.</p>
</div>

<script type="module" src="../../js/client/water_level.js"></script>
<?php
$content = ob_get_clean(); // Get the buffered content and clean the buffer
echo $content; // Output the content
?>

