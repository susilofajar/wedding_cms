-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 18 Sep 2026 pada 02.22
-- Versi server: 9.4.0
-- Versi PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42900476_wedding_cms`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int UNSIGNED NOT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `qris_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `bank_name`, `account_number`, `account_holder`, `bank_logo`, `qris_image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bank Mandiri', '1290012545071', 'Indah Shafira Handayani', '', '', 1, '2026-09-05 13:00:51', '2026-09-08 04:48:34'),
(2, 'Bank BCA', '8720732344', 'Maulana Rusdan Sabilly', '', '', 1, '2026-09-05 13:00:51', '2026-09-08 04:49:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `couples`
--

CREATE TABLE `couples` (
  `id` int UNSIGNED NOT NULL,
  `bride_full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_father` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_mother` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_order` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bride_instagram` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_father` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_mother` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_order` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groom_instagram` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `couples`
--

INSERT INTO `couples` (`id`, `bride_full_name`, `bride_nickname`, `bride_photo`, `bride_father`, `bride_mother`, `bride_order`, `bride_instagram`, `groom_full_name`, `groom_nickname`, `groom_photo`, `groom_father`, `groom_mother`, `groom_order`, `groom_instagram`, `description`, `updated_at`) VALUES
(1, 'INDAH SHAFIRA HANDAYANI', 'Indah', '', 'Sarmada', 'Nurlaela', 'Putri ke - Dua', '', 'MAULANA RUSDAN SABILLY', 'Billy', '', 'Mansur', 'Samah Evi', 'Putra ke - Dua', '', 'Dua hati yang dipersatukan dalam ikatan suci pernikahan, melangkah bersama meraih ridho ilahi.', '2026-09-09 09:08:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reception',
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `maps_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-calendar-alt',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `events`
--

INSERT INTO `events` (`id`, `name`, `type`, `event_date`, `start_time`, `end_time`, `location`, `address`, `maps_url`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Akad Nikah', 'akad', '2026-10-03', '09:00:00', '11:00:00', 'Lapangan Basket', 'Jl. Sepakat V / VIII, Cilangkap Cipayung Jakarta Timur', 'https://maps.google.com/?q=Lapangan+Basket+Jl.+Sepakat+V+VIII+Cilangkap+Cipayung+Jakarta+Timur', 'Prosesi akad nikah dan ijab kabul dengan penuh khidmat dan rasa syukur.', 'fas fa-heart', 1, 1, '2026-09-05 13:00:50', '2026-09-05 13:00:50'),
(2, 'Resepsi Pernikahan', 'reception', '2026-10-03', '11:00:00', NULL, 'Lapangan Basket', 'Jl. Sepakat V / VIII, Cilangkap Cipayung Jakarta Timur', 'https://maps.google.com/?q=Lapangan+Basket+Jl.+Sepakat+V+VIII+Cilangkap+Cipayung+Jakarta+Timur', 'Merayakan momen bahagia bersama keluarga, sahabat, dan rekan tercinta.', 'fas fa-glass-cheers', 2, 1, '2026-09-05 13:00:50', '2026-09-08 04:49:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery`
--

CREATE TABLE `gallery` (
  `id` int UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `caption` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `image`, `title`, `caption`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'ce54030060ab8eaa095638698ad650c0.jpg', '', '', 0, 1, '2026-09-05 15:45:05', '2026-09-09 09:22:51'),
(7, '07ad6c71b85ae7d5b8b0b7f997c855a1.jpg', '', '', 3, 1, '2026-09-05 15:46:56', '2026-09-09 09:23:27'),
(8, 'da7add463117fd7c694ba371e4a05019.jpg', '', '', 1, 1, '2026-09-08 03:18:26', '2026-09-09 09:23:03'),
(9, '0b2349c96ae877d78223e888d994f0a7.jpg', '', '', 2, 1, '2026-09-08 03:18:47', '2026-09-09 09:23:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `love_stories`
--

CREATE TABLE `love_stories` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `story_date` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `music`
--

CREATE TABLE `music` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `music`
--

INSERT INTO `music` (`id`, `title`, `filename`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'You Are My Everything', 'c59ae7a7febadc3209d695dd2034b24d.mp3', 1, '2026-09-09 09:33:48', '2026-09-09 09:33:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rsvps`
--

CREATE TABLE `rsvps` (
  `id` int UNSIGNED NOT NULL,
  `guest_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendance` enum('hadir','tidak_hadir','ragu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `guest_count` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_title', 'Undangan Pernikahan Indah & Billy', '2026-09-08 03:22:51'),
(2, 'invitation_title', 'The Wedding Of', '2026-09-05 13:00:50'),
(3, 'meta_title', 'The Wedding Of Indah & Billy', '2026-09-08 06:18:57'),
(4, 'meta_description', 'Kami mengundang Anda untuk hadir dan berbagi kebahagiaan di hari istimewa pernikahan Indah & Billy.', '2026-09-08 03:22:51'),
(5, 'primary_color', '#8b5e5e', '2026-09-05 13:00:50'),
(6, 'secondary_color', '#d8b4a0', '2026-09-05 13:00:50'),
(7, 'background_color', '#fffaf7', '2026-09-05 13:00:50'),
(8, 'text_color', '#3f3f46', '2026-09-05 13:00:50'),
(9, 'font_heading', 'Playfair Display', '2026-09-05 13:00:50'),
(10, 'font_body', 'Plus Jakarta Sans', '2026-09-05 13:00:50'),
(11, 'wedding_quote', '\"Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya diantaramu rasa kasih dan sayang.\" — QS. Ar-Rum: 21', '2026-09-05 13:00:50'),
(12, 'logo', '', '2026-09-05 13:00:50'),
(13, 'favicon', '74cbaaf6cd4e202d16202397df8c4bbe.png', '2026-09-05 15:28:40'),
(14, 'social_image', '03f135d01877f5c8d0083fbf5528965c.jpg', '2026-09-09 12:35:20'),
(15, 'website_status', 'active', '2026-09-05 13:00:50'),
(16, 'show_section_couple', '1', '2026-09-05 13:00:50'),
(17, 'show_section_story', '1', '2026-09-05 13:00:50'),
(18, 'show_section_events', '1', '2026-09-05 13:00:50'),
(19, 'show_section_gallery', '1', '2026-09-05 13:00:50'),
(20, 'show_section_gift', '1', '2026-09-05 13:00:50'),
(21, 'show_section_rsvp', '1', '2026-09-05 13:00:50'),
(22, 'show_section_wishes', '1', '2026-09-05 13:00:50'),
(23, 'show_section_music', '1', '2026-09-06 04:45:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `role` enum('superadmin','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$V0rc.ZbhvxA6YHEhZduJCO5pHGUIPQZelxoZlGK/sqNiop2U/eQoq', 'Administrator', 'admin@example.com', 'superadmin', '2026-09-05 13:00:50', '2026-09-05 13:00:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wishes`
--

CREATE TABLE `wishes` (
  `id` int UNSIGNED NOT NULL,
  `guest_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendance` enum('hadir','tidak_hadir','ragu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `status` enum('pending','approved','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `couples`
--
ALTER TABLE `couples`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `love_stories`
--
ALTER TABLE `love_stories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `music`
--
ALTER TABLE `music`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rsvps`
--
ALTER TABLE `rsvps`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `wishes`
--
ALTER TABLE `wishes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `couples`
--
ALTER TABLE `couples`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `love_stories`
--
ALTER TABLE `love_stories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `music`
--
ALTER TABLE `music`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `rsvps`
--
ALTER TABLE `rsvps`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `wishes`
--
ALTER TABLE `wishes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
