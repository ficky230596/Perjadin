<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ketua') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ketua</title>
    <link rel="stylesheet" href="assets/css/global.css">
</head>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f4f6fb;
    }
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 230px;
        height: 100vh;
        background: linear-gradient(180deg, #0056b3 0%, #007bff 100%);
        color: #fff;
        box-shadow: 2px 0 8px rgba(0,0,0,0.07);
        display: flex;
        flex-direction: column;
        z-index: 100;
    }
    .sidebar .sidebar-header {
        padding: 32px 24px 16px 24px;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: 1px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .sidebar nav {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 24px 0;
    }
    .sidebar nav a {
        color: #fff;
        text-decoration: none;
        padding: 12px 32px;
        font-size: 1.05rem;
        font-weight: 500;
        border-left: 4px solid transparent;
        transition: background 0.2s, border-color 0.2s, color 0.2s;
        margin-bottom: 4px;
    }
    .sidebar nav a.active,
    .sidebar nav a:hover {
        background: rgba(255,255,255,0.08);
        border-left: 4px solid #ffc107;
        color: #ffc107;
    }
    .main-content {
        margin-left: 230px;
        padding: 32px 40px;
        min-height: 100vh;
    }
    @media (max-width: 900px) {
        .sidebar {
            width: 70px;
        }
        .sidebar .sidebar-header {
            font-size: 1rem;
            padding: 24px 10px 10px 10px;
        }
        .sidebar nav a {
            padding: 10px 10px;
            font-size: 0.95rem;
        }
        .main-content {
            margin-left: 70px;
            padding: 24px 10px;
        }
    }
</style>
<body>
    <header style="background: linear-gradient(90deg, #0056b3 0%, #007bff 100%); color: #fff; padding: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
            <h2 style="margin: 0; font-size: 2rem; letter-spacing: 1px; font-weight: 700;">
                <span style="color: #ffc107;">&#9889;</span> Sistem Perjadin DPRD <span style="font-size:1.2rem;font-weight:400;">- Ketua</span>
            </h2>
            <nav>
                <a href="ketua_dashboard.php" style="color: #fff; text-decoration: none; margin-right: 24px; font-weight: 500; transition: color 0.2s;">Dashboard</a>
                <a href="../logout.php" style="color: #ffc107; text-decoration: none; font-weight: 600; border: 1px solid #ffc107; padding: 6px 18px; border-radius: 20px; transition: background 0.2s, color 0.2s;">Logout</a>
            </nav>
        </div>
    </header>