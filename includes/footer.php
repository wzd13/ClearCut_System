<?php
/**
 * ClearCut — includes/footer.php
 * Shared footer included on every page.
 */
?>
<footer class="footer">
    <div class="container">
        <p>
            &copy; <?php echo date('Y'); ?>
            <strong><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></strong>
            &mdash; AI background removal powered by the
            <a href="https://developer.pixelcut.ai/" target="_blank" rel="noopener noreferrer">Pixelcut API</a>.
        </p>
    </div>
</footer>
