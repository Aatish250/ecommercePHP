<?php

if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
    ?>
    <script>
        // Ensure setFlashMessage is a JavaScript function available on the page
        // (e.g., defined in a separate JS file or <script> block)
        setFlashMessage("<?php echo $_SESSION['message-status'] ?>", "<?php echo $_SESSION['message'] ?>");
    </script>
    <?php
}
unset($_SESSION['message-status']); 
unset($_SESSION['message']);
?>