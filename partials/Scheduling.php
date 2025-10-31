<?php
function getScheduledQueue($pdo, $status_filter)
{
    $stmt = $pdo->prepare("SELECT p.*, u.role AS role_pemohon, u.jabatan FROM pengajuan p JOIN users u ON p.pegawai_id = u.id WHERE p.status = ? ORDER BY p.waktu_pengajuan ASC");
    $stmt->execute([$status_filter]);
    $queues = $stmt->fetchAll();
    foreach ($queues as &$q) {
        $bonus_jabatan = ($q['role_pemohon'] === 'ketua' ? 3 : ($q['role_pemohon'] === 'sekwan' ? 2 : 1));
        $urgensi_skor = ($q['urgensi'] === 'tinggi' ? 3 : ($q['urgensi'] === 'sedang' ? 2 : 1));
        $q['prioritas_skor'] = $urgensi_skor + $bonus_jabatan;
    }
    usort($queues, fn($a, $b) => $b['prioritas_skor'] <=> $a['prioritas_skor']);
    return $queues;
}
