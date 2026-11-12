<?php
// Berkas uji LKM. Jangan diubah. Simpan satu folder dengan buku.php, lalu buka uji.php di browser.
require 'buku.php';

$lolos = 0;
$total = 7;

function cek($nomor, $keterangan, $hasil) {
    global $lolos;
    if ($hasil) {
        $lolos++;
        echo "<p style='color:green'>✔ Uji $nomor lolos: $keterangan</p>";
    } else {
        echo "<p style='color:red'>✘ Uji $nomor belum lolos: $keterangan</p>";
    }
}

cek(1, "class Buku sudah dibuat", class_exists('Buku'));
if (!class_exists('Buku')) { echo "<p>Periksa nama class di buku.php.</p>"; exit; }

try {
    $a = new Buku();
    $b = new Buku();
} catch (Error $e) {
    echo "<p style='color:red'>Class Buku harus bisa dibuat dengan <code>new Buku()</code> tanpa isian. Hapus bagian yang mewajibkan isian saat objek dibuat.</p>";
    exit;
}
$a->judul = "Laskar Pelangi";
$a->penulis = "Andrea Hirata";
$a->dipinjam = false;

$b->judul = "Bumi Manusia";
$b->penulis = "Pramoedya Ananta Toer";
$b->dipinjam = false;

cek(2, "dua objek menyimpan judul masing-masing", $a->judul != $b->judul);

ob_start();
$a->pinjam();
$keluar1 = ob_get_clean();
cek(3, "pinjam() mengubah dipinjam menjadi true", $a->dipinjam === true);

ob_start();
$a->pinjam();
$keluar2 = ob_get_clean();
cek(4, "meminjam buku yang sedang dipinjam menghasilkan pesan berbeda", $keluar1 != $keluar2 && $a->dipinjam === true);

cek(5, "buku B tidak ikut berubah saat buku A dipinjam", $a->dipinjam === true && $b->dipinjam === false);

$a->dipinjam = true;
ob_start();
$a->kembalikan();
ob_end_clean();
cek(6, "kembalikan() mengubah dipinjam menjadi false", $a->dipinjam === false);

ob_start();
$b->lihatStatus();
$status = ob_get_clean();
cek(7, "lihatStatus() menampilkan judul buku", strpos($status, "Bumi Manusia") !== false);

echo "<h3>Hasil: $lolos dari $total uji lolos</h3>";

$atributDasar = ['judul', 'penulis', 'dipinjam'];
$methodDasar = ['pinjam', 'kembalikan', 'lihatStatus'];
$kelas = new ReflectionClass('Buku');
$atributBaru = [];
foreach ($kelas->getProperties() as $p) {
    if (!in_array($p->getName(), $atributDasar)) { $atributBaru[] = $p->getName(); }
}
$methodBaru = [];
foreach ($kelas->getMethods() as $m) {
    if (!in_array($m->getName(), $methodDasar) && $m->getName() != '__construct') { $methodBaru[] = $m->getName() . '()'; }
}
if ($atributBaru || $methodBaru) {
    echo "<h3>★ CHALLENGE</h3>";
    if ($atributBaru) { echo "<p>Atribut tambahan: " . implode(', ', $atributBaru) . "</p>"; }
    if ($methodBaru) { echo "<p>Method tambahan: " . implode(', ', $methodBaru) . "</p>"; }
    echo "<p>Tambahan ini tidak dinilai oleh uji. Pastikan hasil uji di atas tetap lolos semua, lalu tunjukkan tambahanmu ke guru.</p>";
}
