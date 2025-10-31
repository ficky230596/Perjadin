<?php
session_start();
include '../config/db.php';
require_once '../vendor/autoload.php'; // Path to Composer autoload for Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) die('ID invalid');
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, u.nama AS nama_pegawai, u.jabatan FROM pengajuan p JOIN users u ON p.pegawai_id = u.id WHERE p.id = ? AND p.status = 'selesai'");
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) die('Belum selesai');

// Debugging for spd_no
if (empty($data['spd_no'])) {
    error_log("SPD_NO kosong untuk ID: $id");
}

function hitungLama($start, $end)
{
    $diff = strtotime($end) - strtotime($start);
    return round($diff / 86400) + 1;
}

// Placeholder replacements
$replacements = [
    '{{SPT_NO}}' => $data['spt_no'] ?? '-',
    '{{SPD_NO}}' => $data['spd_no'] ?? '-', // Ensure spd_no is used
    '{{NAMA}}' => $data['nama_pegawai'],
    '{{JABATAN}}' => $data['jabatan'],
    '{{ALASAN}}' => $data['alasan'],
    '{{TGL_TERBIT}}' => date('d F Y', strtotime($data['tanggal_berangkat'])),
    '{{SIGNER_NAMA}}' => 'ARKAM SUPU, S.Th.I., M.H',
    '{{SIGNER_JABATAN}}' => 'Ketua DPRD Kabupaten Banggai Kepulauan',
    '{{SPD_NAMA}}' => $data['nama_pegawai'],
    '{{SPD_PANGKAT}}' => $data['pangkat'] ?? '-',
    '{{SPD_JABATAN_SPD}}' => $data['jabatan'],
    '{{SPD_TINGKAT_BIAYA}}' => $data['tingkat_biaya'] ?? '-',
    '{{SPD_MAKSUD}}' => $data['alasan'],
    '{{SPD_ALAT_ANGKUTAN}}' => $data['alat_angkutan'] ?? '-',
    '{{SPD_BERANGKAT}}' => 'Salakan',
    '{{SPD_TUJUAN}}' => $data['tujuan'],
    '{{SPD_LAMANYA}}' => hitungLama($data['tanggal_berangkat'], $data['tanggal_kembali']) . ' Hari',
    '{{SPD_TGL_BERANGKAT}}' => date('d F Y', strtotime($data['tanggal_berangkat'])),
    '{{SPD_TGL_KEMBALI}}' => date('d F Y', strtotime($data['tanggal_kembali'])),
    '{{SPD_PENGIKUT_NAMA}}' => $data['pengikut'] ?? '-',
    '{{SPD_INSTANSI}}' => $data['instansi_anggaran'] ?? '-',
    '{{SPD_AKUN}}' => $data['akun_anggaran'] ?? '-',
    '{{TGL_SPD}}' => date('d F Y', strtotime($data['tanggal_berangkat']))
];

// Original HTML and CSS with fixes for <tr> and margin
$template = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Tugas & Surat Perjalanan Dinas - DPRD Kabupaten Banggai Kepulauan</title>
    <style>
        body {
            font-family: Times New Roman, Times, serif;
            margin: 0;
            padding: 20px;
            color: #000;
            background-color: #fff;
        }
        .document-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid transparent;
            padding: 30px;
        }
        .header-section {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .header-text {
            text-align: center;
            flex-grow: 1;
        }
        .header-text p {
            margin: 0;
            line-height: 1.2;
        }
        .header-text .main-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text .subtitle {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text .address {
            font-size: 10pt;
        }
        h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 14pt;
            margin: 15px 0 5px 0;
        }
        .number-line {
            text-align: center;
            margin-bottom: 25px;
        }
        .spt-content p {
            text-align: justify;
            text-indent: 50px;
            line-height: 1.5;
        }
        .spt-detail-table,
        .spd-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-top: 100;
        }
        .spt-detail-table td:first-child {
            width: 150px;
        }
        .spt-detail-table td:nth-child(2) {
            width: 10px;
        }
        .spt-detail-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .spt-signature-block {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            text-align: center;
            width: 100%;
            font-size: 12px;
        }
        .spt-signer {
            width: 50%;
            margin-top: auto;
        }
        .spt-signer-date {
            margin-bottom: 0;
        }
        .spt-signer-name {
            margin-top: 0;
            font-weight: bold;
            text-decoration: underline;
        }
        .spd-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0 5px 0;
        }
        .spd-number {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 30px;
        }
        .spd-table {
            border: 1px solid #000;
        }
        .spd-table th,
        .spd-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            font-size: 11pt;
            text-align: left;
        }
        .spd-table th {
            text-align: center;
            font-weight: bold;
        }
        .spd-table .row-header {
            width: 20px;
            text-align: center;
            font-weight: bold;
        }
        .spd-table .sub-row-header {
            width: 30px;
            text-align: center;
        }
        .spd-table td.no-border {
            border: none;
            padding: 10px;
        }
        .travel-log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #000;
        }
        .travel-log-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            font-size: 11pt;
        }
        .travel-log-table .section-header {
            text-align: center;
        }
        .travel-log-table .log-signer {
            text-align: center;
            margin-top: 10px;
        }
        .travel-log-table .log-signer-name {
            margin-top: 50px;
            font-weight: bold;
        }
        .attention-section {
            margin-top: 20px;
            font-size: 10pt;
        }
        .attention-section p {
            text-align: justify;
            text-indent: 0;
        }
        @page {
            size: A4;
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
        }
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.3;
            color: #000;
        }
        .kop {
            text-align: center;
            font-weight: bold;
        }
        .kop h1 {
            font-size: 16pt;
            margin: 0;
        }
        .kop h2 {
            font-size: 14pt;
            margin: 0;
        }
        .kop p {
            font-size: 10pt;
            margin: 2px 0;
        }
        .garis {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .nomor {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .isi p {
            text-align: justify;
            margin: 4px 0;
        }
        .tabel-info {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .tabel-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .menugaskan {
            font-weight: bold;
            text-align: center;
            margin: 15px 0 10px 0;
        }
        .ttd {
            margin-top: 40px;
            width: 100%;
        }
        .ttd td {
            vertical-align: top;
        }
        .kanan {
            width: 60%;
            text-align: center;
        }
        .kanan .spasi {
            height: 70px;
        }
    </style>
</head>
<body>
<div class="document-container">
    <div class="header-section">
        <img class="logo" src="logo.png" alt="Lambang Kabupaten Banggai Kepulauan">
        <div class="header-text">
            <p class="main-title">DEWAN PERWAKILAN RAKYAT DAERAH</p>
            <p class="subtitle">KABUPATEN BANGGAI KEPULAUAN</p>
            <p class="address">Jln. Bukit Trikora Jalur Dua No. 02 Telp./ Fax (0462) 2222069 –2222070</p>
            <p class="address">SALAKAN</p>
        </div>
    </div>
    <div class="judul">SURAT PERINTAH TUGAS</div>
    <div class="nomor">NOMOR : {{SPT_NO}}</div>
    <table class="tabel-info">
        <tr>
            <td width="40%">Nama (yang memberikan tugas)</td>
            <td>: ARKAM SUPU, S.Th.I., M.H</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: Ketua DPRD Kabupaten Banggai Kepulauan</td>
        </tr>
    </table>
    <div class="menugaskan">MENUGASKAN :</div>
    <table class="tabel-info">
        <tr>
            <td width="15%">Kepada</td>
            <td>: </td>
        </tr>
        <tr>
            <td></td>
            <td>Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{NAMA}}</td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan&nbsp;&nbsp;: {{JABATAN}}</td>
        </tr>
    </table>
    <table class="tabel-info">
        <tr>
            <td width="15%">Untuk</td>
            <td>: {{ALASAN}}</td>
        </tr>
    </table>
    <p>Demikian Surat Tugas ini dibuat dan diberikan kepada yang bersangkutan untuk dilaksanakan sebagaimana mestinya.</p>
    <table class="ttd">
        <tr>
            <td width="40%"></td>
            <td class="kanan">
                <p>DIKELUARKAN DI : Salakan</p>
                <p>PADA TANGGAL&nbsp;&nbsp;: {{TGL_TERBIT}}</p>
                <p>DEWAN PERWAKILAN RAKYAT DAERAH<br>KABUPATEN BANGGAI KEPULAUAN</p>
                <div class="spasi"></div>
                <p><strong>{{SIGNER_NAMA}}</strong></p>
                <p>{{SIGNER_JABATAN}}</p>
            </td>
        </tr>
    </table>
</div>
<div style="page-break-before: always;"></div>
<div class="document-container">
    <div class="header-section">
        <img class="logo" src="logo.png" alt="Lambang Kabupaten Banggai Kepulauan">
        <div class="header-text">
            <p class="main-title">DEWAN PERWAKILAN RAKYAT DAERAH</p>
            <p class="subtitle">KABUPATEN BANGGAI KEPULAUAN</p>
            <p class="address">Jln. Bukit Trikora Jalur Dua No. 02 Telp./ Fax (0462) 2222069 –2222070</p>
            <p class="address">SALAKAN</p>
        </div>
    </div>
    <div style="text-align: left; font-size: 11pt; margin-bottom: 5px;">
        <p style="float: right; margin: 0;">Lembar ke&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
        <div style="clear: both;"></div>
    </div>
    <div style="text-align: left; font-size: 11pt; margin-bottom: 5px;">
        <p style="float: right; margin: 0;">Kode No&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;</p>
        <div style="clear: both;"></div>
    </div>
    <div style="text-align: left; font-size: 11pt; margin-bottom: 5px;">
        <p style="float: right; margin: 0;">Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{SPD_NO}}</p>
        <div style="clear: both;"></div>
    </div>
    <h2 class="spd-title" style="margin-top: 10px;">SURAT PERJALANAN DINAS</h2>
    <h2 style="font-size: 15pt; margin-top: 0;">SPD</h2>
    <table class="spd-table">
        <tr>
            <td class="row-header">1.</td>
            <td colspan="2">Pejabat yang memberikan tugas</td>
            <td>Sekretaris DPRD Kabupaten Banggai Kepulauan</td>
        </tr>
        <tr>
            <td class="row-header">2.</td>
            <td colspan="2">Nama yang melaksanakan Perjalanan Dinas</td>
            <td>{{SPD_NAMA}}</td>
        </tr>
        <tr>
            <td class="row-header" rowspan="3">3.</td>
            <td colspan="2">a. Pangkat dan Golongan</td>
            <td>a. {{SPD_PANGKAT}}</td>
        </tr>
        <tr>
            <td colspan="2">b. Jabatan</td>
            <td>b. {{SPD_JABATAN_SPD}}</td>
        </tr>
        <tr>
            <td colspan="2">c. Tingkat Biaya Perjalanan Dinas</td>
            <td>c. {{SPD_TINGKAT_BIAYA}}</td>
        </tr>
        <tr>
            <td class="row-header">4.</td>
            <td colspan="2">Maksud Perjalanan Dinas</td>
            <td>{{SPD_MAKSUD}}</td>
        </tr>
        <tr>
            <td class="row-header">5.</td>
            <td colspan="2">Alat angkutan dipergunakan</td>
            <td>{{SPD_ALAT_ANGKUTAN}}</td>
        </tr>
        <tr>
            <td class="row-header" rowspan="2">6.</td>
            <td colspan="2">Tempat Berangkat</td>
            <td>{{SPD_BERANGKAT}}</td>
        </tr>
        <tr>
            <td colspan="2">Tempat Tujuan</td>
            <td>{{SPD_TUJUAN}}</td>
        </tr>
        <tr>
            <td class="row-header" rowspan="3">7.</td>
            <td colspan="2">Lamanya Perjalanan Dinas</td>
            <td>{{SPD_LAMANYA}}</td>
        </tr>
        <tr>
            <td colspan="2">Tanggal berangkat</td>
            <td>{{SPD_TGL_BERANGKAT}}</td>
        </tr>
        <tr>
            <td colspan="2">Tanggal harus kembali</td>
            <td>{{SPD_TGL_KEMBALI}}</td>
        </tr>
        <tr>
            <td class="row-header" rowspan="2">8.</td>
            <td colspan="3">Nama Pengikut:</td>
        </tr>
        <tr>
            <td class="sub-row-header">No.</td>
            <td style="width: 40%;">Nama</td>
            <td>Tanggal lahir / Keterangan</td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">1</td>
            <td>{{SPD_PENGIKUT_NAMA}}</td>
            <td>24 Februari 1980 / </td>
        </tr>
        <tr>
            <td class="row-header" rowspan="2">9.</td>
            <td colspan="3">Pembebanan Anggaran:</td>
        </tr>
        <tr>
            <td colspan="2">a. Instansi<br>b. Akun</td>
            <td>a. {{SPD_INSTANSI}}<br>b. {{SPD_AKUN}}</td>
        </tr>
        <tr>
            <td class="row-header">10.</td>
            <td colspan="3">Keterangan lain - lain</td>
        </tr>
    </table>
    <div class="spt-signature-block">
        <div class="spt-signer">
            <p class="spt-signer-date">DIKELUARKAN DI : Salakan</p>
            <p class="spt-signer-date">PADA TANGGAL : {{TGL_SPD}}</p>
            <p>DEWAN PERWAKILAN RAKYAT DAERAH</p>
            <p>KABUPATEN BANGGAI KEPULAUAN</p>
            <p style="margin-top: 100px; font-weight: bold; text-decoration: underline;">ARKAM SUPU, S.Th.I., M.H</p>
        </div>
    </div>
    <table style="width:100%; border-collapse:collapse; font-family:Times New Roman, serif; font-size:12pt; margin-top:10px;">
        <tr>
            <td style="border:1px solid #000; width:50%; vertical-align:top; padding:6px;">
                I.<br><br><br><br>
            </td>
            <td style="border:1px solid #000; width:50%; vertical-align:top; padding:6px;">
                Berangkat dari&nbsp;&nbsp;&nbsp;: Salakan.<br>
                (Tempat Kedudukan)<br>
                Ke&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{SPD_TUJUAN}}<br>
                Pada Tanggal&nbsp;: {{SPD_TGL_BERANGKAT}}<br><br><br><br>
                <div style="text-align:center; font-weight:bold;">
                    Plt. SEKRETARIS DPRD<br>
                    KABUPATEN BANGGAI KEPULAUAN<br><br><br><br>
                    ASGAR LALU, SH<br>
                    NIP. 19741226 20090 1 002
                </div>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                <strong>II.</strong>&nbsp;Tiba di&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                Berangkat dari&nbsp;&nbsp;:<br>
                Ke&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                <strong>III.</strong>&nbsp;Tiba di&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                Berangkat dari&nbsp;&nbsp;:<br>
                Ke&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                <strong>IV.</strong>&nbsp;Tiba di&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                Berangkat dari&nbsp;&nbsp;:<br>
                Ke&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<br>
                Pada Tanggal&nbsp;:<br><br>
                <table style="width:100%;">
                    <tr>
                        <td style="width:50%; vertical-align:bottom; text-align:center">
                            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                        <td style="width:50%; vertical-align:bottom;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                <strong>V.</strong>&nbsp;Tiba di&nbsp;&nbsp;: Salakan.<br>
                (Tempat Kedudukan)<br>
                Pada Tanggal&nbsp;: {{SPD_TGL_KEMBALI}}<br><br>
                <div style="text-align:center; font-weight:bold;">
                    Plt. Sekretaris DPRD<br>
                    Kabupaten Banggai Kepulauan<br><br><br><br>
                    ASGAR LALU, SH<br>
                    NIP. 19741226 20090 1 002
                </div>
            </td>
            <td style="border:1px solid #000; padding:6px; vertical-align:top;">
                Telah diperiksa dengan keterangan bahwa perjalanan tersebut atas perintahnya dan semata-mata untuk kepentingan jabatannya dalam waktu yang sesingkat-singkatnya.<br><br>
                <div style="text-align:center; font-weight:bold;">
                    Plt. Sekretaris DPRD<br>
                    Kabupaten Banggai Kepulauan<br><br><br><br>
                    ASGAR LALU, SH<br>
                    NIP. 19741226 20090 1 002
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:5px; vertical-align:top;">
                <strong>VI. Catatan Lain-Lain</strong><br>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:6px; vertical-align:top;">
                <strong>VII. PERHATIAN</strong><br>
                Kepala SKPD yang menerbitkan SPD, Pegawai yang melakukan perjalanan dinas, para Pejabat yang mengesahkan tanggal berangkat / tiba, serta bendahara pengeluaran bertanggung jawab berdasarkan ketentuan yang berlaku.
            </td>
        </tr>
    </table>
</div>
</body>
</html>';

// Replace placeholders with data
$html = str_replace(array_keys($replacements), array_values($replacements), $template);

// Validate HTML structure (basic check for stray <tr> tags)
if (substr_count($html, '<tr>') !== substr_count($html, '</tr>')) {
    error_log("Mismatched <tr> tags in HTML");
}

// Initialize Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true); // Allow external images (logo.png)
$options->set('defaultFont', 'Times New Roman');
$dompdf = new Dompdf($options);

// Load HTML into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('F4', 'portrait');

// Render HTML to PDF
$dompdf->render();

// Output PDF for download
$filename = "SPPD_" . ($data['spd_no'] ?? 'document') . ".pdf";
ob_clean(); // Clear any output buffer to prevent "headers already sent" error
$dompdf->stream($filename, ['Attachment' => true]);
