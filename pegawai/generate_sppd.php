<?php

session_start();

include '../config/db.php';

if (!isset($_GET['id'])) {
    die('ID invalid');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT
        p.*,
        u.nama AS nama_pegawai,
        u.jabatan
    FROM pengajuan p
    JOIN users u ON p.pegawai_id = u.id
    WHERE p.id = ?
      AND p.status = 'selesai'
");

$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Data pengajuan belum selesai atau tidak ditemukan.');
}


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


function hitungLama($start, $end)
{
    if (empty($start) || empty($end)) {
        return 1;
    }

    $startTime = strtotime($start);
    $endTime = strtotime($end);

    if (!$startTime || !$endTime) {
        return 1;
    }

    $diff = $endTime - $startTime;

    return max(1, round($diff / 86400) + 1);
}


function tanggalIndonesia($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $timestamp = strtotime($tanggal);

    if (!$timestamp) {
        return '-';
    }

    $hari = date('d', $timestamp);
    $bulanNama = $bulan[(int) date('m', $timestamp)];
    $tahun = date('Y', $timestamp);

    return $hari . ' ' . $bulanNama . ' ' . $tahun;
}


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
|
| Logo dibaca langsung dari server dan dimasukkan ke HTML
| menggunakan Base64.
|
| Dengan cara ini logo tidak bergantung pada relative URL
| ketika browser melakukan proses print.
|
*/

$logoFile = __DIR__ . '/logo.png';

/*
| Jika generate_sppd.php tidak berada satu folder dengan logo.png,
| coba lokasi ../pegawai/logo.png
*/

if (!file_exists($logoFile)) {
    $logoFile = __DIR__ . '/../pegawai/logo.png';
}

if (!file_exists($logoFile)) {
    die(
        'Logo tidak ditemukan.<br><br>' .
        'Lokasi yang diperiksa:<br>' .
        e(__DIR__ . '/logo.png') . '<br>' .
        e(__DIR__ . '/../pegawai/logo.png')
    );
}

$logoMime = mime_content_type($logoFile);

if (!$logoMime) {
    $logoMime = 'image/png';
}

$logoBase64 = base64_encode(
    file_get_contents($logoFile)
);

$logoDataUri = 'data:' . $logoMime . ';base64,' . $logoBase64;


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$namaPegawai = $data['nama_pegawai'] ?? '-';
$jabatan = $data['jabatan'] ?? '-';
$pangkat = $data['pangkat'] ?? '-';
$tujuan = $data['tujuan'] ?? '-';
$alasan = $data['alasan'] ?? '-';

$sptNo = $data['spt_no'] ?? '-';
$spdNo = $data['spd_no'] ?? '-';

$tingkatBiaya = $data['tingkat_biaya'] ?? '-';
$alatAngkutan = $data['alat_angkutan'] ?? '-';
$pengikut = $data['pengikut'] ?? '-';

$instansi = $data['instansi_anggaran'] ?? '-';
$akun = $data['akun_anggaran'] ?? '-';

$tanggalBerangkat = tanggalIndonesia(
    $data['tanggal_berangkat'] ?? null
);

$tanggalKembali = tanggalIndonesia(
    $data['tanggal_kembali'] ?? null
);

$tanggalTerbit = $tanggalBerangkat;

$lamaPerjalanan = hitungLama(
    $data['tanggal_berangkat'] ?? null,
    $data['tanggal_kembali'] ?? null
);


/*
|--------------------------------------------------------------------------
| PLACEHOLDER
|--------------------------------------------------------------------------
*/

$replacements = [

    '{{LOGO_DATA_URI}}' =>
        $logoDataUri,

    '{{SPT_NO}}' =>
        e($sptNo),

    '{{NAMA}}' =>
        e($namaPegawai),

    '{{JABATAN}}' =>
        e($jabatan),

    '{{ALASAN}}' =>
        e($alasan),

    '{{TGL_TERBIT}}' =>
        e($tanggalTerbit),

    '{{SIGNER_NAMA}}' =>
        'ARKAM SUPU, S.Th.I., M.H',

    '{{SIGNER_JABATAN}}' =>
        'Ketua DPRD Kabupaten Banggai Kepulauan',

    '{{SPD_NAMA}}' =>
        e($namaPegawai),

    '{{SPD_PANGKAT}}' =>
        e($pangkat),

    '{{SPD_JABATAN_SPD}}' =>
        e($jabatan),

    '{{SPD_TINGKAT_BIAYA}}' =>
        e($tingkatBiaya),

    '{{SPD_MAKSUD}}' =>
        e($alasan),

    '{{SPD_ALAT_ANGKUTAN}}' =>
        e($alatAngkutan),

    '{{SPD_BERANGKAT}}' =>
        'Salakan',

    '{{SPD_TUJUAN}}' =>
        e($tujuan),

    '{{SPD_LAMANYA}}' =>
        e($lamaPerjalanan . ' Hari'),

    '{{SPD_TGL_BERANGKAT}}' =>
        e($tanggalBerangkat),

    '{{SPD_TGL_KEMBALI}}' =>
        e($tanggalKembali),

    '{{SPD_PENGIKUT_NAMA}}' =>
        e($pengikut),

    '{{SPD_INSTANSI}}' =>
        e($instansi),

    '{{SPD_AKUN}}' =>
        e($akun),

    '{{SPD_NO}}' =>
        e($spdNo),

    '{{TGL_SPD}}' =>
        e($tanggalBerangkat)
];


/*
|--------------------------------------------------------------------------
| TEMPLATE
|--------------------------------------------------------------------------
*/

$template = <<<'HTML'
<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Surat Perintah Tugas & Surat Perjalanan Dinas
</title>


<style>

/* =========================================================
   RESET
========================================================= */

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
    width: 100%;
}

html {
    background: #e5e7eb;
}

body {
    background: #e5e7eb;
    color: #000;

    font-family:
        "Times New Roman",
        Times,
        serif;

    font-size: 10.5pt;

    line-height: 1.25;
}

p {
    margin: 0;
}

table {
    border-collapse: collapse;
}


/* =========================================================
   A4
========================================================= */

@page {
    size: A4 portrait;

    margin:
        12mm
        14mm
        12mm
        14mm;
}


/* =========================================================
   HALAMAN
========================================================= */

.page {

    position: relative;

    width: 182mm;

    min-height: 273mm;

    margin: 0 auto;

    background: #fff;

    page-break-after: always;

    break-after: page;

    overflow: visible;
}

.page:last-child {

    page-break-after: auto;

    break-after: auto;
}


/* =========================================================
   KOP SURAT
========================================================= */

.kop {

    width: 100%;

    display: table;

    table-layout: fixed;

    border-bottom:
        3px double #000;

    padding-bottom: 4mm;

    margin-bottom: 5mm;
}

.kop-logo {

    display: table-cell;

    width: 19%;

    vertical-align: middle;

    text-align: left;
}

.kop-logo img {

    display: block;

    width: 22mm;

    height: 22mm;

    object-fit: contain;

    /*
    Penting untuk browser ketika print.
    */

    -webkit-print-color-adjust: exact;

    print-color-adjust: exact;
}

.kop-text {

    display: table-cell;

    width: 81%;

    vertical-align: middle;

    text-align: center;

    padding-right: 5mm;
}

.kop-text .instansi {

    margin: 0;

    font-size: 14pt;

    font-weight: bold;

    line-height: 1.05;
}

.kop-text .kabupaten {

    margin:
        1.5mm
        0;

    font-size: 12.5pt;

    font-weight: bold;

    line-height: 1.05;
}

.kop-text .alamat {

    margin:
        1mm
        0
        0;

    font-size: 8pt;

    line-height: 1.15;
}


/* =========================================================
   SPT
========================================================= */

.judul {

    text-align: center;

    font-size: 13pt;

    font-weight: bold;

    text-decoration: underline;

    margin-top: 3mm;
}

.nomor {

    text-align: center;

    font-size: 10.5pt;

    font-weight: bold;

    margin-top: 1.5mm;

    margin-bottom: 5mm;
}


/* =========================================================
   INFORMASI SPT
========================================================= */

.tabel-info {

    width: 100%;

    table-layout: fixed;

    margin:
        0
        0
        3mm;
}

.tabel-info td {

    padding:
        1.5mm
        1mm;

    vertical-align: top;

    line-height: 1.25;

    overflow-wrap: break-word;
}

.tabel-info .label-spt {

    width: 31%;
}


/* =========================================================
   MENUGASKAN
========================================================= */

.menugaskan {

    text-align: center;

    font-weight: bold;

    margin:
        4mm
        0
        3mm;
}


/* =========================================================
   ISI
========================================================= */

.isi-spt {

    text-align: justify;

    line-height: 1.35;

    margin: 4mm 0;
}


/* =========================================================
   TANDA TANGAN SPT
========================================================= */

.ttd-spt {

    width: 100%;

    table-layout: fixed;

    margin-top: 10mm;
}

.ttd-spt td {

    vertical-align: top;
}

.ttd-kosong {

    width: 52%;
}

.ttd-kanan {

    width: 48%;

    text-align: center;

    font-size: 10pt;
}

.ttd-kanan p {

    margin:
        1mm
        0;
}

.ruang-ttd {

    height: 24mm;
}

.nama-penandatangan {

    font-weight: bold;

    text-decoration: underline;
}


/* =========================================================
   IDENTITAS SPD
========================================================= */

.spd-identitas {

    width: 100%;

    table-layout: fixed;

    margin-bottom: 2mm;

    font-size: 9pt;
}

.spd-identitas td {

    padding:
        0.5mm
        0;

    vertical-align: top;
}

.spd-identitas .kanan {

    text-align: right;
}


/* =========================================================
   JUDUL SPD
========================================================= */

.spd-title {

    text-align: center;

    font-size: 13.5pt;

    font-weight: bold;

    margin:
        2mm
        0
        0;
}

.spd-subtitle {

    text-align: center;

    font-size: 11pt;

    font-weight: bold;

    margin-bottom: 4mm;
}


/* =========================================================
   TABEL SPD
========================================================= */

.spd-table {

    width: 100%;

    table-layout: fixed;

    font-size: 8.8pt;

    page-break-inside: auto;
}

.spd-table col.col-no {

    width: 7%;
}

.spd-table col.col-label {

    width: 43%;
}

.spd-table col.col-value {

    width: 50%;
}

.spd-table tr {

    page-break-inside: avoid;

    break-inside: avoid;
}

.spd-table td {

    border:
        1px solid #000;

    padding:
        2.2mm
        2mm;

    vertical-align: top;

    line-height: 1.18;

    overflow-wrap: break-word;

    word-break: normal;
}

.spd-table .row-header {

    text-align: center;

    font-weight: bold;

    vertical-align: top;
}

.spd-table p {

    margin: 0;
}


/* =========================================================
   PENGIKUT
========================================================= */

.pengikut-no {

    width: 12%;

    text-align: center;
}

.pengikut-nama {

    width: 38%;
}

.pengikut-keterangan {

    width: 50%;
}


/* =========================================================
   TANDA TANGAN SPD
========================================================= */

.spt-signature-block {

    width: 100%;

    display: table;

    table-layout: fixed;

    margin-top: 5mm;
}

.spt-signer {

    display: table-cell;

    width: 100%;

    text-align: center;

    vertical-align: top;

    font-size: 8.8pt;
}

.spt-signer p {

    margin:
        1mm
        0;
}

.signature-space {

    height: 18mm;
}


/* =========================================================
   PERJALANAN
========================================================= */

.perjalanan-table {

    width: 100%;

    table-layout: fixed;

    font-size: 9pt;

    margin-top: 3mm;
}

.perjalanan-table tr {

    page-break-inside: avoid;

    break-inside: avoid;
}

.perjalanan-table td {

    width: 50%;

    border:
        1px solid #000;

    padding: 3mm;

    vertical-align: top;

    line-height: 1.2;

    overflow-wrap: break-word;
}

.perjalanan-table .ttd-perjalanan {

    text-align: center;
}

.perjalanan-space {

    height: 14mm;
}


/* =========================================================
   PERHATIAN
========================================================= */

.perhatian {

    margin-top: 4mm;

    border:
        1px solid #000;

    padding: 3mm;

    font-size: 8.5pt;

    line-height: 1.2;
}

.perhatian strong {

    display: block;

    margin-bottom: 1mm;
}


/* =========================================================
   PREVIEW SCREEN
========================================================= */

@media screen {

    body {

        padding: 15px 0;
    }

    .page {

        width: 182mm;

        min-height: 273mm;

        margin-bottom: 20px;

        box-shadow:
            0
            2px
            15px
            rgba(
                0,
                0,
                0,
                0.18
            );
    }
}


/* =========================================================
   PRINT
========================================================= */

@media print {

    html {

        width: 100%;

        background: #fff;
    }

    body {

        width: 100%;

        margin: 0;

        padding: 0;

        background: #fff;
    }

    .page {

        width: 100%;

        min-height: 273mm;

        margin: 0;

        padding: 0;

        background: #fff;

        box-shadow: none;

        overflow: visible;
    }

    .page-break {

        page-break-before: always;

        break-before: page;
    }

    img {

        max-width: 100%;

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;
    }

    table {

        page-break-inside: auto;
    }

    tr {

        page-break-inside: avoid;

        break-inside: avoid;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media screen and (max-width: 700px) {

    body {

        padding: 0;
    }

    .page {

        width: 100%;

        min-height: auto;

        padding: 12px;

        margin-bottom: 15px;

        box-shadow: none;
    }

    .kop-logo {

        width: 17%;
    }

    .kop-logo img {

        width: 17mm;

        height: 17mm;
    }

    .kop-text {

        width: 83%;

        padding-right: 0;
    }

    .kop-text .instansi {

        font-size: 10.5pt;
    }

    .kop-text .kabupaten {

        font-size: 9.5pt;
    }

    .kop-text .alamat {

        font-size: 6.5pt;
    }

    .spd-table {

        font-size: 8pt;
    }

    .perjalanan-table {

        font-size: 8pt;
    }
}

</style>

</head>


<body>


<!-- =====================================================
     HALAMAN 1
     SPT
====================================================== -->

<div class="page">


    <!-- KOP -->

    <div class="kop">

        <div class="kop-logo">

            <img
                src="{{LOGO_DATA_URI}}"
                alt="Lambang Kabupaten Banggai Kepulauan"
            >

        </div>


        <div class="kop-text">

            <p class="instansi">
                DEWAN PERWAKILAN RAKYAT DAERAH
            </p>

            <p class="kabupaten">
                KABUPATEN BANGGAI KEPULAUAN
            </p>

            <p class="alamat">
                Jln. Bukit Trikora Jalur Dua No. 02
                Telp./Fax (0462) 2222069 – 2222070
            </p>

            <p class="alamat">
                SALAKAN
            </p>

        </div>

    </div>


    <!-- JUDUL -->

    <div class="judul">
        SURAT PERINTAH TUGAS
    </div>

    <div class="nomor">
        NOMOR : {{SPT_NO}}
    </div>


    <!-- PEMBERI TUGAS -->

    <table class="tabel-info">

        <tr>

            <td class="label-spt">
                Nama (yang memberikan tugas)
            </td>

            <td>
                : ARKAM SUPU, S.Th.I., M.H
            </td>

        </tr>

        <tr>

            <td class="label-spt">
                Jabatan
            </td>

            <td>
                : Ketua DPRD Kabupaten Banggai Kepulauan
            </td>

        </tr>

    </table>


    <div class="menugaskan">
        MENUGASKAN :
    </div>


    <!-- YANG DITUGASKAN -->

    <table class="tabel-info">

        <tr>

            <td style="width:15%;">
                Kepada
            </td>

            <td>
                :
            </td>

        </tr>

        <tr>

            <td></td>

            <td>
                Nama : {{NAMA}}
            </td>

        </tr>

        <tr>

            <td></td>

            <td>
                Jabatan : {{JABATAN}}
            </td>

        </tr>

    </table>


    <!-- UNTUK -->

    <table class="tabel-info">

        <tr>

            <td style="width:15%;">
                Untuk
            </td>

            <td>
                : {{ALASAN}}
            </td>

        </tr>

    </table>


    <!-- PENUTUP -->

    <p class="isi-spt">

        Demikian Surat Tugas ini dibuat dan diberikan kepada
        yang bersangkutan untuk dilaksanakan sebagaimana
        mestinya.

    </p>


    <!-- TANDA TANGAN -->

    <table class="ttd-spt">

        <tr>

            <td class="ttd-kosong"></td>

            <td class="ttd-kanan">

                <p>
                    DIKELUARKAN DI : Salakan
                </p>

                <p>
                    PADA TANGGAL : {{TGL_TERBIT}}
                </p>

                <p>
                    DEWAN PERWAKILAN RAKYAT DAERAH
                </p>

                <p>
                    KABUPATEN BANGGAI KEPULAUAN
                </p>

                <div class="ruang-ttd"></div>

                <p class="nama-penandatangan">
                    {{SIGNER_NAMA}}
                </p>

                <p>
                    {{SIGNER_JABATAN}}
                </p>

            </td>

        </tr>

    </table>

</div>


<!-- =====================================================
     HALAMAN 2
     SPD
====================================================== -->

<div class="page page-break">


    <!-- KOP -->

    <div class="kop">

        <div class="kop-logo">

            <img
                src="{{LOGO_DATA_URI}}"
                alt="Lambang Kabupaten Banggai Kepulauan"
            >

        </div>


        <div class="kop-text">

            <p class="instansi">
                DEWAN PERWAKILAN RAKYAT DAERAH
            </p>

            <p class="kabupaten">
                KABUPATEN BANGGAI KEPULAUAN
            </p>

            <p class="alamat">
                Jln. Bukit Trikora Jalur Dua No. 02
                Telp./Fax (0462) 2222069 – 2222070
            </p>

            <p class="alamat">
                SALAKAN
            </p>

        </div>

    </div>


    <!-- IDENTITAS SPD -->

    <table class="spd-identitas">

        <tr>

            <td></td>

            <td class="kanan">
                Nomor : {{SPD_NO}}
            </td>

        </tr>

        <tr>

            <td></td>

            <td class="kanan">
                Nomor SPT : {{SPT_NO}}
            </td>

        </tr>

    </table>


    <!-- JUDUL SPD -->

    <div class="spd-title">
        SURAT PERJALANAN DINAS
    </div>

    <div class="spd-subtitle">
        SPD
    </div>


    <!-- TABEL SPD -->

    <table class="spd-table">

        <colgroup>

            <col class="col-no">

            <col class="col-label">

            <col class="col-value">

        </colgroup>


        <!-- 1 -->

        <tr>

            <td class="row-header">
                1.
            </td>

            <td>
                Pejabat yang memberikan tugas
            </td>

            <td>
                Sekretaris DPRD Kabupaten Banggai Kepulauan
            </td>

        </tr>


        <!-- 2 -->

        <tr>

            <td class="row-header">
                2.
            </td>

            <td>
                Nama yang melaksanakan Perjalanan Dinas
            </td>

            <td>
                {{SPD_NAMA}}
            </td>

        </tr>


        <!-- 3 -->

        <tr>

            <td
                class="row-header"
                rowspan="3"
            >
                3.
            </td>

            <td>
                a. Pangkat dan Golongan
            </td>

            <td>
                {{SPD_PANGKAT}}
            </td>

        </tr>


        <tr>

            <td>
                b. Jabatan
            </td>

            <td>
                {{SPD_JABATAN_SPD}}
            </td>

        </tr>


        <tr>

            <td>
                c. Tingkat Biaya Perjalanan Dinas
            </td>

            <td>
                {{SPD_TINGKAT_BIAYA}}
            </td>

        </tr>


        <!-- 4 -->

        <tr>

            <td class="row-header">
                4.
            </td>

            <td>
                Maksud Perjalanan Dinas
            </td>

            <td>
                {{SPD_MAKSUD}}
            </td>

        </tr>


        <!-- 5 -->

        <tr>

            <td class="row-header">
                5.
            </td>

            <td>
                Alat angkutan dipergunakan
            </td>

            <td>
                {{SPD_ALAT_ANGKUTAN}}
            </td>

        </tr>


        <!-- 6 -->

        <tr>

            <td
                class="row-header"
                rowspan="2"
            >
                6.
            </td>

            <td>
                Tempat Berangkat
            </td>

            <td>
                {{SPD_BERANGKAT}}
            </td>

        </tr>


        <tr>

            <td>
                Tempat Tujuan
            </td>

            <td>
                {{SPD_TUJUAN}}
            </td>

        </tr>


        <!-- 7 -->

        <tr>

            <td
                class="row-header"
                rowspan="3"
            >
                7.
            </td>

            <td>
                Lamanya Perjalanan Dinas
            </td>

            <td>
                {{SPD_LAMANYA}}
            </td>

        </tr>


        <tr>

            <td>
                Tanggal berangkat
            </td>

            <td>
                {{SPD_TGL_BERANGKAT}}
            </td>

        </tr>


        <tr>

            <td>
                Tanggal harus kembali
            </td>

            <td>
                {{SPD_TGL_KEMBALI}}
            </td>

        </tr>


        <!-- 8 -->

        <tr>

            <td
                class="row-header"
                rowspan="2"
            >
                8.
            </td>

            <td colspan="2">

                <strong>
                    Nama Pengikut:
                </strong>

            </td>

        </tr>


        <tr>

            <td class="pengikut-no">
                No.
            </td>

            <td>
                Nama / Keterangan
            </td>

        </tr>


        <tr>

            <td></td>

            <td class="pengikut-no">
                1
            </td>

            <td>
                {{SPD_PENGIKUT_NAMA}}
            </td>

        </tr>


        <!-- 9 -->

        <tr>

            <td
                class="row-header"
                rowspan="2"
            >
                9.
            </td>

            <td colspan="2">

                <strong>
                    Pembebanan Anggaran:
                </strong>

            </td>

        </tr>


        <tr>

            <td>

                a. Instansi
                <br>
                b. Akun

            </td>

            <td>

                a. {{SPD_INSTANSI}}
                <br>
                b. {{SPD_AKUN}}

            </td>

        </tr>


        <!-- 10 -->

        <tr>

            <td class="row-header">
                10.
            </td>

            <td colspan="2">
                Keterangan lain-lain.
            </td>

        </tr>

    </table>


    <!-- TANDA TANGAN -->

    <div class="spt-signature-block">

        <div class="spt-signer">

            <p>
                DIKELUARKAN DI : Salakan
            </p>

            <p>
                PADA TANGGAL : {{TGL_SPD}}
            </p>

            <p>
                DEWAN PERWAKILAN RAKYAT DAERAH
            </p>

            <p>
                KABUPATEN BANGGAI KEPULAUAN
            </p>

            <div class="signature-space"></div>

            <p>
                <strong>
                    ASGAR LALU, SH
                </strong>
            </p>

            <p>
                NIP. 19741226 20090 1 002
            </p>

        </div>

    </div>

</div>


<!-- =====================================================
     HALAMAN 3
     CATATAN PERJALANAN
====================================================== -->

<div class="page page-break">


    <!-- IDENTITAS -->

    <table class="spd-identitas">

        <tr>

            <td>
                <strong>
                    SPD
                </strong>
            </td>

            <td class="kanan">
                Nomor : {{SPD_NO}}
            </td>

        </tr>

    </table>


    <!-- PERJALANAN I-VII -->

    <table class="perjalanan-table">


        <!-- I -->

        <tr>

            <td>

                <strong>I.</strong>

                <br><br>

                Berangkat dari :
                Salakan

                <br>

                (Tempat Kedudukan)

                <br>

                Ke :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>


            <td class="ttd-perjalanan">

                Plt. SEKRETARIS DPRD

                <br>

                KABUPATEN BANGGAI KEPULAUAN

                <div class="perjalanan-space"></div>

                <strong>
                    ASGAR LALU, SH
                </strong>

                <br>

                NIP. 19741226 20090 1 002

            </td>

        </tr>


        <!-- II -->

        <tr>

            <td>

                <strong>II.</strong>

                Tiba di :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>


            <td>

                Berangkat dari :

                <br>

                Ke :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>

        </tr>


        <!-- III -->

        <tr>

            <td>

                <strong>III.</strong>

                Tiba di :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>


            <td>

                Ke :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>

        </tr>


        <!-- IV -->

        <tr>

            <td>

                <strong>IV.</strong>

                Tiba di :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>


            <td>

                Ke :

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>

        </tr>


        <!-- V -->

        <tr>

            <td>

                <strong>V.</strong>

                Tiba di :
                Salakan

                <br>

                (Tempat Kedudukan)

                <br>

                Pada Tanggal :

                <div class="perjalanan-space"></div>

            </td>


            <td class="ttd-perjalanan">

                Telah diperiksa dengan keterangan bahwa
                perjalanan tersebut atas perintahnya dan
                semata-mata untuk kepentingan jabatannya
                dalam waktu yang sesingkat-singkatnya.

                <div class="perjalanan-space"></div>

                Plt. Sekretaris DPRD

                <br>

                Kabupaten Banggai Kepulauan

                <br><br>

                <strong>
                    ASGAR LALU, SH
                </strong>

                <br>

                NIP. 19741226 20090 1 002

            </td>

        </tr>


        <!-- VI -->

        <tr>

            <td colspan="2">

                <strong>
                    VI. Catatan Lain-Lain
                </strong>

                <br><br>

                &nbsp;

            </td>

        </tr>


        <!-- VII -->

        <tr>

            <td colspan="2">

                <strong>
                    VII. PERHATIAN
                </strong>

                <br>

                Kepala SKPD yang menerbitkan SPD,
                Pegawai yang melakukan perjalanan dinas,
                para Pejabat yang mengesahkan tanggal
                berangkat / tiba, serta bendahara pengeluaran
                bertanggung jawab berdasarkan ketentuan
                yang berlaku.

            </td>

        </tr>

    </table>


    <!-- TANDA TANGAN AKHIR -->

    <div class="spt-signature-block">


        <div class="spt-signer">

            <p>
                PEGAWAI YANG MELAKSANAKAN
            </p>

            <div class="signature-space"></div>

            <p>
                <strong>
                    {{SPD_NAMA}}
                </strong>
            </p>

        </div>


        <div class="spt-signer">

            <p>
                Plt. SEKRETARIS DPRD
            </p>

            <p>
                KABUPATEN BANGGAI KEPULAUAN
            </p>

            <div class="signature-space"></div>

            <p>
                <strong>
                    ASGAR LALU, SH
                </strong>
            </p>

            <p>
                NIP. 19741226 20090 1 002
            </p>

        </div>

    </div>


    <!-- CATATAN -->

    <div class="perhatian">

        <strong>
            CATATAN:
        </strong>

        Dokumen ini merupakan Surat Perintah Tugas
        dan Surat Perjalanan Dinas yang diterbitkan
        berdasarkan data pengajuan perjalanan dinas
        yang telah berstatus selesai.

    </div>


</div>


</body>

</html>
HTML;


/*
|--------------------------------------------------------------------------
| REPLACE PLACEHOLDER
|--------------------------------------------------------------------------
*/

$html = str_replace(
    array_keys($replacements),
    array_values($replacements),
    $template
);


/*
|--------------------------------------------------------------------------
| OUTPUT
|--------------------------------------------------------------------------
*/

echo $html;