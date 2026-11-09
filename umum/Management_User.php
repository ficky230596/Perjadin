<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'umum') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Management User - Bagian Umum</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --navy: #10294d;
            --navy-light: #17477f;
            --gold: #f2bd4b;
            --bg: #f5f7fa;
            --white: #ffffff;
            --border: #e3e9f0;
            --text: #334155;
            --muted: #64748b;
            --danger: #dc3545;
            --success: #198754;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        .main-content-umum {
            margin-left: 250px;
            padding: 92px 25px 85px;
            min-height: 100vh;
        }

        .page-header-user {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .page-header-left {
            min-width: 0;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            color: var(--navy);
            font-size: 23px;
            font-weight: 750;
            line-height: 1.3;
        }

        .page-title-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--navy);
            color: var(--gold);
            border-radius: 10px;
            font-size: 17px;
        }

        .page-subtitle {
            margin: 7px 0 0 54px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        /* =========================
           CONTENT CARD
        ========================= */

        .user-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(16, 41, 77, 0.06);
            overflow: hidden;
        }

        .user-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 20px;
            border-bottom: 1px solid #edf1f5;
        }

        .user-card-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--navy);
            font-size: 14px;
            font-weight: 700;
        }

        .user-card-title i {
            color: var(--gold);
        }

        .user-count {
            padding: 5px 10px;
            background: #f1f5f9;
            color: var(--navy-light);
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: none;
            border-radius: 8px;
            padding: 9px 13px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 650;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn-add {
            background: var(--navy);
            color: #fff;
            box-shadow: 0 3px 8px rgba(16, 41, 77, .15);
        }

        .btn-add:hover {
            background: var(--navy-light);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #fff4dc;
            color: #a66a00;
        }

        .btn-edit:hover {
            background: var(--warning);
            color: #fff;
        }

        .btn-delete {
            background: #fff0f1;
            color: var(--danger);
        }

        .btn-delete:hover {
            background: var(--danger);
            color: #fff;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .user-table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            font-size: 11px;
        }

        .user-table thead th {
            padding: 12px 10px;
            background: var(--navy);
            color: #fff;
            border: none;
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
        }

        .user-table thead th:first-child {
            border-top-left-radius: 0;
        }

        .user-table tbody td {
            padding: 11px 10px;
            border-bottom: 1px solid #edf1f5;
            color: #475569;
            text-align: center;
            vertical-align: middle;
            background: #fff;
        }

        .user-table tbody tr:hover td {
            background: #f8fafc;
        }

        .user-table tbody tr:last-child td {
            border-bottom: none;
        }

        .user-name {
            color: var(--navy);
            font-weight: 700;
            text-align: left !important;
            white-space: nowrap;
        }

        .username-cell {
            color: #64748b;
            font-family: Consolas, monospace;
            font-size: 10px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 20px;
            background: #eef4fb;
            color: var(--navy-light);
            font-size: 9px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .wa-cell {
            white-space: nowrap;
            font-size: 10px;
        }

        .action-cell {
            white-space: nowrap;
        }

        .action-cell .btn {
            margin: 2px;
            padding: 7px 9px;
            font-size: 10px;
        }

        .empty-table {
            padding: 35px 20px !important;
            color: #94a3b8 !important;
        }

        .empty-table i {
            display: block;
            margin-bottom: 8px;
            font-size: 25px;
        }

        /* =========================
           MODAL
        ========================= */

        .user-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 5000;
            background: rgba(15, 23, 42, .58);
            padding: 25px 15px;
            overflow-y: auto;
            backdrop-filter: blur(2px);
        }

        .user-modal.show {
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .modal-content {
            width: min(760px, 100%);
            margin: 35px auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, .2);
            overflow: hidden;
            animation: modalShow .2s ease;
        }

        @keyframes modalShow {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 20px;
            background: var(--navy);
            color: #fff;
        }

        .modal-header h2 {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            font-size: 15px;
            font-weight: 700;
        }

        .modal-header h2 i {
            color: var(--gold);
        }

        .close {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #fff;
            font-size: 19px;
            cursor: pointer;
            transition: all .2s ease;
        }

        .close:hover {
            background: rgba(255, 255, 255, .12);
            color: var(--gold);
        }

        .modal-body {
            padding: 20px;
        }

        /* =========================
           FORM
        ========================= */

        #userForm {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 6px;
            color: #334155;
            font-size: 10px;
            font-weight: 700;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            min-height: 38px;
            padding: 9px 10px;
            border: 1px solid #d7dee7;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #334155;
            font-family: inherit;
            font-size: 11px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .form-control:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(23, 71, 127, .10);
        }

        select.form-control {
            cursor: pointer;
        }

        .form-help {
            margin-top: 5px;
            color: #94a3b8;
            font-size: 9px;
        }

        .section-divider {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 3px;
            padding-bottom: 7px;
            border-bottom: 1px solid #edf1f5;
            color: var(--navy);
            font-size: 11px;
            font-weight: 750;
        }

        .section-divider i {
            color: var(--gold);
        }

        .modal-footer {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 5px;
            padding-top: 14px;
            border-top: 1px solid #edf1f5;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-save {
            background: var(--navy);
            color: #fff;
            min-width: 110px;
        }

        .btn-save:hover {
            background: var(--navy-light);
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {
            .main-content-umum {
                padding-left: 18px;
                padding-right: 18px;
            }
        }

        @media (max-width: 768px) {
            .main-content-umum {
                margin-left: 0;
                padding: 80px 12px 72px;
            }

            .page-header-user {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }

            .page-title {
                font-size: 18px;
            }

            .page-title-icon {
                width: 38px;
                height: 38px;
                min-width: 38px;
                font-size: 15px;
            }

            .page-subtitle {
                margin-left: 50px;
                font-size: 10px;
            }

            .user-card-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .user-card {
                border-radius: 11px;
            }

            .modal-content {
                margin: 10px auto;
            }

            .modal-body {
                padding: 15px;
            }

            #userForm {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .form-group.full-width,
            .section-divider,
            .modal-footer {
                grid-column: auto;
            }
        }

        @media (max-width: 480px) {
            .main-content-umum {
                padding: 76px 9px 68px;
            }

            .page-title {
                font-size: 16px;
                gap: 9px;
            }

            .page-title-icon {
                width: 34px;
                height: 34px;
                min-width: 34px;
                border-radius: 8px;
                font-size: 13px;
            }

            .page-subtitle {
                margin-left: 43px;
                font-size: 9px;
            }

            .user-card-header {
                padding: 14px;
            }

            .modal-header {
                padding: 14px 15px;
            }

            .modal-header h2 {
                font-size: 13px;
            }

            .modal-body {
                padding: 13px;
            }

            .modal-footer {
                flex-direction: column-reverse;
            }

            .modal-footer .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include 'header_umum.php'; ?>
    <?php include 'sidebar_umum.php'; ?>

    <main class="main-content-umum">

        <div class="page-header-user">

            <div class="page-header-left">
                <h1 class="page-title">
                    <span class="page-title-icon">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>
                    Management User
                </h1>

                <p class="page-subtitle">
                    Kelola akun pengguna, role, jabatan, serta informasi pengguna sistem.
                </p>
            </div>

            <button type="button" class="btn btn-add" id="btnAdd">
                <i class="fa-solid fa-user-plus"></i>
                Tambah User
            </button>

        </div>

        <section class="user-card">

            <div class="user-card-header">

                <div class="user-card-title">
                    <i class="fa-solid fa-users"></i>
                    <span>Daftar Pengguna Sistem</span>
                </div>

                <?php
                $countStmt = $pdo->query("SELECT COUNT(*) FROM users");
                $totalUsers = (int) $countStmt->fetchColumn();
                ?>

                <span class="user-count">
                    <?= $totalUsers; ?> User
                </span>

            </div>

            <div class="table-wrapper">

                <table class="user-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Jabatan</th>
                            <th>Fraksi</th>
                            <th>Komisi</th>
                            <th>Pangkat</th>
                            <th>Golongan</th>
                            <th>WA</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php

                        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");

                        $no = 1;

                        if ($stmt->rowCount() === 0):
                        ?>

                            <tr>
                                <td colspan="11" class="empty-table">
                                    <i class="fa-regular fa-folder-open"></i>
                                    Belum ada data pengguna.
                                </td>
                            </tr>

                        <?php
                        else:

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td class="user-name">
                                        <?= htmlspecialchars($row['nama'] ?? '-'); ?>
                                    </td>

                                    <td class="username-cell">
                                        <?= htmlspecialchars($row['username'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <span class="role-badge">
                                            <?= htmlspecialchars(ucfirst($row['role'] ?? '-')); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['jabatan'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['fraksi'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['komisi'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['pangkat'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['golongan'] ?: '-'); ?>
                                    </td>

                                    <td class="wa-cell">
                                        <?= htmlspecialchars($row['wa_phone'] ?: '-'); ?>
                                    </td>

                                    <td class="action-cell">

                                        <button
                                            type="button"
                                            class="btn btn-edit"
                                            onclick="openForm(<?= (int) $row['id']; ?>)">
                                            <i class="fa-solid fa-pen"></i>
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-delete"
                                            onclick="deleteUser(<?= (int) $row['id']; ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                            Hapus
                                        </button>

                                    </td>

                                </tr>

                        <?php
                            endwhile;
                        endif;
                        ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

    <!-- MODAL USER -->

    <div class="user-modal" id="userModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2 id="modalTitle">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Tambah User</span>
                </h2>

                <span class="close" id="closeModal">
                    <i class="fa-solid fa-xmark"></i>
                </span>

            </div>

            <div class="modal-body">

                <form id="userForm">

                    <input
                        type="hidden"
                        name="id"
                        id="userId">

                    <div class="section-divider">
                        <i class="fa-solid fa-user"></i>
                        Informasi Akun
                    </div>

                    <div class="form-group">

                        <label for="nama">
                            Nama <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            class="form-control"
                            required
                            autocomplete="off">

                    </div>

                    <div class="form-group">

                        <label for="username">
                            Username <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="form-control"
                            required
                            autocomplete="off">

                    </div>

                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Kosongkan jika tidak diganti"
                            autocomplete="new-password">

                        <span class="form-help">
                            Saat edit, kosongkan jika password tetap.
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="role">
                            Role <span class="required">*</span>
                        </label>

                        <select
                            name="role"
                            id="role"
                            class="form-control"
                            required>

                            <option value="">
                                -- Pilih Role --
                            </option>

                            <option value="pegawai">
                                Pegawai
                            </option>

                            <option value="sekwan">
                                Sekwan
                            </option>

                            <option value="umum">
                                Umum
                            </option>

                            <option value="ketua">
                                Ketua
                            </option>

                        </select>

                    </div>

                    <div class="section-divider">
                        <i class="fa-solid fa-id-card"></i>
                        Informasi Kepegawaian
                    </div>

                    <div class="form-group full-width">

                        <label for="jabatan">
                            Jabatan <span class="required">*</span>
                        </label>

                        <select
                            name="jabatan"
                            id="jabatan"
                            class="form-control"
                            required>

                            <option value="">
                                -- Pilih Jabatan --
                            </option>

                            <optgroup label="Anggota DPRD">

                                <option value="Ketua DPRD">
                                    Ketua DPRD
                                </option>

                                <option value="Wakil Ketua I">
                                    Wakil Ketua I
                                </option>

                                <option value="Wakil Ketua II">
                                    Wakil Ketua II
                                </option>

                                <option value="Anggota DPRD">
                                    Anggota DPRD
                                </option>

                            </optgroup>

                            <optgroup label="Sekretariat DPRD (ASN / P3K)">

                                <option value="Sekretaris DPRD">
                                    Sekretaris DPRD
                                </option>

                                <option value="Kepala Bagian Umum dan Keuangan">
                                    Kepala Bagian Umum dan Keuangan
                                </option>

                                <option value="Kepala Bagian Persidangan dan Perundangan">
                                    Kepala Bagian Persidangan dan Perundangan
                                </option>

                                <option value="Kasubbag Keuangan">
                                    Kasubbag Keuangan
                                </option>

                                <option value="Kasubbag Umum dan Rumah Tangga">
                                    Kasubbag Umum dan Rumah Tangga
                                </option>

                                <option value="Kasubbag Risalah dan Persidangan">
                                    Kasubbag Risalah dan Persidangan
                                </option>

                                <option value="Bendahara Pengeluaran">
                                    Bendahara Pengeluaran
                                </option>

                                <option value="Bendahara Penerimaan">
                                    Bendahara Penerimaan
                                </option>

                                <option value="Bendahara Barang">
                                    Bendahara Barang
                                </option>

                                <option value="Analis Kebijakan">
                                    Analis Kebijakan
                                </option>

                                <option value="Staf Administrasi">
                                    Staf Administrasi
                                </option>

                                <option value="Petugas TU">
                                    Petugas Tata Usaha
                                </option>

                                <option value="Tenaga Kebersihan">
                                    Tenaga Kebersihan
                                </option>

                                <option value="P3K">
                                    Pegawai Pemerintah dengan Perjanjian Kerja (P3K)
                                </option>

                            </optgroup>

                        </select>

                    </div>

                    <div
                        class="form-group"
                        id="fraksi-group"
                        style="display:none;">

                        <label for="fraksi">
                            Fraksi
                        </label>

                        <select
                            name="fraksi"
                            id="fraksi"
                            class="form-control">

                            <option value="">
                                -- Pilih Fraksi --
                            </option>

                            <option value="Fraksi Golkar">
                                Fraksi Golkar
                            </option>

                            <option value="Fraksi PDIP">
                                Fraksi PDIP
                            </option>

                            <option value="Fraksi NasDem">
                                Fraksi NasDem
                            </option>

                            <option value="Fraksi Gerindra">
                                Fraksi Gerindra
                            </option>

                            <option value="Fraksi Demokrat">
                                Fraksi Demokrat
                            </option>

                            <option value="Fraksi Gabungan">
                                Fraksi Gabungan
                            </option>

                        </select>

                    </div>

                    <div
                        class="form-group"
                        id="komisi-group"
                        style="display:none;">

                        <label for="komisi">
                            Komisi
                        </label>

                        <select
                            name="komisi"
                            id="komisi"
                            class="form-control">

                            <option value="">
                                -- Pilih Komisi --
                            </option>

                            <option value="Komisi I">
                                Komisi I
                            </option>

                            <option value="Komisi II">
                                Komisi II
                            </option>

                            <option value="Komisi III">
                                Komisi III
                            </option>

                        </select>

                    </div>

                    <div
                        class="form-group"
                        id="pangkat-group">

                        <label for="pangkat">
                            Pangkat
                        </label>

                        <select
                            name="pangkat"
                            id="pangkat"
                            class="form-control">

                            <option value="">
                                -- Pilih Pangkat --
                            </option>

                            <option value="Penata Muda (III/a)">
                                Penata Muda (III/a)
                            </option>

                            <option value="Penata (III/b)">
                                Penata (III/b)
                            </option>

                            <option value="Pembina (IV/a)">
                                Pembina (IV/a)
                            </option>

                            <option value="Pembina Tingkat I (IV/b)">
                                Pembina Tingkat I (IV/b)
                            </option>

                            <option value="Pembina Utama Muda (IV/c)">
                                Pembina Utama Muda (IV/c)
                            </option>

                        </select>

                    </div>

                    <div
                        class="form-group"
                        id="golongan-group">

                        <label for="golongan">
                            Golongan
                        </label>

                        <select
                            name="golongan"
                            id="golongan"
                            class="form-control">

                            <option value="">
                                -- Pilih Golongan --
                            </option>

                            <option value="II">
                                II
                            </option>

                            <option value="III">
                                III
                            </option>

                            <option value="IV">
                                IV
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="wa_phone">
                            Nomor WhatsApp
                        </label>

                        <input
                            type="text"
                            name="wa_phone"
                            id="wa_phone"
                            class="form-control"
                            placeholder="628xxxxxxxxxx"
                            autocomplete="off">

                        <span class="form-help">
                            Gunakan format nomor WhatsApp, contoh: 628xxxxxxxxxx.
                        </span>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-cancel"
                            id="cancelModal">
                            <i class="fa-solid fa-xmark"></i>
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script>

        const modal = document.getElementById("userModal");
        const closeModal = document.getElementById("closeModal");
        const cancelModal = document.getElementById("cancelModal");
        const btnAdd = document.getElementById("btnAdd");
        const userForm = document.getElementById("userForm");

        /* =========================
           BUKA MODAL TAMBAH
        ========================= */

        btnAdd.addEventListener("click", function () {

            document.getElementById("modalTitle").innerHTML =
                '<i class="fa-solid fa-user-plus"></i><span>Tambah User</span>';

            userForm.reset();

            document.getElementById("userId").value = "";

            toggleFields();

            modal.classList.add("show");

            document.body.style.overflow = "hidden";
        });


        /* =========================
           TUTUP MODAL
        ========================= */

        function closeUserModal() {

            modal.classList.remove("show");

            document.body.style.overflow = "";

        }

        closeModal.addEventListener("click", closeUserModal);

        cancelModal.addEventListener("click", closeUserModal);

        modal.addEventListener("click", function (event) {

            if (event.target === modal) {
                closeUserModal();
            }

        });

        document.addEventListener("keydown", function (event) {

            if (event.key === "Escape" && modal.classList.contains("show")) {
                closeUserModal();
            }

        });


        /* =========================
           EDIT USER
        ========================= */

        function openForm(id) {

            Swal.fire({
                title: "Memuat data...",
                text: "Mohon tunggu.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("user_action.php?action=get&id=" + encodeURIComponent(id))

                .then(response => {

                    if (!response.ok) {
                        throw new Error("Gagal mengambil data user.");
                    }

                    return response.json();

                })

                .then(data => {

                    Swal.close();

                    if (!data || !data.id) {
                        throw new Error("Data user tidak valid.");
                    }

                    document.getElementById("modalTitle").innerHTML =
                        '<i class="fa-solid fa-user-pen"></i><span>Edit User</span>';

                    document.getElementById("userId").value =
                        data.id ?? "";

                    document.getElementById("nama").value =
                        data.nama ?? "";

                    document.getElementById("username").value =
                        data.username ?? "";

                    document.getElementById("password").value =
                        "";

                    document.getElementById("role").value =
                        data.role ?? "";

                    document.getElementById("jabatan").value =
                        data.jabatan ?? "";

                    document.getElementById("fraksi").value =
                        data.fraksi ?? "";

                    document.getElementById("komisi").value =
                        data.komisi ?? "";

                    document.getElementById("pangkat").value =
                        data.pangkat ?? "";

                    document.getElementById("golongan").value =
                        data.golongan ?? "";

                    document.getElementById("wa_phone").value =
                        data.wa_phone ?? "";

                    toggleFields();

                    modal.classList.add("show");

                    document.body.style.overflow = "hidden";

                })

                .catch(error => {

                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: error.message || "Data user tidak dapat dimuat.",
                        confirmButtonColor: "#10294d"
                    });

                });

        }


        /* =========================
           FIELD DINAMIS
        ========================= */

        document.getElementById("jabatan")
            .addEventListener("change", toggleFields);


        function toggleFields() {

            const jabatan =
                document.getElementById("jabatan").value;

            const fraksi =
                document.getElementById("fraksi-group");

            const komisi =
                document.getElementById("komisi-group");

            const pangkat =
                document.getElementById("pangkat-group");

            const golongan =
                document.getElementById("golongan-group");

            const isDPRD =
                jabatan.includes("DPRD");

            if (isDPRD) {

                fraksi.style.display = "flex";
                komisi.style.display = "flex";

                pangkat.style.display = "none";
                golongan.style.display = "none";

            } else {

                fraksi.style.display = "none";
                komisi.style.display = "none";

                pangkat.style.display = "flex";
                golongan.style.display = "flex";

            }

        }


        /* =========================
           SIMPAN USER
        ========================= */

        userForm.addEventListener("submit", function (event) {

            event.preventDefault();

            const formData = new FormData(userForm);

            Swal.fire({
                title: "Menyimpan data...",
                text: "Mohon tunggu.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("user_action.php?action=save", {

                method: "POST",
                body: formData

            })

                .then(response => response.text())

                .then(result => {

                    result = result.trim();

                    if (result === "OK") {

                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Data user berhasil disimpan.",
                            confirmButtonColor: "#10294d"
                        }).then(() => {

                            location.reload();

                        });

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: result || "Data user gagal disimpan.",
                            confirmButtonColor: "#10294d"
                        });

                    }

                })

                .catch(error => {

                    Swal.fire({
                        icon: "error",
                        title: "Terjadi Kesalahan",
                        text: "Tidak dapat terhubung ke server.",
                        confirmButtonColor: "#10294d"
                    });

                    console.error(error);

                });

        });


        /* =========================
           HAPUS USER
        ========================= */

        function deleteUser(id) {

            Swal.fire({

                title: "Hapus User?",

                text: "Data yang dihapus tidak bisa dikembalikan!",

                icon: "warning",

                showCancelButton: true,

                confirmButtonText: "Ya, Hapus",

                cancelButtonText: "Batal",

                confirmButtonColor: "#dc3545",

                cancelButtonColor: "#64748b",

                reverseButtons: true

            }).then(result => {

                if (!result.isConfirmed) {
                    return;
                }

                Swal.fire({
                    title: "Menghapus...",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(
                    "user_action.php?action=delete&id=" +
                    encodeURIComponent(id)
                )

                    .then(response => response.text())

                    .then(result => {

                        result = result.trim();

                        if (result === "OK") {

                            Swal.fire({

                                icon: "success",

                                title: "Terhapus!",

                                text: "User berhasil dihapus.",

                                confirmButtonColor: "#10294d"

                            }).then(() => {

                                location.reload();

                            });

                        } else {

                            Swal.fire({

                                icon: "error",

                                title: "Gagal!",

                                text: result || "User gagal dihapus.",

                                confirmButtonColor: "#10294d"

                            });

                        }

                    })

                    .catch(error => {

                        Swal.fire({

                            icon: "error",

                            title: "Terjadi Kesalahan",

                            text: "Tidak dapat terhubung ke server.",

                            confirmButtonColor: "#10294d"

                        });

                        console.error(error);

                    });

            });

        }


        /* Jalankan saat halaman pertama kali dibuka */
        toggleFields();

    </script>

</body>

</html>