<?php
/**
 * Admin Layout - Header
 */
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/csrf.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';

require_admin();

$admin        = current_admin();
$page_title   = $page_title ?? 'Dashboard';
$active_menu  = $active_menu ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> — Admin CMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-heart me-2"></i>
        <span>Wedding CMS</span>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Menu Utama</div>

        <a href="<?= admin_url('dashboard.php') ?>" class="sidebar-link <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="sidebar-section">Konten</div>

        <a href="<?= admin_url('settings/index.php') ?>" class="sidebar-link <?= $active_menu === 'settings' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
        <a href="<?= admin_url('couple/index.php') ?>" class="sidebar-link <?= $active_menu === 'couple' ? 'active' : '' ?>">
            <i class="fas fa-heart"></i> Mempelai
        </a>
        <a href="<?= admin_url('events/index.php') ?>" class="sidebar-link <?= $active_menu === 'events' ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Acara
        </a>
        <a href="<?= admin_url('story/index.php') ?>" class="sidebar-link <?= $active_menu === 'story' ? 'active' : '' ?>">
            <i class="fas fa-book-open"></i> Love Story
        </a>
        <a href="<?= admin_url('gallery/index.php') ?>" class="sidebar-link <?= $active_menu === 'gallery' ? 'active' : '' ?>">
            <i class="fas fa-images"></i> Galeri
        </a>
        <a href="<?= admin_url('music/index.php') ?>" class="sidebar-link <?= $active_menu === 'music' ? 'active' : '' ?>">
            <i class="fas fa-music"></i> Musik
        </a>
        <a href="<?= admin_url('gift/index.php') ?>" class="sidebar-link <?= $active_menu === 'gift' ? 'active' : '' ?>">
            <i class="fas fa-gift"></i> Wedding Gift
        </a>

        <div class="sidebar-section">Tamu</div>

        <a href="<?= admin_url('guests/index.php') ?>" class="sidebar-link <?= $active_menu === 'guests' ? 'active' : '' ?>">
            <i class="fas fa-paper-plane"></i> Link Undangan Tamu
        </a>
        <a href="<?= admin_url('rsvp/index.php') ?>" class="sidebar-link <?= $active_menu === 'rsvp' ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i> RSVP
        </a>
        <a href="<?= admin_url('wishes/index.php') ?>" class="sidebar-link <?= $active_menu === 'wishes' ? 'active' : '' ?>">
            <i class="fas fa-comments"></i> Ucapan
        </a>

        <div class="sidebar-section">Sistem</div>

        <a href="<?= admin_url('users/index.php') ?>" class="sidebar-link <?= $active_menu === 'users' ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i> Admin Users
        </a>
        <a href="<?= base_url() ?>" target="_blank" class="sidebar-link">
            <i class="fas fa-external-link-alt"></i> Lihat Website
        </a>
        <a href="<?= admin_url('logout.php') ?>" class="sidebar-link text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">

    <!-- Topbar -->
    <div class="topbar">
        <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-title"><?= e($page_title) ?></div>
        <div class="topbar-user">
            <i class="fas fa-user-circle me-1"></i>
            <?= e($admin['name']) ?>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="container-fluid px-4 pt-3">
        <?= render_flash() ?>
    </div>

    <!-- Page Content -->
    <div class="container-fluid px-4 pb-4">
