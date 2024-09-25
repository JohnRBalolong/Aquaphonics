

<?php
ob_start(); // Start output buffering
?>
 <link rel="stylesheet" href="../../css/client/ph_level.css">
<div class="container">
    <div class="meter">
        <div class="phouter-circle center">
            <div class="phinner-circle">
                <div class="phneedle center"></div>
            </div>
        </div>
    </div>

    <div class="phlabel center">
        <span class="phlavel">Level 0</span>
    </div>
    <p class="normal_label">Note: Fresh water fish needs PH normal level of 6.5pH to 8.5pH (potential of hydrogen)</p>
</div>
<!-- Pass PHP variable to JavaScript -->

<script  type="module" src="../../js/client/ph_level.js"></script>
<?php
$content = ob_get_clean(); // Get the buffered content and clean the buffer
echo $content; // Output the content
?>

