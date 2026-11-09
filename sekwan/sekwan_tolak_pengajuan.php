
<?php

session_start();

require_once '../config/db.php';


/* =====================================================
   VALIDASI SESSION
===================================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'sekwan'
) {
    http_response_code(403);
    exit('Akses ditolak.');
}


/* =====================================================
   VALIDASI METHOD
===================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}


/* =====================================================
   AMBIL DATA POST
===================================================== */

$id = $_POST['id'] ?? '';

$alasan = trim($_POST['alasan'] ?? '');


/* =====================================================
   VALIDASI INPUT
===================================================== */

if (
    !is_string($id) ||
    !ctype_digit($id) ||
    (int)$id <= 0
) {
    http_response_code(400);
    exit('ID pengajuan tidak valid.');
}

if ($alasan === '') {
    http_response_code(400);
    exit('Alasan penolakan wajib diisi.');
}


/* =====================================================
   PROSES PENOLAKAN
===================================================== */

try {

    $pdo->beginTransaction();

    /*
       Pastikan pengajuan masih berstatus draft_sppd.
       FOR UPDATE mencegah perubahan bersamaan selama
       transaksi berlangsung.
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM pengajuan
        WHERE id = ?
          AND status = 'draft_sppd'
        FOR UPDATE
    ");

    $stmt->execute([
        (int)$id
    ]);

    $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pengajuan) {

        $pdo->rollBack();

        http_response_code(409);

        exit('Pengajuan tidak ditemukan atau statusnya sudah berubah.');

    }


    /*
       Ubah status dan simpan alasan penolakan.
    */

    $stmt_update = $pdo->prepare("
        UPDATE pengajuan
        SET
            status = 'ditolak',
            alasan_penolakan = ?
        WHERE
            id = ?
            AND status = 'draft_sppd'
    ");

    $stmt_update->execute([
        $alasan,
        (int)$id
    ]);


    if ($stmt_update->rowCount() !== 1) {

        $pdo->rollBack();

        http_response_code(409);

        exit('Pengajuan gagal ditolak atau status sudah berubah.');

    }


    $pdo->commit();

    echo 'OK';


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'Kesalahan penolakan pengajuan Sekwan: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit('Terjadi kesalahan database saat menolak pengajuan.');

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'Kesalahan sistem penolakan Sekwan: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit('Terjadi kesalahan sistem.');

}
