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
    <title>Management User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 0.9em;
        }

        .btn-add {
            background-color: #2ecc71;
            color: white;
        }

        .btn-edit {
            background-color: #f39c12;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: #fff;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            width: 45%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            flex: 1 1 48%;
            display: flex;
            flex-direction: column;
        }

        #userForm {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        #userForm button {
            flex: 1 1 100%;
            margin-top: 10px;
        }

        .form-group label {
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <?php include 'header_umum.php'; ?>
    <?php include 'sidebar_umum.php'; ?>

    <div class="main-content-umum">
        <h1>Management User</h1>
        <button class="btn btn-add" id="btnAdd"><i class="fa fa-plus icon"></i> Tambah User</button>

        <table>
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
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= ucfirst($row['role']); ?></td>
                        <td><?= $row['jabatan'] ?: '-'; ?></td>
                        <td><?= $row['fraksi'] ?: '-'; ?></td>
                        <td><?= $row['komisi'] ?: '-'; ?></td>
                        <td><?= $row['pangkat'] ?: '-'; ?></td>
                        <td><?= $row['golongan'] ?: '-'; ?></td>
                        <td><?= $row['wa_phone'] ?: '-'; ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="openForm(<?= $row['id']; ?>)"><i class="fa fa-pen"></i> Edit</button>
                            <button class="btn btn-delete" onclick="deleteUser(<?= $row['id']; ?>)"><i class="fa fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Tambah User</h2>
            <form id="userForm">
                <input type="hidden" name="id" id="userId">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diganti">
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="sekwan">Sekwan</option>
                        <option value="umum">Umum</option>
                        <option value="ketua">Ketua</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jabatan</label>
                    <select name="jabatan" id="jabatan" class="form-control" required>
                        <option value="">-- Pilih Jabatan --</option>

                        <optgroup label="Anggota DPRD">
                            <option value="Ketua DPRD">Ketua DPRD</option>
                            <option value="Wakil Ketua I">Wakil Ketua I</option>
                            <option value="Wakil Ketua II">Wakil Ketua II</option>
                            <option value="Anggota DPRD">Anggota DPRD</option>
                        </optgroup>

                        <optgroup label="Sekretariat DPRD (ASN / P3K)">
                            <option value="Sekretaris DPRD">Sekretaris DPRD</option>
                            <option value="Kepala Bagian Umum dan Keuangan">Kepala Bagian Umum dan Keuangan</option>
                            <option value="Kepala Bagian Persidangan dan Perundangan">Kepala Bagian Persidangan dan Perundangan</option>
                            <option value="Kasubbag Keuangan">Kasubbag Keuangan</option>
                            <option value="Kasubbag Umum dan Rumah Tangga">Kasubbag Umum dan Rumah Tangga</option>
                            <option value="Kasubbag Risalah dan Persidangan">Kasubbag Risalah dan Persidangan</option>
                            <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                            <option value="Bendahara Penerimaan">Bendahara Penerimaan</option>
                            <option value="Bendahara Barang">Bendahara Barang</option>
                            <option value="Analis Kebijakan">Analis Kebijakan</option>
                            <option value="Staf Administrasi">Staf Administrasi</option>
                            <option value="Petugas TU">Petugas Tata Usaha</option>
                            <option value="Tenaga Kebersihan">Tenaga Kebersihan</option>
                            <option value="P3K">Pegawai Pemerintah dengan Perjanjian Kerja (P3K)</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group" id="fraksi-group" style="display:none;">
                    <label>Fraksi</label>
                    <select name="fraksi" id="fraksi">
                        <option value="">-- Pilih Fraksi --</option>
                        <option value="Fraksi Golkar">Fraksi Golkar</option>
                        <option value="Fraksi PDIP">Fraksi PDIP</option>
                        <option value="Fraksi NasDem">Fraksi NasDem</option>
                        <option value="Fraksi Gerindra">Fraksi Gerindra</option>
                        <option value="Fraksi Demokrat">Fraksi Demokrat</option>
                        <option value="Fraksi Gabungan">Fraksi Gabungan</option>
                    </select>
                </div>

                <div class="form-group" id="komisi-group" style="display:none;">
                    <label>Komisi</label>
                    <select name="komisi" id="komisi">
                        <option value="">-- Pilih Komisi --</option>
                        <option value="Komisi I">Komisi I</option>
                        <option value="Komisi II">Komisi II</option>
                        <option value="Komisi III">Komisi III</option>
                    </select>
                </div>

                <div class="form-group" id="pangkat-group">
                    <label>Pangkat</label>
                    <select name="pangkat" id="pangkat">
                        <option value="">-- Pilih Pangkat --</option>
                        <option value="Penata Muda (III/a)">Penata Muda (III/a)</option>
                        <option value="Penata (III/b)">Penata (III/b)</option>
                        <option value="Pembina (IV/a)">Pembina (IV/a)</option>
                        <option value="Pembina Tingkat I (IV/b)">Pembina Tingkat I (IV/b)</option>
                        <option value="Pembina Utama Muda (IV/c)">Pembina Utama Muda (IV/c)</option>
                    </select>
                </div>

                <div class="form-group" id="golongan-group">
                    <label>Golongan</label>
                    <select name="golongan" id="golongan">
                        <option value="">-- Pilih Golongan --</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nomor WA</label>
                    <input type="text" name="wa_phone" id="wa_phone" placeholder="628xxxxxxxxxx">
                </div>

                <button type="submit" class="btn btn-add">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        let modal = document.getElementById("userModal");
        let span = document.getElementsByClassName("close")[0];

        // buka form tambah user
        document.getElementById("btnAdd").addEventListener("click", () => {
            document.getElementById("modalTitle").innerText = "Tambah User";
            document.getElementById("userForm").reset();
            document.getElementById("userId").value = "";
            toggleFields();
            modal.style.display = "block";
        });

        span.onclick = () => modal.style.display = "none";
        window.onclick = e => {
            if (e.target == modal) modal.style.display = "none";
        };

        function openForm(id) {
            fetch('user_action.php?action=get&id=' + id)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("modalTitle").innerText = "Edit User";
                    document.getElementById("userId").value = data.id;
                    document.getElementById("nama").value = data.nama;
                    document.getElementById("username").value = data.username;
                    document.getElementById("password").value = "";
                    document.getElementById("role").value = data.role;
                    document.getElementById("jabatan").value = data.jabatan;
                    document.getElementById("fraksi").value = data.fraksi;
                    document.getElementById("komisi").value = data.komisi;
                    document.getElementById("pangkat").value = data.pangkat;
                    document.getElementById("golongan").value = data.golongan;
                    document.getElementById("wa_phone").value = data.wa_phone;
                    toggleFields();
                    modal.style.display = "block";
                });
        }

        // tampilkan / sembunyikan field dinamis
        document.getElementById("jabatan").addEventListener("change", toggleFields);

        function toggleFields() {
            let jabatan = document.getElementById("jabatan").value;
            let fraksi = document.getElementById("fraksi-group");
            let komisi = document.getElementById("komisi-group");
            let pangkat = document.getElementById("pangkat-group");
            let golongan = document.getElementById("golongan-group");

            if (jabatan.includes("DPRD")) {
                fraksi.style.display = "block";
                komisi.style.display = "block";
                pangkat.style.display = "none";
                golongan.style.display = "none";
            } else {
                fraksi.style.display = "none";
                komisi.style.display = "none";
                pangkat.style.display = "block";
                golongan.style.display = "block";
            }
        }

        // simpan data
        document.getElementById("userForm").addEventListener("submit", e => {
            e.preventDefault();
            let formData = new FormData(e.target);
            fetch('user_action.php?action=save', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(res => {
                    if (res === "OK") {
                        Swal.fire('Sukses!', 'Data user berhasil disimpan.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', res, 'error');
                    }
                });
        });

        function deleteUser(id) {
            Swal.fire({
                title: 'Hapus User?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('user_action.php?action=delete&id=' + id)
                        .then(res => res.text())
                        .then(res => {
                            if (res === "OK") {
                                Swal.fire('Terhapus!', 'User berhasil dihapus.', 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', res, 'error');
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>