

<?php
ob_start(); // Start output buffering
?>
 <link rel="stylesheet" href="../../css/client/tds_level.css">
<div class="container">
    <div class="meter">
        <div class="tdsouter-circle center">
            <div class="inner-circle">
                <div class="tdsneedle center"></div>
            </div>
        </div>
    </div>

    <div class="tdslabel center">
        <span>Level 0</span>
    </div>
</div>
<script type="module" src="../../js/client/tds_level.js"></script>
<?php
$content = ob_get_clean(); // Get the buffered content and clean the buffer
echo $content; // Output the content
?>

