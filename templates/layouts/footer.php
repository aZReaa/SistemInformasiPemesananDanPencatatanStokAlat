<footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Sistem Penyewaan Alat Pesta Haqiqah. Semua hak dilindungi.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hakikah/public/js/main.js"></script>
</body>
</html>

<?php
if (isset($_SESSION['old_data'])) {
    unset($_SESSION['old_data']);
}
?>