<?php
// includes/footer.php
// Global footer with live clock and attribution
?>

<!-- ============================================================
     FOOTER
     ============================================================ -->

<footer class="bg-white border-top mt-4 py-2">

    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- LEFT: REAL-TIME CLOCK -->
        <div id="footer_clock" class="small text-muted">
            Loading time...
        </div>

        <!-- CENTER: COPYRIGHT / AUTHOR -->
        <div class="small text-center text-muted flex-grow-1">
            Created by: <strong>Louis van Rooyen</strong>
        </div>

        <!-- RIGHT: OPTIONAL (EMPTY / FUTURE USE) -->
                <div class="small text-center text-muted flex-grow-1">
            Vibe AI Technology used for code generation
        </div>

    </div>

</footer>

<!-- ============================================================
     JS (LOAD LAST)
     ============================================================ -->

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============================================================
     FOOTER SCRIPT
     ============================================================ -->

<script>
(function() {

    function updateClock() {
        const now = new Date();

        const formatted = now.toLocaleString(undefined, {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const el = document.getElementById('footer_clock');
        if (el) el.textContent = formatted;
    }

    // Initial call
    updateClock();

    // Update every second
    setInterval(updateClock, 1000);

})();
</script>

</body>
</html>