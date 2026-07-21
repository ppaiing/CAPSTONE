    </div><!-- end .content -->
    <footer style="background:#fff;border-top:1px solid var(--border);padding:14px 28px;font-size:12px;color:var(--text-muted);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <span>© <?= date('Y') ?> <strong style="color:var(--primary)">HMIF-FT UNISMUH</strong> — Fakultas Teknik UNISMUH Makassar</span>
        <span>Sistem Administrasi Persuratan v1.0</span>
    </footer>
</div><!-- end .main -->

<script>
// Close sidebar on outside click (mobile)
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (sidebar && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && (!toggle || !toggle.contains(e.target))) {
            sidebar.classList.remove('open');
        }
    }
});
</script>
</body>
</html>
