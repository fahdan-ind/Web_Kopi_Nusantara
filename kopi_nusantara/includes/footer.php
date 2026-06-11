<?php // includes/footer.php ?>
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-col">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                     viewBox="0 0 24 24" fill="none" stroke="#C4863A"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
                    <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/>
                    <line x1="6" y1="2" x2="6" y2="4"/>
                    <line x1="10" y1="2" x2="10" y2="4"/>
                    <line x1="14" y1="2" x2="14" y2="4"/>
                </svg>
                <h3 style="margin:0;">Kopi Nusantara</h3>
            </div>
            <p>Platform marketplace kopi premium langsung dari agen terpercaya di seluruh Indonesia.</p>
        </div>
        <div class="footer-col">
            <h4>Kategori</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Arabica">Arabica</a></li>
                <li><a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Robusta">Robusta</a></li>
                <li><a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Liberika">Liberika</a></li>
                <li><a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Excelsa">Excelsa</a></li>
                <li><a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Blend">Paket Gift</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Kontak</h4>
            <ul>
                <li>0800-KOPI-ID</li>
                <li>info@kopinusantara.id</li>
                <li>Jakarta Selatan</li>
            </ul>
        </div>
    </div>
    <p class="footer-copy">&copy; <?= date('Y') ?> Kopi Nusantara. All rights reserved.</p>
</footer>
</body>
</html>