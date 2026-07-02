-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 01 Jul 2026 pada 01.27
-- Versi server: 8.0.45
-- Versi PHP: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `intratest_ext`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `abouts`
--

INSERT INTO `abouts` (`id`, `version`, `user_id`, `description`, `release_date`, `created_at`, `updated_at`) VALUES
(3, '1.0.0', 17, '<p><strong>Keterangan Versi </strong>:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><em>Update Major : Perubahan besar terjadi pada arsitektur, fitur inti, atau cara penggunaan program yang tidak kompatibel dengan versi sebelumnya. Contoh: 1.0.0, 2.0.0.</em></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><em>Update Minor : Penambahan fitur baru atau menu baru tanpa merusak kompatibilitas dengan versi sebelumunya. Contoh: 1.1.0, 1.2.0.</em></li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><em>Update Patch : Perbaikan bug yang kompatibel dengan versi sebelumnya. Contoh: 1.0.1, 1.0.2</em></li></ol><p><br></p><p><strong>Release Pertama :</strong></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Employee Profile</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Medical Check Up</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Role Access mode Admin and Mode Employee</li></ol><p><br></p>', '2023-11-01', '2025-07-07 07:13:45', '2025-07-07 08:25:14'),
(4, '1.1.0', 17, '<ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Internal Rules &amp; Benefit</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Calendar</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>E-PKB</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>News &amp; Event</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Guest Visit (Security)</li></ol><p><br></p>', '2024-03-29', '2025-07-07 08:04:08', '2025-07-07 08:14:08'),
(5, '1.2.0', 17, '<ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Booking Meeting Room</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Clinic</li></ol>', '2024-09-16', '2025-07-07 08:04:50', '2025-07-07 08:25:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `areas`
--

CREATE TABLE `areas` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `areas`
--

INSERT INTO `areas` (`id`, `kode`, `name`, `created_at`, `updated_at`) VALUES
(1, 'HQF', 'HEADQUARTERS / FACTORY', '2023-08-31 09:07:07', '2024-11-13 07:22:05'),
(2, 'HO', 'HEAD OFFICE', '2023-08-31 03:10:46', '2023-10-31 02:31:15'),
(3, 'EJ1', 'EJ1', '2023-08-31 03:11:01', '2023-11-09 07:08:13'),
(4, 'EJ2', 'EJ2', '2023-08-31 03:11:12', '2023-11-09 07:08:23'),
(5, 'WJ', 'WEST JAVA', '2023-08-31 03:11:25', '2023-08-31 03:11:25'),
(6, 'JKT1', 'JAKARTA 1', '2023-08-31 03:11:31', '2024-11-04 08:10:05'),
(7, 'OIWJ1', 'OIWJ1', '2023-08-31 03:11:44', '2023-11-09 07:08:40'),
(8, 'OIWJ2', 'OIWJ2', '2023-08-31 03:11:52', '2023-11-09 07:08:52'),
(9, 'MDN', 'MEDAN', '2023-08-31 03:11:59', '2023-08-31 03:11:59'),
(10, 'KLM', 'KALIMANTAN', '2023-08-31 03:12:09', '2023-08-31 03:12:09'),
(11, 'MKS', 'MAKASSAR', '2023-08-31 03:12:16', '2023-08-31 03:12:16'),
(12, 'CJ1', 'CJ1', '2023-08-31 03:12:42', '2023-11-09 07:07:32'),
(13, 'CJ2', 'CJ2', '2023-08-31 03:12:47', '2023-11-09 07:07:59'),
(19, 'JKT2', 'JAKARTA 2', '2024-11-04 08:09:32', '2024-11-04 08:09:32'),
(20, 'OIE', 'OIE', '2024-11-07 08:35:45', '2024-11-07 08:35:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_disposals`
--

CREATE TABLE `asset_disposals` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requester_id` bigint UNSIGNED NOT NULL,
  `buyer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `validated_at` datetime DEFAULT NULL COMMENT 'this date time is for buyer validation time',
  `buyer_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `current_step` int DEFAULT '1',
  `doc_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `current_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_disposal_items`
--

CREATE TABLE `asset_disposal_items` (
  `id` bigint UNSIGNED NOT NULL,
  `asset_disposal_id` bigint UNSIGNED NOT NULL,
  `it_asset_id` bigint UNSIGNED NOT NULL,
  `current_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'the asset status at the time disposal requested',
  `buy_price` decimal(15,2) NOT NULL,
  `sale_price` decimal(15,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_disposal_logs`
--

CREATE TABLE `asset_disposal_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `asset_disposal_id` bigint UNSIGNED NOT NULL,
  `disposal_approval_path_id` bigint UNSIGNED DEFAULT NULL,
  `for_buyer` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `actioned_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_histories`
--

CREATE TABLE `asset_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `it_asset_id` bigint UNSIGNED NOT NULL,
  `action_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'Performed by',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_maintenances`
--

CREATE TABLE `asset_maintenances` (
  `id` bigint UNSIGNED NOT NULL,
  `task_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maintenance_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_maintenance_items`
--

CREATE TABLE `asset_maintenance_items` (
  `id` bigint UNSIGNED NOT NULL,
  `asset_maintenance_id` bigint UNSIGNED NOT NULL,
  `it_asset_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_types`
--

CREATE TABLE `asset_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_lifespan` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asset_types`
--

INSERT INTO `asset_types` (`id`, `name`, `estimated_lifespan`, `created_at`, `updated_at`) VALUES
(1, 'Server', 60, '2026-03-02 14:38:52', '2026-03-02 14:38:52'),
(2, 'Computer', 60, '2026-03-02 14:39:13', '2026-04-24 11:43:30'),
(4, 'Handphone', 36, '2026-03-03 10:05:14', '2026-03-03 10:05:36'),
(7, 'UPS', 60, '2026-03-03 10:06:26', '2026-03-03 10:06:26'),
(8, 'Switch', 36, '2026-03-03 10:06:57', '2026-03-03 10:06:57'),
(9, 'Firewall', 60, '2026-03-03 10:07:13', '2026-03-03 10:07:13'),
(10, 'Access Point', 36, '2026-03-03 10:07:27', '2026-03-03 10:07:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workhour_id` bigint UNSIGNED DEFAULT NULL,
  `workhour_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` time DEFAULT NULL,
  `end_date` time DEFAULT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status_check_in` enum('on_time','late') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_check_out` enum('on_time','early_leave','overtime') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_status` enum('present','leave','sick','holiday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendance_calendars`
--

CREATE TABLE `attendance_calendars` (
  `id` bigint UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `type` enum('national','company','cultural','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_hq` tinyint(1) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendance_calendars`
--

INSERT INTO `attendance_calendars` (`id`, `date`, `type`, `is_hq`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2026-01-01 00:00:00', 'national', 1, 'Tahun Baru Masehi', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(2, '2026-01-01 00:00:00', 'national', 0, 'Tahun Baru Masehi', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(3, '2026-04-03 00:00:00', 'national', 1, 'Wafat Isa Almasih', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(4, '2026-04-03 00:00:00', 'national', 0, 'Wafat Isa Almasih', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(5, '2026-04-05 00:00:00', 'national', 1, 'Paskah', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(6, '2026-04-05 00:00:00', 'national', 0, 'Paskah', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(7, '2026-05-01 00:00:00', 'national', 1, 'Hari Buruh Internasional', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(8, '2026-05-01 00:00:00', 'national', 0, 'Hari Buruh Internasional', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(9, '2026-05-14 00:00:00', 'national', 1, 'Kenaikan Isa Almasih', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(10, '2026-05-14 00:00:00', 'national', 0, 'Kenaikan Isa Almasih', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(11, '2026-06-01 00:00:00', 'national', 1, 'Hari Lahir Pancasila', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(12, '2026-06-01 00:00:00', 'national', 0, 'Hari Lahir Pancasila', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(13, '2026-08-17 00:00:00', 'national', 1, 'Hari Ulang Tahun Kemerdekaan Republik Indonesia', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(14, '2026-08-17 00:00:00', 'national', 0, 'Hari Ulang Tahun Kemerdekaan Republik Indonesia', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:03'),
(15, '2026-12-25 00:00:00', 'national', 1, 'Hari Raya Natal', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:04'),
(16, '2026-12-25 00:00:00', 'national', 0, 'Hari Raya Natal', 1, '2026-04-13 07:17:43', '2026-06-18 16:04:04'),
(17, '2026-04-16 00:00:00', 'company', 1, 'test', 1, '2026-04-13 07:18:05', '2026-04-13 07:18:05'),
(18, '2026-04-16 00:00:00', 'company', 0, 'test', 1, '2026-04-13 07:18:19', '2026-04-13 07:18:19'),
(21, '2026-04-22 00:00:00', 'company', 1, 'libur bersama', 1, '2026-04-13 07:18:54', '2026-04-13 07:18:54'),
(23, '2026-04-22 00:00:00', 'company', 0, 'libur bersama', 1, '2026-04-13 07:19:48', '2026-04-13 07:19:48'),
(25, '2026-04-21 00:00:00', 'national', 1, 'test2', 1, '2026-04-13 07:53:51', '2026-04-13 07:53:51'),
(26, '2026-04-21 00:00:00', 'national', 0, 'test2', 1, '2026-04-13 08:05:47', '2026-04-13 08:05:47'),
(27, '2026-05-05 00:00:00', 'national', 1, 'test', 1, '2026-05-04 08:26:04', '2026-05-04 08:26:04'),
(29, '2026-05-07 00:00:00', 'national', 1, 'test', 1, '2026-05-07 03:09:45', '2026-05-07 03:09:45'),
(30, '2026-06-16 00:00:00', 'national', 1, 'libur', 1, '2026-06-15 05:11:57', '2026-06-15 05:11:57'),
(31, '2026-06-16 00:00:00', 'national', 0, 'libur', 1, '2026-06-15 05:12:12', '2026-06-15 05:12:12'),
(34, '2026-06-03 00:00:00', 'national', 1, 'test', 1, '2026-06-19 10:07:04', '2026-06-19 10:07:04'),
(35, '2026-06-03 00:00:00', 'cultural', 1, 'test', 1, '2026-06-19 10:07:23', '2026-06-19 10:07:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendance_permits`
--

CREATE TABLE `attendance_permits` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `work_in` time NOT NULL,
  `work_out` time NOT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrd_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `hrd_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('approved','rejected','waiting') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `approved_by_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_at` datetime DEFAULT NULL,
  `reason_reject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_time_in` time DEFAULT NULL,
  `actual_time_out` time DEFAULT NULL,
  `security_name_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_name_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_knowledge_1` tinyint(1) NOT NULL DEFAULT '0',
  `security_knowledge_2` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendance_permits`
--

INSERT INTO `attendance_permits` (`id`, `employee_id`, `nik`, `employee_name`, `position`, `area`, `department`, `reason`, `type`, `start_date`, `end_date`, `start_time`, `end_time`, `work_in`, `work_out`, `attachment`, `hrd_knowledge`, `hrd_name`, `status`, `approved_by_name`, `approved_by_position`, `approved_by_at`, `reason_reject`, `approval_token`, `created_by`, `updated_by`, `actual_time_in`, `actual_time_out`, `security_name_1`, `security_name_2`, `security_knowledge_1`, `security_knowledge_2`, `created_at`, `updated_at`) VALUES
(1, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 'urusan keluarga', 'earlyout', '2026-06-15', '2026-06-15', NULL, '12:00:00', '07:45:00', '16:30:00', NULL, 1, 'TESTEMPLOYEE', 'approved', 'TESTEMPLOYEE', 'TECHNICIAN', '2026-06-08 15:25:22', NULL, 'c343fdb0-199e-4924-b44d-cd4e70746ff0', 'Steve Satterfield Jr.', 'TESTEMPLOYEE', NULL, NULL, NULL, NULL, 0, 0, '2026-06-08 08:24:59', '2026-06-08 08:26:29'),
(2, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 'test', 'earlyout', '2026-06-16', '2026-06-16', NULL, '12:00:00', '07:45:00', '16:30:00', NULL, 0, NULL, 'approved', 'TESTEMPLOYEE', 'TECHNICIAN', '2026-06-08 15:39:44', NULL, '61bd5b6f-0ff2-41eb-9ef4-b0c38301e499', 'Steve Satterfield Jr.', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-08 08:30:26', '2026-06-08 08:39:44'),
(3, 4130, 'EMP00052', 'Diamond Kerluke', 'KAIZEN DEVELOPMENT GROUP LEADER', 'KALIMANTAN', 'Engineering', 'pengiriman barang penting', 'temporary_out', '2026-06-16', '2026-06-16', '15:00:00', '12:00:00', '07:45:00', '16:30:00', NULL, 1, 'TESTEMPLOYEE', 'approved', 'TESTEMPLOYEE', 'TECHNICIAN', '2026-06-08 15:39:46', NULL, '29336d42-029f-4eaf-9586-9f860af6df6d', 'Diamond Kerluke', 'TESTEMPLOYEE', NULL, NULL, NULL, NULL, 0, 0, '2026-06-08 08:32:44', '2026-06-08 09:00:16'),
(4, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 'test', 'earlyout', '2026-06-17', '2026-06-17', NULL, '12:00:00', '07:45:00', '16:30:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, 'c983f394-73b9-4069-a3af-75d11ec9c27b', 'Steve Satterfield Jr.', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-08 08:43:10', '2026-06-08 08:43:10'),
(5, 1075, '0000000000000000', 'TESTEMPLOYEE', 'TECHNICIAN', 'WEST JAVA', 'HRD & GA', 'test', 'earlyout', '2026-06-18', '2026-06-18', NULL, '12:00:00', '07:45:00', '16:30:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, 'a76f1afe-7c4a-4db5-9a59-c01e48e33be9', 'TESTEMPLOYEE', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-17 07:16:47', '2026-06-17 07:16:47'),
(7, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'ada kepentingan keluarga', 'earlyout', '2026-06-19', '2026-06-19', NULL, NULL, '07:45:00', '16:45:00', NULL, 1, 'FERDISAPUTRO', 'waiting', NULL, NULL, NULL, NULL, '6b7ece82-1f8d-4ef5-ba27-f736250a5859', 'FERDISAPUTRO', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 10:54:31', '2026-06-19 13:13:04'),
(8, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'izin pulang dulu', 'earlyout', '2026-06-22', '2026-06-22', '00:30:00', NULL, '07:45:00', '16:30:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, 'a9c6e7f1-36ca-4b4e-9817-b3ab7fa386b5', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 11:02:51', '2026-06-19 11:02:51'),
(9, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'tesss', 'earlyout', '2026-06-23', '2026-06-23', '12:00:00', NULL, '07:45:00', '16:30:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, '56887ab1-8d9b-4a98-b2e4-724cc10f7120', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 11:03:55', '2026-06-19 11:03:55'),
(10, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'sakit demam', 'sick', '2026-06-15', '2026-06-15', NULL, NULL, '07:45:00', '16:30:00', 'attendance_permits/jM6kqEbdi296hvMjsRlRuvuQovoVXptA9wE5AbJl.jpg', 0, NULL, 'approved', 'FERDISAPUTRO', 'IT STAFF', '2026-06-19 14:24:01', NULL, '410fd52a-ec21-4dcb-94a5-667f54232f3e', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 11:06:04', '2026-06-19 14:24:01'),
(11, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'test', 'earlyout', '2026-07-03', '2026-07-03', NULL, '12:00:00', '07:45:00', '16:45:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, '0488d643-fe63-4974-a114-678e7f19ce9d', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 14:21:40', '2026-06-19 14:21:40'),
(12, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 'test', 'earlyout', '2026-07-15', '2026-07-15', NULL, '12:00:00', '07:45:00', '16:30:00', NULL, 0, NULL, 'waiting', NULL, NULL, NULL, NULL, '7b073bd0-834a-4886-b95a-0de14ac68f75', 'FERDISAPUTRO', NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-06-19 14:22:12', '2026-06-19 14:22:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_record`
--

CREATE TABLE `booking_record` (
  `id` bigint UNSIGNED NOT NULL,
  `brief_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `room_id` int NOT NULL,
  `tipe` enum('internal','external') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('tentative','confirmed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `employee_id` int DEFAULT NULL,
  `repeat_day` json DEFAULT NULL,
  `repeat_week` int DEFAULT NULL,
  `repeat_month` int DEFAULT NULL,
  `repeat_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `booking_record`
--

INSERT INTO `booking_record` (`id`, `brief_description`, `full_description`, `date_start`, `date_end`, `room_id`, `tipe`, `status`, `kode`, `employee_id`, `repeat_day`, `repeat_week`, `repeat_month`, `repeat_status`, `created_at`, `updated_at`) VALUES
(1, 'IT Internship', NULL, '2026-02-13 08:00:00', '2026-02-13 16:30:00', 8, 'external', NULL, '', 196, NULL, NULL, NULL, 'None', '2026-02-13 09:30:24', '2026-02-13 09:30:24'),
(2, 'IT internship', NULL, '2026-02-16 07:00:00', '2026-02-16 12:30:00', 8, 'internal', NULL, '', 196, NULL, NULL, NULL, 'None', '2026-02-16 11:11:19', '2026-02-16 11:11:19'),
(3, 'ss', NULL, '2026-02-16 07:00:00', '2026-02-16 07:30:00', 7, 'internal', NULL, '', 196, NULL, NULL, NULL, 'None', '2026-02-16 15:10:20', '2026-02-16 15:10:20'),
(4, 'BLABLA', NULL, '2026-02-18 07:00:00', '2026-02-18 08:30:00', 8, 'internal', NULL, '', 196, NULL, NULL, NULL, 'None', '2026-02-18 07:58:45', '2026-02-18 07:58:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_cancellations`
--

CREATE TABLE `business_cancellations` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `propose_date` date NOT NULL,
  `reason_cancel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason_other` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `employee_covered_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `company_covered_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_loss_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `status` enum('draft','submitted','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_cancellations`
--

INSERT INTO `business_cancellations` (`id`, `business_trip_id`, `propose_date`, `reason_cancel`, `reason_other`, `employee_covered_amount`, `company_covered_amount`, `total_loss_amount`, `currency`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, '2026-06-08', 'other', 'test', 2500000.00, 2500000.00, 5000000.00, 'IDR', 'approved', '2026-06-08 03:39:05', '2026-06-11 04:24:03'),
(2, 4, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 03:13:22', '2026-06-12 03:30:31'),
(3, 4, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 03:31:03', '2026-06-12 03:42:20'),
(4, 4, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 03:47:15', '2026-06-12 03:47:42'),
(5, 3, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 03:55:47', '2026-06-12 04:10:10'),
(6, 5, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 04:06:27', '2026-06-12 04:09:36'),
(7, 4, '2026-06-12', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-12 07:42:02', '2026-06-15 02:16:54'),
(8, 6, '2026-06-15', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-15 02:06:44', '2026-06-15 02:20:20'),
(14, 6, '2026-06-15', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-15 02:38:16', '2026-06-15 02:41:51'),
(15, 6, '2026-06-15', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-15 02:42:03', '2026-06-15 02:43:01'),
(16, 6, '2026-06-15', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'rejected', '2026-06-15 02:43:09', '2026-06-15 02:48:00'),
(17, 6, '2026-06-15', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'draft', '2026-06-15 02:48:15', '2026-06-15 02:48:15'),
(18, 11, '2026-06-19', 'emergency', NULL, 0.00, 0.00, 0.00, 'IDR', 'approved', '2026-06-19 14:56:25', '2026-06-19 15:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_cancellation_approvals`
--

CREATE TABLE `business_cancellation_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `cancellation_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `status` enum('pending','waiting','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_cancellation_approvals`
--

INSERT INTO `business_cancellation_approvals` (`id`, `cancellation_id`, `approver_id`, `position`, `department`, `level`, `status`, `approved_at`, `reason`, `approval_token`, `created_at`, `updated_at`) VALUES
(1, 1, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-11 04:07:42', NULL, '5c654a75-a4b3-4ce1-9eed-99f73f3665fb', '2026-06-08 03:39:05', '2026-06-11 04:07:42'),
(2, 1, 1058, 'DIRECTOR', 'NA', 2, 'approved', '2026-06-11 04:18:53', NULL, '571ecf00-4343-41d0-ab43-8cbf55c2707e', '2026-06-08 03:39:05', '2026-06-11 04:18:53'),
(3, 1, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'approved', '2026-06-11 04:24:03', NULL, 'e6ff91f3-1862-423c-9509-077948c578ee', '2026-06-08 03:39:05', '2026-06-11 04:24:03'),
(4, 2, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 03:30:31', NULL, 'ee8e0458-794e-4844-b652-ec5f23f92cc1', '2026-06-12 03:13:22', '2026-06-12 03:30:31'),
(5, 2, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '8e5a936f-e05e-4cb3-b8fb-5d7a3f93b52b', '2026-06-12 03:13:22', '2026-06-12 03:13:22'),
(6, 2, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '0e5ce577-8c09-46b9-9e43-775046d55124', '2026-06-12 03:13:22', '2026-06-12 03:13:22'),
(7, 3, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 03:42:20', NULL, '1da02ee0-3a67-4389-8518-07050736b6aa', '2026-06-12 03:31:03', '2026-06-12 03:42:20'),
(8, 3, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, 'f2fcbfa9-dde7-45e6-9dc0-2adf9aa8a888', '2026-06-12 03:31:03', '2026-06-12 03:31:03'),
(9, 3, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '4050d7aa-93fc-4c89-86c4-8f529e5b1081', '2026-06-12 03:31:03', '2026-06-12 03:31:03'),
(10, 4, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 03:47:42', NULL, '4b081dc3-1de9-453c-96a6-1a19b7906965', '2026-06-12 03:47:15', '2026-06-12 03:47:42'),
(11, 4, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '32e8b734-dd1f-4b40-ac5c-1bd94f0b9794', '2026-06-12 03:47:15', '2026-06-12 03:47:15'),
(12, 4, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '914af43b-7356-43e8-b9c0-2de65b19ab02', '2026-06-12 03:47:15', '2026-06-12 03:47:15'),
(13, 5, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 04:10:10', NULL, 'de0ca91e-59bc-4742-ba10-4a13de3a20c9', '2026-06-12 03:55:47', '2026-06-12 04:10:10'),
(14, 5, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '728e5795-a43c-4df1-a170-0eeb55294bf7', '2026-06-12 03:55:47', '2026-06-12 03:55:47'),
(15, 5, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '3e773086-06b4-49aa-aa79-a75d57c39e6c', '2026-06-12 03:55:47', '2026-06-12 03:55:47'),
(16, 6, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 04:09:36', NULL, 'cc06560d-f4f0-4cc1-892f-c562873daa39', '2026-06-12 04:06:27', '2026-06-12 04:09:36'),
(17, 6, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '389c4119-9c17-4120-be78-2ed6aa78c588', '2026-06-12 04:06:27', '2026-06-12 04:06:27'),
(18, 6, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '113385a7-420f-47d6-9e42-8c20b26ac825', '2026-06-12 04:06:27', '2026-06-12 04:06:27'),
(19, 7, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-15 02:16:54', NULL, '725dc6f6-75c4-4a86-987e-cc5d31857ac9', '2026-06-12 07:42:02', '2026-06-15 02:16:54'),
(20, 7, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '928b6eb9-c20b-486f-8c74-1714fa0f775b', '2026-06-12 07:42:02', '2026-06-12 07:42:02'),
(21, 7, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '3ff1eb1c-25cd-4b01-9b5d-1a62fb146bd0', '2026-06-12 07:42:02', '2026-06-12 07:42:02'),
(22, 8, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-15 02:20:20', NULL, '3e6e5106-610c-4e1a-b4a9-976a88875af7', '2026-06-15 02:06:45', '2026-06-15 02:20:20'),
(23, 8, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, 'e84c03c1-62ea-4959-97d9-96f89627b307', '2026-06-15 02:06:45', '2026-06-15 02:06:45'),
(24, 8, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '0a4ca9ed-f661-4aaa-b02b-fab1dc96791e', '2026-06-15 02:06:45', '2026-06-15 02:06:45'),
(40, 14, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-15 02:41:51', NULL, '452daf17-aab3-405a-804e-93ff6a0097e9', '2026-06-15 02:38:16', '2026-06-15 02:41:51'),
(41, 14, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '8a3664e0-ccfe-4b92-89b1-ed9133177362', '2026-06-15 02:38:16', '2026-06-15 02:38:16'),
(42, 14, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '132aa36c-94c1-4c0a-8658-a684e3ab1c9b', '2026-06-15 02:38:16', '2026-06-15 02:38:16'),
(43, 15, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-15 02:43:01', NULL, 'fcec0fba-6e6b-43c7-ba33-6361f663deaf', '2026-06-15 02:42:03', '2026-06-15 02:43:01'),
(44, 15, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '30ae3820-f1d1-4bf6-a6a3-a50dd42c8fb5', '2026-06-15 02:42:03', '2026-06-15 02:42:03'),
(45, 15, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '4f5ddd63-3b48-4f11-ad31-9f90d139c912', '2026-06-15 02:42:03', '2026-06-15 02:42:03'),
(46, 16, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-15 02:48:00', NULL, '9825bb35-7994-4f53-a4fd-df0515374a34', '2026-06-15 02:43:09', '2026-06-15 02:48:00'),
(47, 16, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, 'b858b416-591b-4f86-8e39-192f8d2b20eb', '2026-06-15 02:43:09', '2026-06-15 02:43:09'),
(48, 16, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, '73068603-0b4e-468c-b019-c9fe55903adc', '2026-06-15 02:43:09', '2026-06-15 02:43:09'),
(49, 17, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'waiting', NULL, NULL, 'ac59c53d-13d4-4fac-ba48-2dbf56c63399', '2026-06-15 02:48:15', '2026-06-15 02:48:15'),
(50, 17, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '9c2aaa17-24a2-4fb7-a2ad-7288289fcb64', '2026-06-15 02:48:15', '2026-06-15 02:48:15'),
(51, 17, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, NULL, 'c94f6ce1-d2e6-45b3-adb5-98f1b92de31f', '2026-06-15 02:48:15', '2026-06-15 02:48:15'),
(52, 18, 1093, 'IT STAFF', 'Kaizen Development', 1, 'approved', '2026-06-19 15:18:10', NULL, '24c45d0d-4025-44b8-8bf4-6823835d6ca2', '2026-06-19 14:56:25', '2026-06-19 15:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_cancellation_items`
--

CREATE TABLE `business_cancellation_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cancellation_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_cancellation_items`
--

INSERT INTO `business_cancellation_items` (`id`, `cancellation_id`, `qty`, `category`, `unit_total`, `unit_amount`, `currency`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'daily', 5000000.00, 1000000.00, 'IDR', NULL, '2026-06-08 03:39:05', '2026-06-08 03:39:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_cancellation_logs`
--

CREATE TABLE `business_cancellation_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `business_cancellation_id` bigint UNSIGNED NOT NULL,
  `approval_path_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_cancellation_logs`
--

INSERT INTO `business_cancellation_logs` (`id`, `business_cancellation_id`, `approval_path_id`, `status`, `reason`, `action_at`, `created_at`, `updated_at`) VALUES
(5, 1, 1, 'approved', NULL, '2026-06-11 11:07:42', '2026-06-11 04:07:42', '2026-06-11 04:07:42'),
(6, 1, 2, 'approved', NULL, '2026-06-11 11:18:53', '2026-06-11 04:18:53', '2026-06-11 04:18:53'),
(7, 1, 3, 'approved', NULL, '2026-06-11 11:24:03', '2026-06-11 04:24:03', '2026-06-11 04:24:03'),
(8, 2, 4, 'rejected', 'test', '2026-06-12 10:30:31', '2026-06-12 03:30:31', '2026-06-12 03:30:31'),
(9, 3, 7, 'rejected', 'test', '2026-06-12 10:42:20', '2026-06-12 03:42:20', '2026-06-12 03:42:20'),
(10, 4, 10, 'rejected', 'test', '2026-06-12 10:47:42', '2026-06-12 03:47:42', '2026-06-12 03:47:42'),
(11, 6, 16, 'rejected', 'test', '2026-06-12 11:09:36', '2026-06-12 04:09:36', '2026-06-12 04:09:36'),
(12, 5, 13, 'rejected', 'test', '2026-06-12 11:10:10', '2026-06-12 04:10:10', '2026-06-12 04:10:10'),
(13, 7, 19, 'rejected', 'test', '2026-06-15 09:16:54', '2026-06-15 02:16:54', '2026-06-15 02:16:54'),
(14, 8, 22, 'rejected', 'test', '2026-06-15 09:20:20', '2026-06-15 02:20:20', '2026-06-15 02:20:20'),
(15, 14, 40, 'rejected', 'test', '2026-06-15 09:41:51', '2026-06-15 02:41:51', '2026-06-15 02:41:51'),
(16, 15, 43, 'rejected', 'test', '2026-06-15 09:43:01', '2026-06-15 02:43:01', '2026-06-15 02:43:01'),
(17, 16, 46, 'rejected', 'test', '2026-06-15 09:48:00', '2026-06-15 02:48:00', '2026-06-15 02:48:00'),
(18, 18, 52, 'approved', 'test', '2026-06-19 15:18:10', '2026-06-19 15:18:10', '2026-06-19 15:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_reports`
--

CREATE TABLE `business_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propose_date` date NOT NULL,
  `trip_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL DEFAULT '0',
  `arrival_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `balance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `total_cost` decimal(15,2) DEFAULT NULL,
  `revised_level` int DEFAULT NULL,
  `revised_count` int NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('waiting','revised','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_reports`
--

INSERT INTO `business_reports` (`id`, `business_trip_id`, `employee_id`, `level`, `position`, `department`, `propose_date`, `trip_type`, `start_date`, `end_date`, `total_days`, `arrival_to`, `purpose`, `report_result`, `balance_amount`, `currency`, `total_cost`, `revised_level`, `revised_count`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', '2026-06-05', 'domestic', '2026-06-08', '2026-06-12', 5, 'jakarta', 'test', 'hrehe', 0.00, 'IDR', 10500000.00, NULL, 0, NULL, 'approved', '2026-06-05 09:04:13', '2026-06-11 04:29:14'),
(2, NULL, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', '2026-06-11', 'domestic', '2026-06-15', '2026-06-19', 5, 'jakarta', 'main main', NULL, 10000000.00, 'IDR', 2000000.00, NULL, 2, NULL, 'waiting', '2026-06-11 08:40:10', '2026-06-11 08:43:02'),
(3, 5, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', '2026-06-12', 'domestic', '2026-06-15', '2026-06-19', 5, 'jakarta', 'gagag', NULL, 10000000.00, 'IDR', 5000000.00, NULL, 0, NULL, 'rejected', '2026-06-12 03:05:48', '2026-06-12 03:06:14'),
(4, 5, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', '2026-06-12', 'domestic', '2026-06-15', '2026-06-19', 5, 'jakarta', 'gagag', NULL, 10000000.00, 'IDR', 5000000.00, NULL, 0, NULL, 'rejected', '2026-06-12 03:10:03', '2026-06-12 03:10:24'),
(8, 10, 1092, 'STAFF', 'IT STAFF', 'Kaizen Development', '2026-06-19', 'domestic', '2026-06-15', '2026-06-18', 4, 'jakarta', 'test', 'test', 0.00, 'IDR', 5000000000.00, NULL, 0, NULL, 'waiting', '2026-06-19 15:03:03', '2026-06-19 15:03:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_report_approvals`
--

CREATE TABLE `business_report_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `business_report_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `status` enum('pending','waiting','approved','rejected','revised') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` datetime DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_report_approvals`
--

INSERT INTO `business_report_approvals` (`id`, `business_report_id`, `approver_id`, `position`, `department`, `level`, `status`, `approved_at`, `reason`, `approval_token`, `created_at`, `updated_at`) VALUES
(1, 1, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-11 11:14:34', NULL, 'cd3dad6f-2ed5-4512-a852-42f83ff4ed5f', '2026-06-05 09:04:13', '2026-06-11 04:14:34'),
(2, 1, 1058, 'DIRECTOR', 'NA', 2, 'approved', '2026-06-11 11:19:35', NULL, 'd07a2115-e9e0-4a36-bb03-a644a099f775', '2026-06-05 09:04:13', '2026-06-11 04:19:35'),
(3, 1, 4134, 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'Engineering', 3, 'approved', '2026-06-11 11:29:14', NULL, '375151eb-b253-4f21-9fab-165e25ce2b46', '2026-06-05 09:04:13', '2026-06-11 04:29:14'),
(4, 2, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'waiting', NULL, NULL, '24e8281b-521c-4c76-a466-a3c29a4bed25', '2026-06-11 08:40:10', '2026-06-11 08:43:02'),
(5, 2, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, 'c0ef3daf-b449-4d1e-9942-a823034ec165', '2026-06-11 08:40:10', '2026-06-11 08:43:02'),
(6, 2, 4134, 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'Engineering', 3, 'pending', NULL, NULL, 'c045dee8-7e53-4148-bd7a-401bd35d9583', '2026-06-11 08:40:10', '2026-06-11 08:43:02'),
(7, 3, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 10:06:14', NULL, 'ea3463fc-1adc-463a-966e-5a9a31b4c9a8', '2026-06-12 03:05:48', '2026-06-12 03:06:14'),
(8, 3, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '0dd29536-e65e-41e4-ae9e-0d1ff25b56e0', '2026-06-12 03:05:48', '2026-06-12 03:05:48'),
(9, 3, 4134, 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'Engineering', 3, 'pending', NULL, NULL, 'bd3b512e-7b24-474b-bfff-f60c9af867b4', '2026-06-12 03:05:48', '2026-06-12 03:05:48'),
(10, 4, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'rejected', '2026-06-12 10:10:24', NULL, 'd939fd3a-c82f-4151-8a71-511b40b303a2', '2026-06-12 03:10:03', '2026-06-12 03:10:24'),
(11, 4, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, '7c3b3010-2e06-4d0b-be9c-492899fa1eb1', '2026-06-12 03:10:03', '2026-06-12 03:10:03'),
(12, 4, 4134, 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'Engineering', 3, 'pending', NULL, NULL, '69822b6c-c0f9-4c0c-8f10-cf992200f2f2', '2026-06-12 03:10:03', '2026-06-12 03:10:03'),
(13, 8, 1093, 'IT STAFF', 'Kaizen Development', 1, 'approved', '2026-06-19 15:17:11', NULL, 'f408b2b3-0cae-4897-a22e-3118fbca791b', '2026-06-19 15:03:03', '2026-06-19 15:17:11'),
(14, 8, 1092, 'IT STAFF', 'Kaizen Development', 2, 'waiting', NULL, NULL, 'e77fd532-71f5-4108-ab97-213d49c7208e', '2026-06-19 15:03:03', '2026-06-19 15:17:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_report_attachments`
--

CREATE TABLE `business_report_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `business_report_item_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_report_items`
--

CREATE TABLE `business_report_items` (
  `id` bigint UNSIGNED NOT NULL,
  `business_report_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_amount` decimal(15,2) NOT NULL,
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_report_items`
--

INSERT INTO `business_report_items` (`id`, `business_report_id`, `qty`, `category`, `unit_total`, `unit_amount`, `currency`, `notes`, `expense_date`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'daily', 5000000.00, 1000000.00, 'IDR', NULL, '2026-06-08', '2026-06-05 09:04:13', '2026-06-05 09:04:13'),
(2, 1, 1, 'transport', 500000.00, 500000.00, 'IDR', 'bensin', '2026-06-08', '2026-06-05 09:04:13', '2026-06-05 09:04:13'),
(3, 1, 1, 'tol', 5000000.00, 5000000.00, 'IDR', 'tol', '2026-06-08', '2026-06-05 09:04:13', '2026-06-05 09:04:13'),
(14, 2, 1, 'meal', 400000.00, 400000.00, 'IDR', NULL, '2026-06-15', '2026-06-11 08:43:02', '2026-06-11 08:43:02'),
(15, 2, 1, 'meal', 400000.00, 400000.00, 'IDR', NULL, '2026-06-16', '2026-06-11 08:43:02', '2026-06-11 08:43:02'),
(16, 2, 1, 'meal', 400000.00, 400000.00, 'IDR', NULL, '2026-06-17', '2026-06-11 08:43:02', '2026-06-11 08:43:02'),
(17, 2, 1, 'meal', 400000.00, 400000.00, 'IDR', NULL, '2026-06-18', '2026-06-11 08:43:02', '2026-06-11 08:43:02'),
(18, 2, 1, 'meal', 400000.00, 400000.00, 'IDR', NULL, '2026-06-19', '2026-06-11 08:43:02', '2026-06-11 08:43:02'),
(19, 3, 5, 'daily', 5000000.00, 1000000.00, 'IDR', NULL, '2026-06-15', '2026-06-12 03:05:48', '2026-06-12 03:05:48'),
(20, 4, 5, 'daily', 5000000.00, 1000000.00, 'IDR', NULL, '2026-06-15', '2026-06-12 03:10:03', '2026-06-12 03:10:03'),
(26, 8, 5, 'daily', 5000000000.00, 1000000000.00, 'IDR', NULL, '2026-06-15', '2026-06-19 15:03:03', '2026-06-19 15:03:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_report_logs`
--

CREATE TABLE `business_report_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `business_report_id` bigint UNSIGNED NOT NULL,
  `approval_path_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_report_logs`
--

INSERT INTO `business_report_logs` (`id`, `business_report_id`, `approval_path_id`, `status`, `reason`, `action_at`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'approved', NULL, '2026-06-11 10:58:31', '2026-06-11 03:58:31', '2026-06-11 03:58:31'),
(4, 1, 1, 'approved', NULL, '2026-06-11 11:14:34', '2026-06-11 04:14:34', '2026-06-11 04:14:34'),
(5, 1, 2, 'approved', NULL, '2026-06-11 11:19:35', '2026-06-11 04:19:35', '2026-06-11 04:19:35'),
(6, 1, 3, 'approved', NULL, '2026-06-11 11:29:14', '2026-06-11 04:29:14', '2026-06-11 04:29:14'),
(7, 2, 4, 'revised', 'kurangi uang makan', '2026-06-11 15:41:24', '2026-06-11 08:41:24', '2026-06-11 08:41:24'),
(8, 2, 4, 'revised', 'test', '2026-06-11 15:42:51', '2026-06-11 08:42:51', '2026-06-11 08:42:51'),
(9, 3, 7, 'rejected', 'test', '2026-06-12 10:06:14', '2026-06-12 03:06:14', '2026-06-12 03:06:14'),
(10, 4, 10, 'rejected', 'test', '2026-06-12 10:10:24', '2026-06-12 03:10:24', '2026-06-12 03:10:24'),
(11, 8, 13, 'approved', 'test', '2026-06-19 15:17:11', '2026-06-19 15:17:11', '2026-06-19 15:17:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trips`
--

CREATE TABLE `business_trips` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trip_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propose_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL DEFAULT '0',
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','revised','approved','ongoing','reported','completed','cancelled','rejected','cancel_waiting') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `expense_method` enum('reimbursement','advance','operating_cost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reimbursement',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `need_hotel` tinyint(1) NOT NULL DEFAULT '0',
  `revised_level` int DEFAULT NULL,
  `revised_count` int NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hrd_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `hrd_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrd_knowledge_date` date DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trips`
--

INSERT INTO `business_trips` (`id`, `employee_id`, `level`, `position`, `department`, `no_document`, `trip_type`, `propose_date`, `start_date`, `end_date`, `total_days`, `departure_time`, `arrival_time`, `departure_from`, `arrival_to`, `purpose`, `status`, `expense_method`, `total_cost`, `advance_amount`, `advance_currency`, `need_hotel`, `revised_level`, `revised_count`, `notes`, `hrd_knowledge`, `hrd_name`, `hrd_knowledge_date`, `approved_at`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', 'BTD/202606/0001', 'domestic', '2026-06-05', '2026-06-08', '2026-06-12', 5, '08:00:00', '12:00:00', 'house', 'jakarta', 'test', 'completed', 'reimbursement', 10500000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-06-05 15:22:12', 'Alisha Kertzmann', '2026-06-05 08:21:00', '2026-06-11 04:29:14'),
(2, 4130, 'KAE', 'KAIZEN DEVELOPMENT GROUP LEADER', 'Engineering', 'BTD/202606/0002', 'domestic', '2026-06-05', '2026-06-08', '2026-06-12', 5, '08:00:00', '12:00:00', 'house', 'hahahaa', 'test', 'draft', 'reimbursement', 5000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(3, 4130, 'KAE', 'KAIZEN DEVELOPMENT GROUP LEADER', 'Engineering', 'BTD/202606/0003', 'domestic', '2026-06-05', '2026-06-15', '2026-06-19', 5, '08:00:00', '12:00:00', 'house', 'hahahaa', 'test', 'ongoing', 'reimbursement', 5000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-06-12 10:55:25', 'Alisha Kertzmann', '2026-06-05 08:44:54', '2026-06-12 04:10:10'),
(4, 4130, 'KAE', 'KAIZEN DEVELOPMENT GROUP LEADER', 'Engineering', 'BTD/202606/0004', 'domestic', '2026-06-05', '2026-06-22', '2026-06-26', 5, '08:00:00', '12:00:00', 'house', 'hahahaa', 'test', 'draft', 'reimbursement', 5000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, '2026-06-05 08:57:39', '2026-06-15 02:16:54'),
(5, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', 'BTD/202606/0005', 'domestic', '2026-06-05', '2026-06-15', '2026-06-19', 5, '12:00:00', '12:00:00', 'house', 'jakarta', 'gagag', 'cancelled', 'advance', 5000000.00, 10000000.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-06-12 10:04:58', 'Alisha Kertzmann', '2026-06-05 08:59:46', '2026-06-12 04:09:36'),
(6, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', 'BTD/202606/0006', 'domestic', '2026-06-11', '2026-06-29', '2026-07-03', 5, '12:00:00', '12:00:00', 'house', 'jakarta', 'sdad', 'cancel_waiting', 'reimbursement', 5000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, '2026-06-11 09:15:37', '2026-06-15 02:48:15'),
(7, 4083, 'KAE', 'QUALITY CONTROL STAFF', 'Kaizen Development', 'BTD/202606/0007', 'domestic', '2026-06-17', '2026-07-06', '2026-07-08', 3, '08:00:00', '10:00:00', 'house', 'surabaya', 'main', 'draft', 'advance', 8000000.00, 10000000.00, 'IDR', 1, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(8, 1093, 'STAFF', 'IT STAFF', 'Kaizen Development', 'BTD/202606/0008', 'domestic', '2026-06-19', '2026-06-19', '2026-06-19', 1, NULL, NULL, 'house', 'jakarta barat', 'pertemuan klien', 'rejected', 'reimbursement', 1015000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 'FERDISAPUTRO', '2026-06-19 13:13:47', '2026-06-19 13:28:25'),
(9, 1093, 'STAFF', 'IT STAFF', 'Kaizen Development', 'BTD/202606/0009', 'domestic', '2026-06-19', '2026-06-19', '2026-06-22', 4, NULL, NULL, 'PT.HPI', 'Surabaya', 'perjalanan dinas bisnis', 'approved', 'reimbursement', 4000000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 'FERDISAPUTRO', '2026-06-19 13:34:14', '2026-06-19 13:34:14'),
(10, 1092, 'STAFF', 'IT STAFF', 'Kaizen Development', 'BTD/202606/0010', 'domestic', '2026-06-19', '2026-06-15', '2026-06-18', 5, '12:00:00', '12:00:00', 'house', 'jakarta', 'test', 'approved', 'reimbursement', 5000000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-06-19 15:20:08', 'FERDISAPUTRO', '2026-06-19 14:53:07', '2026-06-19 15:20:08'),
(11, 1092, 'STAFF', 'IT STAFF', 'Kaizen Development', 'BTD/202606/0011', 'domestic', '2026-06-19', '2026-07-27', '2026-07-31', 5, '09:00:00', '12:00:00', 'house', 'Aceh Aceh', 'test', 'cancelled', 'reimbursement', 5000000000.00, 0.00, 'IDR', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, '2026-06-19 14:53:47', '2026-06-19 15:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_allowances`
--

CREATE TABLE `business_trip_allowances` (
  `id` bigint UNSIGNED NOT NULL,
  `level_id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimum_hours` int NOT NULL DEFAULT '0',
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trip_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_allowances`
--

INSERT INTO `business_trip_allowances` (`id`, `level_id`, `category`, `minimum_hours`, `amount`, `currency`, `trip_type`, `created_at`, `updated_at`) VALUES
(1, 18, 'daily', 0, 1000000.00, 'IDR', 'domestic', '2026-05-26 05:02:22', '2026-05-26 05:02:22'),
(2, 18, 'meal', 4, 500000.00, 'IDR', 'domestic', '2026-05-26 05:02:36', '2026-05-26 05:02:36'),
(3, 18, 'meal', 8, 750000.00, 'IDR', 'domestic', '2026-05-26 05:02:48', '2026-05-26 05:02:48'),
(4, 18, 'meal', 0, 2000000.00, 'IDR', 'overseas', '2026-05-26 05:03:00', '2026-05-26 05:03:00'),
(5, 18, 'laundry', 0, 200000.00, 'IDR', 'overseas', '2026-05-26 05:03:08', '2026-05-26 05:03:08'),
(6, 18, 'daily', 0, 5000000.00, 'IDR', 'overseas', '2026-05-26 05:03:21', '2026-05-26 05:03:21'),
(8, 8, 'daily', 0, 380000.00, 'IDR', 'overseas', '2026-06-19 10:54:27', '2026-06-19 10:57:17'),
(9, 8, 'daily', 0, 1000000000.00, 'IDR', 'domestic', '2026-06-19 13:11:33', '2026-06-19 13:11:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_approvals`
--

CREATE TABLE `business_trip_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `status` enum('pending','waiting','approved','rejected','revised') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` datetime DEFAULT NULL,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_approvals`
--

INSERT INTO `business_trip_approvals` (`id`, `business_trip_id`, `approver_id`, `position`, `department`, `level`, `status`, `approved_at`, `approval_token`, `created_at`, `updated_at`) VALUES
(1, 1, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-05 15:21:29', '2123fc77-5970-4593-886a-228746591412', '2026-06-05 08:21:00', '2026-06-05 08:21:29'),
(2, 1, 1058, 'DIRECTOR', 'NA', 2, 'approved', '2026-06-05 15:21:48', 'c413317f-fe54-4188-94e3-193dab7b4627', '2026-06-05 08:21:00', '2026-06-05 08:21:48'),
(3, 1, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'approved', '2026-06-05 15:22:12', 'bd3f37b5-5834-40b8-bcdc-3e6ec2dd62bf', '2026-06-05 08:21:00', '2026-06-05 08:22:12'),
(4, 2, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'waiting', NULL, '91122eed-ec4a-46e8-abb9-1105c4639133', '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(5, 2, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, 'f3a2b713-6e08-4185-87a2-05294fa2e2ac', '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(6, 2, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, 'f557e839-0f43-45b5-be78-796a5bc6e9f0', '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(7, 3, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-09 08:45:40', '366e373f-7783-4644-bd1c-f93044ffb2bb', '2026-06-05 08:44:54', '2026-06-09 01:45:40'),
(8, 3, 1058, 'DIRECTOR', 'NA', 2, 'approved', '2026-06-12 10:55:10', 'b9d78222-be02-4977-b0c3-ef416190bbc7', '2026-06-05 08:44:54', '2026-06-12 03:55:10'),
(9, 3, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'approved', '2026-06-12 10:55:25', '0f1eb9f3-7d3b-48dd-9f2a-9441cde9573f', '2026-06-05 08:44:54', '2026-06-12 03:55:25'),
(10, 4, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-11 11:18:24', 'e5f7d006-3eb5-4adf-be35-8fbe592c403c', '2026-06-05 08:57:39', '2026-06-11 04:18:24'),
(11, 4, 1058, 'DIRECTOR', 'NA', 2, 'waiting', NULL, 'c289c54d-a918-422f-836a-5082f1d0bddc', '2026-06-05 08:57:39', '2026-06-15 02:16:54'),
(12, 4, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, '0884e84a-9635-45ad-a8fe-da7f66ec340b', '2026-06-05 08:57:39', '2026-06-15 02:16:54'),
(13, 5, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'approved', '2026-06-11 11:11:47', '3786562b-cd2d-4dcb-aa20-9c48dee7ba32', '2026-06-05 08:59:46', '2026-06-11 04:11:47'),
(14, 5, 1058, 'DIRECTOR', 'NA', 2, 'approved', '2026-06-12 10:03:50', '6d4e0037-7768-4b29-a4a9-36b1d164e492', '2026-06-05 08:59:46', '2026-06-12 03:03:50'),
(15, 5, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'approved', '2026-06-12 10:04:58', 'e1825a25-256c-4dbb-8b79-3876e36a2c4e', '2026-06-05 08:59:46', '2026-06-12 03:04:58'),
(16, 6, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'pending', NULL, '1f1ba3bf-d31a-47cb-b610-faf5fe7cfec0', '2026-06-11 09:15:37', '2026-06-15 02:48:15'),
(17, 6, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, '8d3fd22a-e6cd-418e-9690-1ba41ff90566', '2026-06-11 09:15:37', '2026-06-15 02:48:00'),
(18, 6, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, '2bed9953-4988-4978-8998-8efa3a971d55', '2026-06-11 09:15:37', '2026-06-15 02:48:00'),
(19, 7, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'waiting', NULL, 'e3486cc5-daa9-4dc6-9c0a-6711a46b9825', '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(20, 7, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, 'a1a6519a-9a03-48f5-8204-731471a25f65', '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(21, 7, 4234, 'HRD & GA GENERAL MANAGER', 'Sales & Trade Marketing', 3, 'pending', NULL, '8775e213-3632-4e5e-a8e0-95e4ac2b7396', '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(22, 8, 1093, 'IT STAFF', 'Kaizen Development', 1, 'rejected', '2026-06-19 13:28:25', 'f8831371-865d-45d1-a6f5-5285326d134d', '2026-06-19 13:13:47', '2026-06-19 13:28:25'),
(23, 9, 1093, 'IT STAFF', 'Kaizen Development', 1, 'waiting', NULL, '6c21574b-07be-47fe-909e-db4cfb000e93', '2026-06-19 13:34:14', '2026-06-19 13:34:14'),
(24, 10, 1093, 'IT STAFF', 'Kaizen Development', 1, 'approved', '2026-06-19 15:20:08', 'c97f3780-20db-4a60-ab5c-838445b6c952', '2026-06-19 14:53:07', '2026-06-19 15:20:08'),
(25, 11, 1093, 'IT STAFF', 'Kaizen Development', 1, 'pending', NULL, 'ee07cab5-8610-4662-ae42-42d665ccc832', '2026-06-19 14:53:47', '2026-06-19 14:56:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_costs`
--

CREATE TABLE `business_trip_costs` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_amount` decimal(15,2) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_costs`
--

INSERT INTO `business_trip_costs` (`id`, `business_trip_id`, `qty`, `category`, `unit_amount`, `total_amount`, `currency`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-05 08:21:00', '2026-06-05 08:21:00'),
(2, 1, 1, 'transport', 500000.00, 500000.00, 'IDR', 'bensin', '2026-06-05 08:21:00', '2026-06-05 08:21:00'),
(3, 1, 1, 'tol', 5000000.00, 5000000.00, 'IDR', 'tol', '2026-06-05 08:21:00', '2026-06-05 08:21:00'),
(4, 2, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(5, 3, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-05 08:44:54', '2026-06-05 08:44:54'),
(6, 4, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-05 08:57:39', '2026-06-05 08:57:39'),
(7, 5, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-05 08:59:46', '2026-06-05 08:59:46'),
(8, 6, 5, 'daily', 1000000.00, 5000000.00, 'IDR', NULL, '2026-06-11 09:15:37', '2026-06-11 09:15:37'),
(9, 7, 3, 'daily', 1000000.00, 3000000.00, 'IDR', NULL, '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(10, 7, 1, 'transport', 5000000.00, 5000000.00, 'IDR', 'uang bensin', '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(11, 8, 1, 'daily', 1000000000.00, 1000000000.00, 'IDR', NULL, '2026-06-19 13:13:47', '2026-06-19 13:13:47'),
(12, 8, 3, 'Hotel', 5000000.00, 15000000.00, 'IDR', NULL, '2026-06-19 13:13:47', '2026-06-19 13:13:47'),
(13, 9, 4, 'daily', 1000000000.00, 4000000000.00, 'IDR', NULL, '2026-06-19 13:34:14', '2026-06-19 13:34:14'),
(14, 10, 5, 'daily', 1000000000.00, 5000000000.00, 'IDR', NULL, '2026-06-19 14:53:07', '2026-06-19 14:53:07'),
(15, 11, 5, 'daily', 1000000000.00, 5000000000.00, 'IDR', NULL, '2026-06-19 14:53:47', '2026-06-19 14:53:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_hotels`
--

CREATE TABLE `business_trip_hotels` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `reservation_by_ga` tinyint(1) NOT NULL DEFAULT '0',
  `hotel_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_days` int NOT NULL DEFAULT '0',
  `total_nights` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_hotels`
--

INSERT INTO `business_trip_hotels` (`id`, `business_trip_id`, `reservation_by_ga`, `hotel_name`, `check_in`, `check_out`, `total_days`, `total_nights`, `created_at`, `updated_at`) VALUES
(1, 7, 1, NULL, NULL, NULL, 0, 0, '2026-06-17 07:55:29', '2026-06-17 07:55:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_logs`
--

CREATE TABLE `business_trip_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `approval_path_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_logs`
--

INSERT INTO `business_trip_logs` (`id`, `business_trip_id`, `approval_path_id`, `status`, `reason`, `action_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'approved', NULL, '2026-06-05 15:21:29', '2026-06-05 08:21:29', '2026-06-05 08:21:29'),
(2, 1, 2, 'approved', NULL, '2026-06-05 15:21:48', '2026-06-05 08:21:48', '2026-06-05 08:21:48'),
(3, 1, 3, 'approved', NULL, '2026-06-05 15:22:12', '2026-06-05 08:22:12', '2026-06-05 08:22:12'),
(4, 3, 7, 'approved', NULL, '2026-06-09 08:45:40', '2026-06-09 01:45:40', '2026-06-09 01:45:40'),
(5, 5, 13, 'approved', NULL, '2026-06-11 11:11:47', '2026-06-11 04:11:47', '2026-06-11 04:11:47'),
(6, 4, 10, 'approved', NULL, '2026-06-11 11:18:24', '2026-06-11 04:18:24', '2026-06-11 04:18:24'),
(7, 5, 14, 'approved', NULL, '2026-06-12 10:03:50', '2026-06-12 03:03:50', '2026-06-12 03:03:50'),
(10, 5, 15, 'approved', NULL, '2026-06-12 10:04:58', '2026-06-12 03:04:58', '2026-06-12 03:04:58'),
(11, 3, 8, 'approved', 'test', '2026-06-12 10:55:10', '2026-06-12 03:55:10', '2026-06-12 03:55:10'),
(12, 3, 9, 'approved', 'test', '2026-06-12 10:55:25', '2026-06-12 03:55:25', '2026-06-12 03:55:25'),
(15, 8, 22, 'rejected', 'alasan tidak jelas', '2026-06-19 13:28:25', '2026-06-19 13:28:25', '2026-06-19 13:28:25'),
(18, 10, NULL, NULL, 'test', NULL, '2026-06-19 15:15:59', '2026-06-19 15:15:59'),
(19, 10, NULL, NULL, NULL, NULL, '2026-06-19 15:20:08', '2026-06-19 15:20:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_trip_transportations`
--

CREATE TABLE `business_trip_transportations` (
  `id` bigint UNSIGNED NOT NULL,
  `business_trip_id` bigint UNSIGNED NOT NULL,
  `transport_type` enum('private','company_car','public_transport') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_transport_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `business_trip_transportations`
--

INSERT INTO `business_trip_transportations` (`id`, `business_trip_id`, `transport_type`, `public_transport_type`, `vehicle_number`, `driver_name`, `departure_date`, `departure_time`, `arrival_date`, `arrival_time`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 08:21:00', '2026-06-05 08:21:00'),
(2, 2, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 08:33:47', '2026-06-05 08:33:47'),
(3, 3, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 08:44:54', '2026-06-05 08:44:54'),
(4, 4, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 08:57:39', '2026-06-05 08:57:39'),
(5, 5, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 08:59:46', '2026-06-05 08:59:46'),
(6, 6, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-11 09:15:37', '2026-06-11 09:15:37'),
(7, 7, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 07:55:29', '2026-06-17 07:55:29'),
(8, 8, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-19 13:13:47', '2026-06-19 13:13:47'),
(9, 9, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-19 13:34:14', '2026-06-19 13:34:14'),
(10, 10, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-19 14:53:07', '2026-06-19 14:53:07'),
(11, 11, 'private', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-19 14:53:47', '2026-06-19 14:53:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `calendar`
--

CREATE TABLE `calendar` (
  `id` bigint UNSIGNED NOT NULL,
  `id_temp_calendar` int NOT NULL,
  `id_leave` int NOT NULL,
  `event` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `calendar`
--

INSERT INTO `calendar` (`id`, `id_temp_calendar`, `id_leave`, `event`, `type`, `tanggal_awal`, `tanggal_akhir`, `created_at`, `updated_at`) VALUES
(6, 1, 2, 'New Year 2022', 1, '2023-01-01', NULL, '2023-12-15 06:05:02', '2023-12-15 06:05:02'),
(7, 1, 2, 'Chinese New Year 2574', 1, '2023-01-22', NULL, '2023-12-15 06:07:32', '2023-12-15 06:07:32'),
(8, 1, 2, 'Ascension of Prophet Muhammad SAW', 1, '2023-02-18', NULL, '2023-12-15 06:08:58', '2023-12-15 06:08:58'),
(9, 1, 2, 'Day of Silence New Year Saka 1945', 1, '2023-03-22', NULL, '2023-12-15 06:10:34', '2023-12-15 06:10:34'),
(10, 1, 2, 'Jesus Christ Passing Day', 1, '2023-04-07', NULL, '2023-12-15 06:31:15', '2023-12-15 06:31:15'),
(11, 1, 1, 'National Collective Leave', 1, '2023-04-21', NULL, '2023-12-15 06:32:22', '2023-12-15 06:32:22'),
(12, 1, 1, 'National Collective Leave', 1, '2023-04-24', NULL, '2023-12-15 06:32:46', '2023-12-15 06:32:46'),
(13, 1, 2, 'Eid Al - Fitr 1444 H', 1, '2023-04-22', '2023-04-23', '2023-12-18 08:13:19', '2023-12-18 08:32:43'),
(14, 1, 2, 'Labor Day', 1, '2023-05-01', NULL, '2023-12-18 08:33:57', '2023-12-18 08:33:57'),
(15, 1, 2, 'Ascension Day of Jesus Christ', 1, '2023-05-18', NULL, '2023-12-18 08:36:13', '2023-12-18 08:36:13'),
(16, 1, 2, 'Pancasila Day', 1, '2023-06-01', NULL, '2023-12-18 08:36:58', '2023-12-18 08:36:58'),
(17, 1, 2, 'Waisak Day 2567', 1, '2023-06-04', NULL, '2023-12-18 08:37:43', '2023-12-18 08:37:43'),
(18, 1, 2, 'Eid al-Adha 1444 Hijriah', 1, '2023-06-29', NULL, '2023-12-18 08:39:08', '2023-12-18 08:39:08'),
(19, 1, 2, 'Islamic New Year 1445 Hijriah', 1, '2023-07-19', NULL, '2023-12-18 08:39:59', '2023-12-18 08:39:59'),
(20, 1, 2, 'Indonesian Independence Day', 1, '2023-08-17', NULL, '2023-12-18 08:40:51', '2023-12-18 08:40:51'),
(21, 1, 2, 'The Prophet Muhammad\'s Birthday', 1, '2023-09-28', NULL, '2023-12-18 08:46:30', '2023-12-18 08:46:30'),
(22, 1, 2, 'Christmas Day', 1, '2023-12-25', NULL, '2023-12-18 08:47:56', '2023-12-18 08:47:56'),
(24, 1, 1, 'National Collective Leave', 1, '2023-12-26', NULL, '2023-12-18 08:50:48', '2023-12-18 08:50:48'),
(25, 1, 2, 'New Year 2022', 2, '2023-01-01', NULL, '2023-12-18 08:51:47', '2023-12-18 08:51:47'),
(26, 1, 2, 'Chinese New Year 2574', 2, '2023-01-22', NULL, '2023-12-18 08:52:18', '2023-12-18 08:52:18'),
(27, 1, 2, 'Ascension of Prophet Muhammad SAW', 2, '2023-02-18', NULL, '2023-12-18 08:53:25', '2023-12-18 08:53:25'),
(28, 1, 2, 'Day of Silence New Year Saka 1945', 2, '2023-03-22', NULL, '2023-12-18 08:54:05', '2023-12-18 08:54:05'),
(29, 1, 2, 'Jesus Christ Passing Day', 2, '2023-04-07', NULL, '2023-12-18 08:54:48', '2023-12-18 08:54:48'),
(30, 1, 1, 'National Collective Leave', 2, '2023-04-21', NULL, '2023-12-18 08:55:47', '2023-12-18 08:55:47'),
(31, 1, 2, 'Eid Al - Fitr 1444 H', 2, '2023-04-22', '2023-04-23', '2023-12-18 08:56:34', '2023-12-18 08:59:27'),
(32, 1, 1, 'National Collective Leave', 2, '2023-04-24', '2023-04-26', '2023-12-18 08:57:12', '2023-12-18 08:57:12'),
(33, 1, 2, 'Labor Day', 2, '2023-05-01', NULL, '2023-12-18 08:57:39', '2023-12-18 08:57:39'),
(34, 1, 2, 'Ascension Day of Jesus Christ', 2, '2023-05-18', NULL, '2023-12-18 08:58:55', '2023-12-18 08:58:55'),
(35, 1, 2, 'Pancasila Day', 2, '2023-06-01', NULL, '2023-12-18 09:01:11', '2023-12-18 09:01:11'),
(36, 1, 2, 'Waisak Day 2567', 2, '2023-06-04', NULL, '2023-12-18 09:01:49', '2023-12-18 09:01:49'),
(37, 1, 2, 'Eid al-Adha 1444 Hijriah', 2, '2023-06-29', NULL, '2023-12-18 09:02:44', '2023-12-18 09:02:44'),
(38, 1, 3, 'Company Collective Leave', 2, '2023-06-30', NULL, '2023-12-18 09:04:04', '2023-12-18 09:04:04'),
(39, 1, 2, 'Islamic New Year 1445 Hijriah', 2, '2023-07-19', NULL, '2023-12-18 09:05:00', '2023-12-18 09:05:00'),
(40, 1, 2, 'Indonesian Independence Day', 2, '2023-08-17', NULL, '2023-12-18 09:05:37', '2023-12-18 09:05:37'),
(41, 1, 3, 'Company Tour (plan)', 2, '2023-08-18', NULL, '2023-12-18 09:06:21', '2023-12-18 09:06:21'),
(42, 1, 2, 'The Prophet Muhammad\'s Birthday', 2, '2023-09-28', NULL, '2023-12-18 09:06:43', '2023-12-18 09:06:43'),
(43, 1, 2, 'Christmas Day', 2, '2023-12-25', NULL, '2023-12-18 09:07:14', '2023-12-18 09:07:14'),
(44, 2, 1, 'Cuti Bersama', 2, '2024-02-09', NULL, '2023-12-29 08:34:01', '2023-12-29 08:34:01'),
(53, 2, 2, 'Eidul Fitri 1445', 2, '2024-04-10', '2024-04-11', '2024-03-25 06:25:25', '2024-03-25 06:25:25'),
(54, 2, 3, 'Cuti Bersama Idul Fitri', 2, '2024-04-08', '2024-04-09', '2024-03-25 06:26:33', '2024-03-25 06:29:12'),
(55, 2, 4, 'Cuti Bersama', 2, '2024-04-12', NULL, '2024-03-25 06:26:59', '2024-03-25 06:26:59'),
(57, 2, 2, 'Nyepi', 2, '2024-03-11', NULL, '2024-03-25 06:30:30', '2024-03-25 06:30:39'),
(58, 2, 1, 'Cuti Bersama', 1, '2024-04-09', NULL, '2024-04-19 07:30:59', '2024-04-19 07:31:40'),
(59, 2, 2, 'Idul Fitri', 1, '2024-04-10', '2024-04-11', '2024-04-19 07:31:27', '2024-04-19 07:31:27'),
(60, 2, 1, 'Cuti Bersama', 1, '2024-04-12', NULL, '2024-04-19 07:32:03', '2024-04-19 07:32:03'),
(61, 2, 2, 'Labor Day', 1, '2024-05-01', NULL, '2024-04-19 07:33:01', '2024-04-19 07:33:01'),
(62, 2, 2, 'Labor Day', 2, '2024-05-01', NULL, '2024-04-19 07:33:01', '2024-04-19 07:33:01'),
(63, 2, 2, 'Kelahiran Isa Almasih', 1, '2024-05-09', NULL, '2024-04-19 07:33:54', '2024-04-23 01:23:06'),
(64, 2, 2, 'Kenaikan Isa Almasih', 2, '2024-05-09', NULL, '2024-04-19 07:33:54', '2024-04-19 07:33:54'),
(65, 2, 2, 'Waisak', 1, '2024-05-23', NULL, '2024-04-19 07:35:19', '2024-04-19 07:35:19'),
(66, 2, 2, 'Waisak', 2, '2024-05-23', NULL, '2024-04-19 07:35:19', '2024-04-19 07:35:19'),
(67, 2, 4, 'Salonpas Day', 1, '2024-05-18', NULL, '2024-04-19 07:38:34', '2024-04-19 07:38:34'),
(68, 2, 4, 'Salonpas Day', 2, '2024-05-18', NULL, '2024-04-19 07:38:34', '2024-04-19 07:38:34'),
(69, 2, 4, 'Halal Bihalal HQ/Factory', 2, '2024-04-18', NULL, '2024-04-23 01:18:32', '2024-04-23 01:18:32'),
(70, 2, 4, 'Halal Bihalal HQ/Factory', 1, '2024-04-18', NULL, '2024-04-23 01:20:22', '2024-04-23 01:20:22'),
(71, 2, 2, 'Wafat Isa Almasih', 2, '2024-03-29', NULL, '2024-04-23 01:23:43', '2024-04-23 01:24:55'),
(72, 2, 2, 'Pancasila Day', 1, '2024-06-01', NULL, '2024-05-29 04:48:05', '2024-05-29 04:48:05'),
(73, 2, 2, 'Pancasila Day', 2, '2024-06-01', NULL, '2024-05-29 04:48:05', '2024-05-29 04:48:05'),
(74, 2, 4, 'Ten - Ten Day', 1, '2024-10-10', NULL, '2024-05-29 04:48:55', '2024-05-29 04:48:55'),
(75, 2, 4, 'Ten - Ten Day', 2, '2024-10-10', NULL, '2024-05-29 04:48:55', '2024-05-29 04:48:55'),
(76, 2, 2, 'Eid Al-Adha 1445H', 1, '2024-06-17', NULL, '2024-05-29 04:50:25', '2024-05-29 04:50:25'),
(77, 2, 2, 'Eid Al-Adha 1445H', 2, '2024-06-17', NULL, '2024-05-29 04:50:25', '2024-05-29 04:50:25'),
(78, 2, 3, 'Factory Leave', 2, '2024-06-18', NULL, '2024-05-29 04:52:17', '2024-05-29 04:52:53'),
(79, 2, 2, 'Islamic New Year 1446H', 1, '2024-07-07', NULL, '2024-05-29 04:54:00', '2024-05-29 04:54:00'),
(80, 2, 2, 'Islamic New Year 1446H', 2, '2024-07-07', NULL, '2024-05-29 04:54:00', '2024-05-29 04:54:00'),
(81, 2, 4, 'Company Tour', 2, '2024-09-13', NULL, '2024-05-29 04:55:01', '2024-05-29 04:55:01'),
(82, 2, 2, 'Independent Days', 1, '2024-08-17', NULL, '2024-05-29 04:55:48', '2024-05-29 04:55:48'),
(83, 2, 2, 'Independent Days', 2, '2024-08-17', NULL, '2024-05-29 04:55:48', '2024-05-29 04:55:48'),
(84, 2, 2, 'The Propert Muhammad\'s Birthday', 1, '2024-09-16', NULL, '2024-05-29 05:05:18', '2024-05-29 05:05:18'),
(85, 2, 2, 'The Propert Muhammad\'s Birthday', 2, '2024-09-16', NULL, '2024-05-29 05:05:18', '2024-05-29 05:05:18'),
(86, 2, 2, 'Christmas Day', 1, '2024-12-25', NULL, '2024-05-29 05:06:16', '2024-05-29 05:06:16'),
(90, 2, 2, 'Mary Christmas', 2, '2024-12-25', NULL, '2024-05-30 07:47:43', '2024-05-30 07:48:56'),
(93, 2, 4, 'HQ/Factory MCU', 2, '2024-11-19', '2024-11-20', '2024-10-25 03:26:36', '2024-10-25 03:26:36'),
(94, 2, 4, 'All Area MCU', 1, '2024-11-01', '2024-12-01', '2024-10-28 07:42:37', '2024-10-28 07:42:37'),
(95, 2, 4, 'All Area MCU', 2, '2024-11-01', '2024-12-01', '2024-10-28 07:42:37', '2024-10-28 07:42:37'),
(96, 4, 2, 'New Year 2025', 1, '2025-01-01', NULL, '2024-11-28 06:31:42', '2024-11-28 06:31:42'),
(97, 4, 2, 'New Year 2025', 2, '2025-01-01', NULL, '2024-11-28 06:31:42', '2024-11-28 06:31:42'),
(98, 4, 4, 'COMPANY TOUR 2025', 2, '2025-09-12', NULL, '2024-11-28 06:41:55', '2024-11-28 06:41:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidate`
--

CREATE TABLE `candidate` (
  `id` bigint UNSIGNED NOT NULL,
  `posting_id` bigint UNSIGNED NOT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `no_ktp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ktp_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicile_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthplace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` smallint DEFAULT NULL,
  `weight` smallint DEFAULT NULL,
  `skill` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expected_salary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submit_date` datetime DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `referer_source` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `captcha_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidate_education`
--

CREATE TABLE `candidate_education` (
  `id` bigint UNSIGNED NOT NULL,
  `candidate_id` bigint UNSIGNED NOT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institution_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `major` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_graduated` year DEFAULT NULL,
  `score_gpa` decimal(5,2) DEFAULT NULL,
  `ijazah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidate_experience`
--

CREATE TABLE `candidate_experience` (
  `id` bigint UNSIGNED NOT NULL,
  `candidate_id` bigint UNSIGNED NOT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years` smallint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `claim_approvals`
--

CREATE TABLE `claim_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `claim_overtime_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int NOT NULL,
  `status` enum('waiting','approved','rejected','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `approved_at` timestamp NULL DEFAULT NULL,
  `reason_reject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `claim_approvals`
--

INSERT INTO `claim_approvals` (`id`, `claim_overtime_id`, `employee_id`, `position`, `department`, `level`, `status`, `approved_at`, `reason_reject`, `approval_token`, `created_at`, `updated_at`) VALUES
(1, 1, 1075, 'TECHNICIAN', 'HRD & GA', 1, 'waiting', NULL, NULL, '7688246f-bbb6-4121-8eef-4b168753d529', '2026-06-08 12:41:15', '2026-06-08 12:41:15'),
(2, 1, 1058, 'DIRECTOR', 'NA', 2, 'pending', NULL, NULL, 'c681cfe6-8cc6-4a44-b9a4-79f56fe2a92a', '2026-06-08 12:41:15', '2026-06-08 12:41:15'),
(3, 1, 1057, 'PRESIDENT DIRECTOR', 'NA', 3, 'pending', NULL, NULL, '8e82c723-8315-43ab-b187-d6e4e68fb3a5', '2026-06-08 12:41:15', '2026-06-08 12:41:15'),
(6, 3, 1093, 'IT STAFF', 'Kaizen Development', 1, 'approved', '2026-06-19 14:47:35', NULL, 'b8701f0b-d8e3-4120-b0fa-1be395ade582', '2026-06-19 14:46:11', '2026-06-19 14:47:35'),
(7, 3, 1092, 'IT STAFF', 'Kaizen Development', 2, 'waiting', NULL, NULL, 'd4d1dd99-2e2b-4f91-90c3-2b58a0345a5c', '2026-06-19 14:46:11', '2026-06-19 14:47:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `claim_overtimes`
--

CREATE TABLE `claim_overtimes` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `employee_attendance_id` bigint UNSIGNED DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `overtime_date` date DEFAULT NULL,
  `claim_overtime` date DEFAULT NULL,
  `total_work` int DEFAULT NULL,
  `actual_start_time` time DEFAULT NULL,
  `actual_end_time` time DEFAULT NULL,
  `agreed_work_start` time DEFAULT NULL,
  `agreed_work_end` time DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','approved','rejected','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `hrd_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `hrd_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrd_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `claim_overtimes`
--

INSERT INTO `claim_overtimes` (`id`, `employee_id`, `employee_attendance_id`, `position`, `area`, `department`, `overtime_date`, `claim_overtime`, `total_work`, `actual_start_time`, `actual_end_time`, `agreed_work_start`, `agreed_work_end`, `reason`, `source`, `status`, `hrd_knowledge`, `hrd_name`, `hrd_note`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4130, 62, 'KAIZEN DEVELOPMENT GROUP LEADER', 'KALIMANTAN', 'Engineering', '2026-06-08', '2026-06-08', 111, '05:54:00', '07:45:00', NULL, NULL, NULL, 'bf', 'waiting', 0, NULL, NULL, 'Diamond Kerluke', 'Diamond Kerluke', '2026-06-08 12:41:15', '2026-06-08 12:41:15'),
(3, 1093, 2031, 'IT STAFF', 'CJ1', 'Kaizen Development', '2026-06-19', '2026-06-19', 272, '15:45:00', '20:17:45', NULL, NULL, 'mau claim lembur pak bos', 'af', 'waiting', 0, NULL, NULL, 'FERDISAPUTRO', 'FERDISAPUTRO', '2026-06-19 14:46:11', '2026-06-19 14:46:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval` int DEFAULT NULL,
  `approval_code` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `name`, `approval`, `approval_code`, `created_at`, `updated_at`) VALUES
(1, 'HRD & GA', 1, 2, '2023-09-02 03:16:07', '2023-10-03 01:31:27'),
(2, 'ACC & FIN', 1, 2, '2023-09-02 03:16:45', '2023-09-02 03:16:45'),
(3, 'HSE', 1, 1, '2023-09-02 03:16:57', '2023-09-02 03:16:57'),
(4, 'Production', 2, 1, '2023-09-02 03:17:07', '2023-09-02 03:17:07'),
(5, 'Quality', 2, 1, '2023-09-02 03:17:15', '2023-09-02 03:17:15'),
(6, 'Kaizen Development', 2, 1, '2023-09-02 03:17:25', '2023-09-02 03:17:25'),
(7, 'Sales & Trade Marketing', 1, NULL, '2023-09-02 03:31:01', '2023-09-02 03:31:01'),
(8, 'Engineering', 2, 1, '2023-09-02 03:31:12', '2023-09-02 03:31:12'),
(9, 'Warehouse & Logistic', 2, 1, '2023-09-02 03:31:19', '2023-09-02 03:31:19'),
(10, 'Purchasing', 2, 1, '2023-09-02 03:31:27', '2023-09-02 03:31:27'),
(11, 'NA', 1, NULL, '2023-09-02 03:31:35', '2024-10-30 03:46:24'),
(12, 'BOD', 1, NULL, '2023-09-02 03:31:42', '2026-06-12 07:57:02'),
(13, 'Marketing', 1, NULL, '2023-09-02 11:29:12', '2023-09-02 11:29:13'),
(18, 'Regulatory', NULL, NULL, '2025-06-10 10:42:52', '2025-06-10 10:42:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `disposal_approval_paths`
--

CREATE TABLE `disposal_approval_paths` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `asset_disposal_id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `step_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctor_account`
--

CREATE TABLE `doctor_account` (
  `id` bigint UNSIGNED NOT NULL,
  `id_dokter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `doctor_account`
--

INSERT INTO `doctor_account` (`id`, `id_dokter`, `nama`, `email`, `created_at`, `updated_at`) VALUES
(1, '767504', 'dr. Prabangkara Dikma Billy Suryanegara', NULL, '2024-09-24 09:00:40', '2024-09-24 09:00:41'),
(2, '513163', 'dr. M. Praja Pratama', NULL, '2024-09-24 09:00:43', '2024-09-24 09:00:44'),
(3, '513165', 'dr. Diah M', NULL, '2024-09-24 09:00:43', '2024-09-24 09:00:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `drug_keluar`
--

CREATE TABLE `drug_keluar` (
  `id` bigint UNSIGNED NOT NULL,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tr_tanggal` date DEFAULT NULL,
  `kode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_patient` bigint DEFAULT NULL,
  `id_employee` bigint DEFAULT NULL,
  `id_drug` int DEFAULT NULL,
  `jml_drug` int DEFAULT NULL,
  `ket` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `drug_masuk`
--

CREATE TABLE `drug_masuk` (
  `id` bigint UNSIGNED NOT NULL,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_tanggal` date NOT NULL,
  `kode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_drug` int NOT NULL,
  `jml_drug` int NOT NULL,
  `id_user` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `earlyout_orlates`
--

CREATE TABLE `earlyout_orlates` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `work_in` time NOT NULL,
  `work_out` time NOT NULL,
  `hrd_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('approved','rejected','waiting') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `approved_by_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by_at` datetime NOT NULL,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_time_in` time DEFAULT NULL,
  `actual_time_out` time DEFAULT NULL,
  `security_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_ktp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addressktp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthplace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joindate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint DEFAULT NULL,
  `position_id` bigint DEFAULT NULL,
  `level_id` bigint DEFAULT NULL,
  `building_id` bigint DEFAULT NULL,
  `work_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_startdate` date DEFAULT NULL,
  `contract_number` int DEFAULT NULL,
  `domicile_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `emergency_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_handphone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `permanent_startdate` date DEFAULT NULL,
  `iso_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_center` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_education` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `major_last_education` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_education_institutional` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_dependents` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `outsourcing_vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bpjs_kesehatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bpjs_ketenagakerjaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latest_agreement_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_agreement_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_attendances`
--

CREATE TABLE `employee_attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `position_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_trip_id` bigint UNSIGNED DEFAULT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `master_workhour_id` bigint UNSIGNED NOT NULL,
  `work_in` time DEFAULT NULL,
  `work_out` time DEFAULT NULL,
  `date` date NOT NULL,
  `attendance_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holiday_id` bigint UNSIGNED DEFAULT NULL,
  `holiday_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holiday_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_attendance_details`
--

CREATE TABLE `employee_attendance_details` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_attendance_id` bigint UNSIGNED NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status_check_in` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_check_out` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latlong_check_in` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latlong_check_out` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_check_in` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_check_out` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_check_in` int DEFAULT NULL,
  `distance_check_out` int DEFAULT NULL,
  `out_of_range_check_in` tinyint(1) NOT NULL DEFAULT '0',
  `out_of_range_check_out` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_milestones`
--

CREATE TABLE `employee_milestones` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'career, reward, disciplinary',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Career(Promotion, Mutation, Demotion). Disciplinary(Teguran, SP1, SP2, SP3)',
  `date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_milestones`
--

INSERT INTO `employee_milestones` (`id`, `employee_id`, `category`, `type`, `date`, `description`, `created_at`, `updated_at`) VALUES
(1, 196, 'career', 'promotion', '2026-01-01', 'Promotion to IT Manager', '2026-03-03 09:43:13', '2026-03-03 09:43:13'),
(2, 196, 'career', 'mutation', '2025-01-01', 'from Section IT&GA to IT', '2026-03-04 08:56:29', '2026-03-04 08:56:29'),
(3, 196, 'disciplinary', 'sp1', '2025-01-01', 'Terlmbat 3x dalam 1 bulan', '2026-03-04 08:57:08', '2026-03-04 08:57:08'),
(4, 196, 'reward', NULL, '2025-01-01', 'Company Awards 2024 - Outstanding 1 Contribution - Improvement COGS from 58% to 49%', '2026-03-04 08:58:48', '2026-03-04 08:58:48'),
(5, 1075, 'disciplinary', 'warning', '2026-03-03', 'Dummys', '2026-03-06 08:12:25', '2026-03-06 08:13:19'),
(6, 1075, 'reward', NULL, '2026-03-09', 'dummys4', '2026-03-06 08:13:31', '2026-03-06 08:14:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition`
--

CREATE TABLE `employee_requisition` (
  `id` bigint UNSIGNED NOT NULL,
  `applicant_id` bigint UNSIGNED NOT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `needs` int DEFAULT NULL,
  `reason_requisition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_replaced_id` bigint UNSIGNED DEFAULT NULL,
  `reason_replacement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_replacement_other` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_period` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_experience` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_work_experience` int DEFAULT NULL,
  `qualification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `employment_date` date DEFAULT NULL,
  `decision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decision_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_pengajuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submit_date` datetime DEFAULT NULL,
  `approval1_id` bigint UNSIGNED DEFAULT NULL,
  `approval2_id` bigint UNSIGNED DEFAULT NULL,
  `approval3_id` bigint UNSIGNED DEFAULT NULL,
  `approval4_id` bigint UNSIGNED DEFAULT NULL,
  `approval1_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval2_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval3_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval4_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval1_date` datetime DEFAULT NULL,
  `approval2_date` datetime DEFAULT NULL,
  `approval3_date` datetime DEFAULT NULL,
  `approval4_date` datetime DEFAULT NULL,
  `fulfilled_date` datetime DEFAULT NULL,
  `fulfilled_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_requisition`
--

INSERT INTO `employee_requisition` (`id`, `applicant_id`, `position_id`, `department_id`, `section_id`, `area_id`, `needs`, `reason_requisition`, `person_replaced_id`, `reason_replacement`, `reason_replacement_other`, `employee_status`, `contract_period`, `work_experience`, `duration_work_experience`, `qualification`, `employment_date`, `decision`, `decision_comment`, `status`, `no_pengajuan`, `submit_date`, `approval1_id`, `approval2_id`, `approval3_id`, `approval4_id`, `approval1_as`, `approval2_as`, `approval3_as`, `approval4_as`, `approval1_date`, `approval2_date`, `approval3_date`, `approval4_date`, `fulfilled_date`, `fulfilled_reason`, `created_at`, `updated_at`) VALUES
(1, 196, 52, 1, NULL, 1, 3, 'Tambahan / Additional', NULL, NULL, NULL, 'Kontrak / Contract', '12', 'Dibutuhkan / Required', 1, 'System Development\r\nInfrastructure', '2026-01-01', 'APPROVED', 'go a head', 'DONE', 'RE25001', '2025-10-20 15:30:01', 634, 1057, NULL, NULL, 'Approval', 'President Director', NULL, NULL, '2025-10-24 15:28:41', '2025-10-27 13:59:56', NULL, NULL, NULL, NULL, '2025-10-20 15:26:51', '2025-10-27 13:59:56'),
(2, 101, 1, 4, 2, 1, 1, 'Penggantian / Replacement', 992, 'Kontrak Habis / End Contract', NULL, 'Kontrak / Contract', '6', 'Tidak dibutuhkan / Not Required (Freshgraduate)', NULL, 'â€¢ Pendidikan minimal SMK Teknik Mesin.\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\nâ€¢ Bersedia bekerja dengan sistem shift.\nâ€¢ Penempatan di Buduran, Sidoarjo.', '2026-01-04', 'APPROVED', NULL, 'DONE', 'RE25002', '2025-11-21 13:51:17', 594, 634, 1058, 1057, 'Checker', 'Approval', 'Director', 'President Director', '2025-11-21 13:53:55', '2025-11-21 13:59:23', '2025-11-21 14:00:30', '2025-12-22 08:49:26', NULL, NULL, '2025-11-21 13:48:55', '2025-12-22 08:49:26'),
(3, 101, 1, 4, 5, 1, 1, 'Penggantian / Replacement', 722, 'Kontrak Habis / End Contract', NULL, 'Kontrak / Contract', '6', 'Tidak dibutuhkan / Not Required (Freshgraduate)', NULL, 'â€¢ Pendidikan minimal SMK Teknik Mesin.\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\nâ€¢ Bersedia bekerja dengan sistem shift.\nâ€¢ Penempatan di Buduran, Sidoarjo.', '2026-01-04', 'APPROVED', NULL, 'DONE', 'RE25003', '2025-11-21 14:16:38', 594, 634, 1058, 1057, 'Checker', 'Approval', 'Director', 'President Director', '2025-12-19 14:54:13', '2025-12-19 14:54:23', '2025-12-19 14:54:30', '2025-12-22 08:49:34', NULL, NULL, '2025-11-21 14:16:20', '2025-12-22 08:49:34'),
(4, 101, 1, 4, 4, 1, 10, 'Tambahan / Additional', NULL, NULL, NULL, 'Kontrak / Contract', '3', 'Tidak dibutuhkan / Not Required (Freshgraduate)', NULL, 'â€¢ Pendidikan minimal SMK Teknik Mesin.\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\nâ€¢ Bersedia bekerja dengan sistem shift.\nâ€¢ Penempatan di Buduran, Sidoarjo.', '2026-01-05', 'APPROVED', NULL, 'DONE', 'RE25004', '2025-12-22 08:04:35', 594, 634, 1058, 1057, 'Checker', 'Approval', 'Director', 'President Director', '2025-12-22 08:07:35', '2025-12-22 08:07:41', '2025-12-22 08:07:46', '2025-12-22 08:49:34', NULL, NULL, '2025-12-19 14:48:09', '2025-12-22 08:49:34'),
(5, 101, 1, 4, 6, 1, 23, 'Tambahan / Additional', NULL, NULL, NULL, 'Kontrak / Contract', '3', 'Tidak dibutuhkan / Not Required (Freshgraduate)', NULL, 'â€¢ Pendidikan minimal SMK Teknik Mesin.\r\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\r\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\r\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\r\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\r\nâ€¢ Bersedia bekerja dengan sistem shift.\r\nâ€¢ Penempatan di Buduran, Sidoarjo.', '2026-01-12', 'APPROVED', NULL, 'DONE', 'RE25005', '2025-12-22 08:05:00', 594, 634, 1058, 1057, 'Checker', 'Approval', 'Director', 'President Director', '2025-12-22 08:08:03', '2025-12-22 08:08:11', '2025-12-22 08:08:15', '2025-12-22 08:49:34', NULL, NULL, '2025-12-19 14:49:31', '2025-12-22 08:49:34'),
(6, 101, 10, 4, 8, 1, 7, 'Tambahan / Additional', NULL, NULL, NULL, 'Kontrak / Contract', '6', 'Dibutuhkan / Required', 2, NULL, NULL, NULL, NULL, 'DRAFT', NULL, NULL, 594, 634, 1058, 1057, 'Checker', 'Approval', 'Director', 'President Director', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-19 14:50:28', '2025-12-19 14:50:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_educations`
--

CREATE TABLE `employee_requisition_educations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_requisition_educations`
--

INSERT INTO `employee_requisition_educations` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Sarjana / Bachelor Degree', '2025-10-20 15:26:51', '2025-10-20 15:26:51'),
(2, 'Diploma / Diploma Degree', '2025-11-21 13:48:55', '2025-11-21 13:48:55'),
(3, 'SMA / MA / SMK', '2025-11-21 14:16:20', '2025-11-21 14:16:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_genders`
--

CREATE TABLE `employee_requisition_genders` (
  `id` bigint UNSIGNED NOT NULL,
  `requisition_id` bigint UNSIGNED NOT NULL,
  `gender_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `needs_count` int NOT NULL,
  `start_age` int NOT NULL,
  `end_age` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_requisition_genders`
--

INSERT INTO `employee_requisition_genders` (`id`, `requisition_id`, `gender_name`, `needs_count`, `start_age`, `end_age`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pria / Male', 3, 24, 30, '2025-10-20 15:30:01', '2025-10-20 15:30:01'),
(3, 2, 'Wanita / Female', 1, 18, 30, '2025-11-21 13:51:17', '2025-11-21 13:51:17'),
(5, 3, 'Wanita / Female', 1, 18, 30, '2025-11-21 14:16:38', '2025-11-21 14:16:38'),
(9, 6, 'Pria / Male', 7, 20, 25, '2025-12-19 14:50:28', '2025-12-19 14:50:28'),
(10, 4, 'Wanita / Female', 10, 20, 25, '2025-12-22 08:04:35', '2025-12-22 08:04:35'),
(11, 5, 'Pria / Male', 8, 20, 25, '2025-12-22 08:05:00', '2025-12-22 08:05:00'),
(12, 5, 'Wanita / Female', 15, 20, 25, '2025-12-22 08:05:00', '2025-12-22 08:05:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_has_educations`
--

CREATE TABLE `employee_requisition_has_educations` (
  `requisition_id` bigint UNSIGNED NOT NULL,
  `education_id` bigint UNSIGNED NOT NULL,
  `major` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_requisition_has_educations`
--

INSERT INTO `employee_requisition_has_educations` (`requisition_id`, `education_id`, `major`) VALUES
(1, 1, 'Teknik Komputer, Teknologi Informasi, Sistem Informasi,'),
(2, 2, 'Farmasi, Umum'),
(3, 2, 'Farmasi / Umum'),
(3, 3, 'semua jurusan'),
(4, 3, 'Semua Jurusan'),
(5, 3, 'Semua Jurusan'),
(6, 3, 'Teknik Mesin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_has_recruitment_sources`
--

CREATE TABLE `employee_requisition_has_recruitment_sources` (
  `requisition_id` bigint UNSIGNED NOT NULL,
  `source_id` bigint UNSIGNED NOT NULL,
  `other_detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_hiring_steps`
--

CREATE TABLE `employee_requisition_hiring_steps` (
  `id` bigint UNSIGNED NOT NULL,
  `requisition_id` bigint UNSIGNED NOT NULL,
  `master_hiring_id` bigint UNSIGNED NOT NULL,
  `step_order` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_requisition_hiring_steps`
--

INSERT INTO `employee_requisition_hiring_steps` (`id`, `requisition_id`, `master_hiring_id`, `step_order`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2025-11-18 11:49:56', '2025-11-18 11:49:56'),
(2, 1, 5, 2, '2025-11-18 11:49:56', '2025-11-18 11:49:56'),
(3, 1, 6, 3, '2025-11-18 11:49:56', '2025-11-18 11:49:56'),
(4, 2, 2, 1, '2025-12-19 14:01:54', '2025-12-19 14:01:54'),
(5, 2, 3, 2, '2025-12-19 14:01:54', '2025-12-19 14:01:54'),
(6, 2, 4, 3, '2025-12-19 14:01:54', '2025-12-19 14:01:54'),
(7, 2, 5, 4, '2025-12-19 14:01:54', '2025-12-19 14:01:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_requisition_recruitment_sources`
--

CREATE TABLE `employee_requisition_recruitment_sources` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `appraisal_id` bigint UNSIGNED NOT NULL,
  `appraisal_position_id` bigint UNSIGNED NOT NULL,
  `appraisal_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval1_id` bigint UNSIGNED DEFAULT NULL,
  `approval2_id` bigint UNSIGNED DEFAULT NULL,
  `approval3_id` bigint UNSIGNED DEFAULT NULL,
  `approval4_id` bigint UNSIGNED DEFAULT NULL,
  `approval5_id` bigint UNSIGNED DEFAULT NULL,
  `approval6_id` bigint UNSIGNED DEFAULT NULL,
  `drafter_id` bigint UNSIGNED DEFAULT NULL,
  `approval1_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval2_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval3_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval4_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval5_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval6_as` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eval_start` date NOT NULL,
  `eval_end` date NOT NULL,
  `purpose` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpi_w` int DEFAULT NULL,
  `kpi_s` decimal(8,2) DEFAULT NULL,
  `kpi_sc` decimal(8,2) DEFAULT NULL,
  `kpi_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_managerial_w` int DEFAULT NULL,
  `ap_managerial_s` decimal(8,2) DEFAULT NULL,
  `ap_managerial_sc` decimal(8,2) DEFAULT NULL,
  `ap_managerial_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_ability_response_w` int DEFAULT NULL,
  `ap_ability_response_s` decimal(8,2) DEFAULT NULL,
  `ap_ability_response_sc` decimal(8,2) DEFAULT NULL,
  `ap_ability_response_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_leadership_w` int DEFAULT NULL,
  `ap_leadership_s` decimal(8,2) DEFAULT NULL,
  `ap_leadership_sc` decimal(8,2) DEFAULT NULL,
  `ap_leadership_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_accuracy_w` int DEFAULT NULL,
  `ap_accuracy_s` decimal(8,2) DEFAULT NULL,
  `ap_accuracy_sc` decimal(8,2) DEFAULT NULL,
  `ap_accuracy_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_capability_w` int DEFAULT NULL,
  `ap_capability_s` decimal(8,2) DEFAULT NULL,
  `ap_capability_sc` decimal(8,2) DEFAULT NULL,
  `ap_capability_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_initiative_w` int DEFAULT NULL,
  `ap_initiative_s` decimal(8,2) DEFAULT NULL,
  `ap_initiative_sc` decimal(8,2) DEFAULT NULL,
  `ap_initiative_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_kaizen_w` int DEFAULT NULL,
  `ap_kaizen_s` decimal(8,2) DEFAULT NULL,
  `ap_kaizen_sc` decimal(8,2) DEFAULT NULL,
  `ap_kaizen_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_responsibility_w` int DEFAULT NULL,
  `ap_responsibility_s` decimal(8,2) DEFAULT NULL,
  `ap_responsibility_sc` decimal(8,2) DEFAULT NULL,
  `ap_responsibility_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_discipline_w` int DEFAULT NULL,
  `ap_discipline_s` decimal(8,2) DEFAULT NULL,
  `ap_discipline_sc` decimal(8,2) DEFAULT NULL,
  `ap_discipline_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_cooperation_w` int DEFAULT NULL,
  `ap_cooperation_s` decimal(8,2) DEFAULT NULL,
  `ap_cooperation_sc` decimal(8,2) DEFAULT NULL,
  `ap_cooperation_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_w` int DEFAULT NULL,
  `ap_s` decimal(8,2) DEFAULT NULL,
  `ap_sc` decimal(8,2) DEFAULT NULL,
  `attendance_w` int DEFAULT NULL,
  `attendance_s` decimal(8,2) DEFAULT NULL,
  `attendance_sc` decimal(8,2) DEFAULT NULL,
  `attendance_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minus_poin` decimal(8,2) DEFAULT NULL,
  `total_score` decimal(8,2) DEFAULT NULL,
  `grade` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `positive` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `weakness` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note_hrd` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `decision_employment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_extend` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_extend` date DEFAULT NULL,
  `decision_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` datetime DEFAULT NULL,
  `approval1_date` datetime DEFAULT NULL,
  `approval2_date` datetime DEFAULT NULL,
  `approval3_date` datetime DEFAULT NULL,
  `approval4_date` datetime DEFAULT NULL,
  `approval5_date` datetime DEFAULT NULL,
  `approval6_date` datetime DEFAULT NULL,
  `drafter_date` datetime DEFAULT NULL,
  `approval1_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval2_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval3_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval4_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval5_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval6_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluation_attachments`
--

CREATE TABLE `evaluation_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluation_has_attachments`
--

CREATE TABLE `evaluation_has_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `evaluation_id` bigint UNSIGNED NOT NULL,
  `attachment_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluation_histories`
--

CREATE TABLE `evaluation_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `evaluation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `group_employees`
--

CREATE TABLE `group_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `group_employee_workhours`
--

CREATE TABLE `group_employee_workhours` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `group_employee_workhours`
--

INSERT INTO `group_employee_workhours` (`id`, `name`, `created_at`, `updated_at`) VALUES
(19, 'Test', '2026-04-17 03:33:47', '2026-04-17 03:33:47'),
(20, 'test2', '2026-04-17 03:36:55', '2026-04-17 03:36:55'),
(22, 'shift 3', '2026-05-06 03:00:01', '2026-05-06 03:00:01'),
(23, 'hrd and ga', '2026-06-19 09:23:08', '2026-06-19 09:23:08'),
(25, 'kaizen', '2026-06-19 09:45:39', '2026-06-19 09:45:39'),
(26, 'production', '2026-06-19 11:06:04', '2026-06-19 11:06:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `group_workhours`
--

CREATE TABLE `group_workhours` (
  `id` bigint UNSIGNED NOT NULL,
  `workhour_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `group_workhours`
--

INSERT INTO `group_workhours` (`id`, `workhour_id`, `group_id`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(49, 1, 19, '2026-04-17', NULL, 1, '2026-04-17 03:33:47', '2026-04-17 03:33:47'),
(50, 2, 20, '2026-04-17', NULL, 1, '2026-04-17 03:36:56', '2026-04-17 03:36:56'),
(54, 13, 23, '2026-06-19', NULL, 1, '2026-06-19 09:23:08', '2026-06-19 09:23:08'),
(58, 14, 25, '2026-06-19', NULL, 1, '2026-06-19 09:45:39', '2026-06-19 09:45:39'),
(64, 4, 26, '2026-06-19', '2026-06-28', 1, '2026-06-19 11:24:14', '2026-06-19 11:24:14'),
(65, 18, 26, '2026-06-29', NULL, 0, '2026-06-19 11:24:14', '2026-06-19 11:24:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hiring_step_has_employees`
--

CREATE TABLE `hiring_step_has_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `requisition_hiring_step_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `internal_rules`
--

CREATE TABLE `internal_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_berlaku` date NOT NULL,
  `tgl_kedaluwarsa` date DEFAULT NULL,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_revisi` date DEFAULT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rev` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `itsm_priorities`
--

CREATE TABLE `itsm_priorities` (
  `id` bigint UNSIGNED NOT NULL,
  `min_score` int NOT NULL,
  `max_score` int NOT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_sla_hours` int DEFAULT NULL,
  `max_sla_hours` int DEFAULT NULL,
  `sla_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `itsm_priorities`
--

INSERT INTO `itsm_priorities` (`id`, `min_score`, `max_score`, `level`, `min_sla_hours`, `max_sla_hours`, `sla_label`, `created_at`, `updated_at`) VALUES
(2, 11, 15, 'high', 4, 8, '-', '2026-03-26 02:25:42', '2026-04-28 11:50:12'),
(3, 6, 10, 'medium', NULL, 16, '<', '2026-03-26 02:25:42', '2026-04-28 11:51:23'),
(4, 1, 5, 'low', NULL, 40, '<', '2026-03-26 02:25:42', '2026-04-28 11:54:31'),
(5, 16, 21, 'critical', NULL, 4, '<', '2026-04-22 03:38:03', '2026-04-28 11:49:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `it_assets`
--

CREATE TABLE `it_assets` (
  `id` bigint UNSIGNED NOT NULL,
  `asset_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_type_id` bigint UNSIGNED NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `specification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `software` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `year_registered` date NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `employee_fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `it_assets`
--

INSERT INTO `it_assets` (`id`, `asset_code`, `asset_type_id`, `brand`, `specification`, `software`, `year_registered`, `price`, `employee_id`, `employee_fullname`, `employee_nik`, `employee_department`, `employee_position`, `employee_area`, `status`, `created_at`, `updated_at`) VALUES
(129, '01.01.19.002', 2, 'LENOVO IDEAPAD 330', 'LAPTOP, INTEL CORE I512 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebytes', '2019-01-19', 8965000.00, 529, 'TTIAN ADI RAHMAWAN', '2018010', 'HRD & GA', 'IT GROUP LEADER', 'HEADQUARTERS / FACTORY', 'backup', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(130, NULL, 2, 'LENOVO H530S', 'DESKTOP, Intel Pentium 3.0GHz6 GBHDD 512 GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebytes', '2012-05-01', 5335000.00, 513, 'LUKMAN NUR HAKIIM', '2017196', 'Kaizen Development', 'KAIZEN PROMOTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(131, '13.12.13.001', 2, 'LENOVO Thinkpad E330', 'LAPTOP, INTEL CORE I34 GBSSD512GB', 'WINDOWS 10 LTSC 64 BIT OFFICE 2013 STD Malwarebytes', '2013-12-05', 7675000.00, 865, 'IVA HELSIA DWI ROHMAWATI', '2022027', 'Production', 'ENGINEERING ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(132, NULL, 2, 'LENOVO H530S', 'DESKTOP, Intel Pentium 3.0GHz6 GBHDD 512GB', 'WINDOWS 8.1 PRO 32BIT OFFICE 2013 STD Malwarebytes', '2014-04-01', 5335000.00, 138, 'FIRDAUS', '2011015', 'Production', 'WAREHOUSE & LOGISTIC OPERATOR', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(133, NULL, 2, 'LENOVO H530S', 'DESKTOP, Intel Pentium 3.0GHz2 GBHDD 512GB', 'WINDOWS 8.1 PRO 32BIT OFFICE 2013 STD Malwarebytes', '2014-04-15', 4990000.00, 432, 'NUR ROKHIF RIZAD PRASTIYO', '2017115', 'Production', 'TECHNICIAN', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(134, NULL, 2, 'LENOVO H530S', 'DESKTOP, Intel Pentium 3.0GHz2 GBHDD 512GB', 'WINDOWS 7 PRO 32BIT OFFICE 2013 STD Malwarebytes', '2015-03-01', 4990000.00, 507, 'RATIH YENNY MARTA SARI', '2017190', 'Production', 'WAREHOUSE & LOGISTIC ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(135, '10.02.16.001.N', 2, 'DELL VOSTRO 3458', 'LAPTOP, INTEL CORE I34 GB', 'WINDOWS 8.1 PRO 64BIT OFFICE 2013 STD Malwarebytes', '2016-01-27', 0.00, 174, 'ROHMAN', '2013021', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(136, '03.01.17.001', 2, 'DELL VOSTRO 3458', 'LAPTOP, INTEL CORE I38 GBSSD 512GB', 'WINDOWS 10 OFFICE 2013 STD Malwarebyte', '2017-01-01', 5390000.00, 108, 'MASRUL MUJAHIDIN', '2009004', 'Production', 'WAREHOUSE & LOGISTIC GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(137, '11.01.15.001', 2, 'DELL INSPIRON 3437', 'LAPTOP, INTEL CORE I38 GBSSD 512GB', 'WINDOWS 10 OFFICE 2013 NA', '2015-01-23', 5800000.00, 317, 'MUHAMMAD NAGANO', '2016041', 'HRD & GA', 'GA GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(138, '13.06.17.002', 2, 'DELL INSPIRON 3000', 'LAPTOP, INTEL CORE I34 GB', 'WINDOWS 10 OFFICE 2013 STD Malwarebyte', '2017-06-15', 5351500.00, 153, 'SUJARWO', '2012016', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(139, NULL, 2, 'ASUS VIVOBOOK', 'LAPTOP, INTEL CORE i38 GBSSD 512 GB', 'MIZAN OFFICE 2013 STD Malwarebyte', '2022-04-01', 8000000.00, 45, 'MARIYATI', '1993001', 'Production', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(140, NULL, 2, 'ASUS NETBOOK TP201S', 'LAPTOP, INTEL QUAD CORE4 GBHDD 512GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebyte', '2018-04-11', 4900000.00, 115, 'VIDYA LISTYANTI', '2010006', 'Quality', 'QUALITY ASSURANCE GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(141, '05.01.17.001', 2, 'LENOVO THINKPAD E465', 'LAPTOP, AMD A108 GBSSD512GB', 'WINDOWS 10 LTSC OFFICE 365 Malwarebyte', '2017-01-01', 7250000.00, 513, 'LUKMAN NUR HAKIIM', '2017196', 'Kaizen Development', 'KAIZEN PROMOTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(142, '10.05.18.001.N', 2, 'ASUS E402W', 'LAPTOP, INTEL CELERON4 GB', 'WINDOWS 10 SL OFFICE 2010 STD Malwarebyte', '2018-05-01', 3960000.00, 355, 'ARI AYU RAHMAWATI', '2017038', 'Production', 'PRODUCTION ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(143, '13.09.19.004', 2, 'LENOVO THINKPAD E480', 'LAPTOP, INTEL CORE I58 GBSSD512GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebyte', '2019-09-01', 11935000.00, 155, 'AGUNG TRIWIBOWO', '2012020', 'Production', 'ENGINEERING JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(144, '12.01.20.001', 2, 'LENOVO IP S530-13IWL', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebyte', '2020-01-01', 8943000.00, 177, 'PHONTAS ANTON SUDIBYO', '2013027', 'HRD & GA', 'HUMAN RESOURCE DEVELOPMENT JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(145, '05.01.20.002', 2, 'LENOVO IP S530-13IWL', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebyte', '2020-01-01', 8943000.00, 513, 'LUKMAN NUR HAKIIM', '2017196', 'Kaizen Development', 'KAIZEN PROMOTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(146, NULL, 2, 'LENOVO S145', 'LAPTOP, INTEL CORE I3 8 GBSSD 512 GB', 'WINDOWS 10 SL 64 BIT OFFICE 2020 STD Malwarebyte', '2020-05-01', 7315000.00, 100, 'ARYOGI FEBRIANA', '2008001', 'Production', 'PRODUCTION ADMINISTRATION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(147, '09.07.20.005', 2, 'LENOVO IDEAPAD S145', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 10 SL 64 BIT OFFICE 2010 STD Malwarebyte', '2020-07-01', 7315000.00, 642, 'MUFIDATUN NISAK', '2019046', 'Quality', 'QUALITY CONTROL GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(148, '10.04.21.001', 2, 'LENOVO V14 IIL', 'LAPTOP, INTEL CORE I58 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 Malwarebyte', '2021-04-29', 9050000.00, 80, 'HERRY SUSANTO', '2001004', 'Production', 'WAREHOUSE & LOGISTIC JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(149, '05.10.21.002', 2, 'LENOVO IDEAPAD 5', 'LAPTOP, INTEL CORE I716 GBSSD 512 GB', 'WINDOWS 10 PRO-JAPAN OFFICE 365 Malwarebyte', '2021-10-01', 14600000.00, 178, 'ALBERT', '2013028', 'Kaizen Development', 'KAIZEN DEVELOPMENT & PDSO JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(150, '09.10.21.002', 2, 'IDEAPAD 5', 'LAPTOP, INTEL CORE I58 GBSSD 512 GB', 'WINDOWS 10 SL 64 BIT OUTLOOK 2013 Malwarebyte', '2021-10-01', 12375000.00, 333, 'TAUFAN NUGROHO', '2017016', 'Quality', 'QUALITY CONTROL ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(151, '11.12.03.001', 2, 'LENOVO IDEAPAD 5 SLIM', 'LAPTOP, INTEL CORE i58 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 365 Malwarebyte', '2021-12-01', 12485000.00, 594, 'SENTOT PURWANDI', '2018075', 'Production', 'GENERAL MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(152, '01.03.22.001', 2, 'LENOVO V14 G2-ITL', 'LAPTOP, INTEL CORE I516SSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 STD Malwarebytes', '2022-03-22', 12210000.00, 840, 'AGUS NURBAGYONO', '2022002', 'ACC & FIN', 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(153, '08.06.22.004', 2, 'ASUS X415', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2013 Malwarebyte', '2022-06-01', 8100000.00, 434, 'SITI MUNAWAROH', '2017117', 'Quality', 'QUALITY ASSURANCE ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(154, '08.06.22.002', 2, 'ASUS X415', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2013 Malwarebyte', '2022-06-01', 8100000.00, 115, 'VIDYA LISTYANTI', '2010006', 'Quality', 'QUALITY ASSURANCE GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(155, '08.06.22.003', 2, 'ASUS X415', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2013 Malwarebyte', '2022-06-01', 8100000.00, 70, 'NIMATUL CHUSNAH', '1997010', 'Quality', 'QUALITY ASSURANCE DOCUMENTATION STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(156, '07.09.22.001', 2, 'LENOVO V14 G2', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 2013 Malwarebyte', '2022-09-01', 6150000.00, 709, 'ABU RIZAL ARIFIANSYAH', '2020049', 'Purchasing', 'PURCHASING STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(157, '14.10.22.001', 2, 'LENOVO V14 G2', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 10 SL OFFICE 20213 Malwarebyte', '2022-10-01', 6150000.00, 911, 'FAHRIAL FIRMANSYAH', '2022073', 'HSE', 'HSE STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(158, '05.12.22.004', 2, 'LENOVO YOGA SLIM 7', 'LAPTOP, INTEL CORE I716 GBSSD 1TB', 'WINDOWS 11 SL OFFICE 2013 Malwarebytes', '2022-12-15', 18000000.00, 196, 'NAUVAL MUNIF', '2014033', 'HRD & GA', 'IT Manager', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(159, NULL, 2, 'LENOVO IDEAPAD SLIM 5i', 'LAPTOP, INTEL CORE i516 GBSSD 1 TB', NULL, '2023-01-01', 10782000.00, 627, 'DEO RISTIADI', '2019031', 'Production', 'ENGINEERING SECTION CHIEF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(160, NULL, 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE i316 GBSSD 512 GB', NULL, '2023-01-01', 5585000.00, 186, 'MOCHAMMAD FATHKUR ROZI', '2014004', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(161, NULL, 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE i316 GBSSD 512 GB', NULL, '2023-01-01', 5585000.00, 711, 'ANDI SUBAGIO', '2020051', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(162, NULL, 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE i316 GBSSD 512 GB', NULL, '2023-01-01', 5585000.00, 355, 'ARI AYU RAHMAWATI', '2017038', 'Production', 'PRODUCTION ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(163, '0', 2, 'LENOVO', 'LAPTOP, LENOVO IDEAPAD, INTEL CORE i3 RAM 16GB, SSD 512 GB', NULL, '2023-01-01', 5585000.00, 745, 'RAZAK DARDIRI', '2021007', 'Quality', 'PRODUCT DEVELOPMENT ANALYST', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 10:47:41'),
(164, '13.03.23.005', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I516 GBSSD 1 TB', NULL, '2023-03-01', 9900000.00, 667, 'SURYA ADI SAPUTRA', '2020007', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(165, '04.04.23.004', 2, 'LENOVO FLEX 5', 'LAPTOP, INTEL CORE i716 GBSSD 512GB', NULL, '2023-04-01', 11800000.00, 634, 'WAWAN SUPRIYANTO', '2019038', 'HRD & GA', 'HRD & GA GENERAL MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(166, '11.03.23.002', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-03-01', 6200000.00, 725, 'MOCH ADWIYAN ROY FRIYADI', '2020065', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(167, '11.03.23.003', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-03-01', 6200000.00, 302, 'SISWANTO', '2016026', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(168, '08.03.23.001', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-03-01', 6200000.00, 878, 'RACHMITA FARADILA EFENDI', '2022040', 'Quality', 'QUALITY ASSURANCE ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(169, '08.03.23.002', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-03-01', 6200000.00, 877, 'MUHAMMAD RIZAL ALFANDI', '2022039', 'Quality', 'COMPLIANCE STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(170, NULL, 2, 'LENOVO IDEAPAD 320', 'LAPTOP, AMD A48 GBSSD 512 GB', 'WINDOWS 10 PRO 64BIT OFFICE 2013 STD Malwarebyte', '2018-04-18', 4000000.00, 877, 'MUHAMMAD RIZAL ALFANDI', '2022039', 'Quality', 'COMPLIANCE STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(171, '05.09.22.003', 2, 'ASUS VIVOBOOK', 'LAPTOP, INTEL CORE i716 GBSSD 512 GB', 'WINDOWS 10 PRO OFFICE 365 Malwarebyte', '2022-09-01', 13000000.00, 529, 'TTIAN ADI RAHMAWAN', '2018010', 'HRD & GA', 'IT GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(172, '03.04.23.001', 2, 'LENOVO V14', 'LAPTOP, INTEL CORE I516 GBSSD 512GB', NULL, '2023-04-01', 8500000.00, 181, 'SEPTALIA META KARINA', '2013031', 'HRD & GA', 'HR & COMBEN SECTION CHIEF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(173, '10.04.23.003', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-04-10', 6882000.00, 188, 'KISMA PRASETYANING ATMOJO', '2014007', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(174, '11.04.23.004', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', NULL, '2023-04-10', 6882000.00, 106, 'RINA KUSNIA', '2009002', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(175, '08.07.23.004', 2, 'LENOVO YOGA 6', 'LAPTOP, AMD RYZEN 5 16 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 365 Malwarebyte', '2023-07-01', 12900000.00, 151, 'RINI SUSANTI', '2012014', 'Quality', 'QUALITY GENERAL MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(176, NULL, 2, 'HP Victus 16', 'LAPTOP, INTEL CORE i716 GBSSD 512 GB', NULL, '2023-11-01', 15900000.00, 667, 'SURYA ADI SAPUTRA', '2020007', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(177, '11.01.24.002', 2, 'LENOVO IP SLIM 5', 'LAPTOP, AMD Ryzen16 GBSSD 512 GB', NULL, '2024-01-01', 9730000.00, 223, 'DADALI HAMUKTI WICAKSANA', '2015015', 'Production', 'PRODUCTION SECTION CHIEF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(178, '13.01.24.002', 2, 'LENOVO IDEAPAD 5', 'LAPTOP, INTEL CORE I516 GBSSD 1 TB', NULL, '2024-01-01', 11968020.00, 627, 'DEO RISTIADI', '2019031', 'Production', 'ENGINEERING SECTION CHIEF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(179, '13.03.24.003', 2, 'LENOVO IDEAPAD SLIM 5', 'LAPTOP, INTEL CORE i516 GBSSD 512 GB', NULL, '2024-03-22', 10150000.00, 628, 'DAMAR ADI SUWASONO', '2019032', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(180, '09.03.24.002', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE i316 GBSSD 512 GB', NULL, '2024-03-22', 6300000.00, 766, 'MALISDA IRWANTRI LEONALD ', '2023064', 'Quality', 'QUALITY CONTROL STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(181, '09.03.24.003', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE i316 GBSSD 512 GB', NULL, '2024-03-22', 6300000.00, 1037, 'TRI MEGA YULIARTI', '2023088', 'Quality', 'QUALITY ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(182, '09.11.24.010', 2, 'PC RAKITAN', 'DESKTOP, INTEL CORE i5 1240016 GBSSD 512 GB', NULL, '2024-11-01', 9680000.00, 333, 'TAUFAN NUGROHO', '2017016', 'Quality', 'QUALITY CONTROL ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(183, '08.11.24.005', 2, 'LENOVO IDEAPAD SLIM 5 Light', 'LAPTOP, RYZEN 7 770016 GBSSD 1 TB', NULL, '2024-11-01', 9910000.00, 224, 'MOHAMMAD CHOIRUL MIZAN', '2015017', 'Quality', 'RESEARCH & DEVELOPMENT ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(184, '13.11.24.013', 2, 'PC RAKITAN', 'DESKTOP, INTEL CORE i5 1240016 GBSSD 512 GB', NULL, '2024-11-01', 9180000.00, 729, 'FAJAR ALIF CHALIFATULLAH', '2020069', 'Production', 'TECHNICIAN', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(185, '11.11.24.017', 2, 'LENOVO IDEAPAD SLIM 5 Light', 'LAPTOP, RYZEN 7 770016 GBSSD 1 TB', 'WINDOWS 11 SL, OFFICE 365 ONLINE, Malwarebyte', '2024-11-01', 9910000.00, 191, 'MARCELLUS NUNUNG YUDHA PUTRANTO', '2014012', 'Production', 'PRODUCTION JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(186, '01.11.24.001', 2, 'LENOVO YOGA PRO 7', 'LAPTOP, INTEL CORE I732 GBSSD 1 TB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2024-11-01', 23460000.00, 708, 'BUDI WAHYU JATMIKO', '2020048', 'ACC & FIN', 'GENERAL MANAGER ACC & FNC', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(187, '09.07.24.006', 2, 'PC RAKITAN', 'DESKTOP, INTEL CORE I516 GBSSD 512 GB', NULL, '2024-09-01', 10045500.00, 333, 'TAUFAN NUGROHO', '2017016', 'Quality', 'QUALITY CONTROL ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(188, '06.07.24.001', 2, 'Lenovo V14 - G4 i5', 'LAPTOP, INTEL CORE I516GBSSD 512 GB', NULL, '2024-07-01', 10861350.00, 363, 'HABIBIE ARDIYANSYAH', '2017046', 'HSE', 'HSE ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(189, '11.06.24.004', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I316GBSSD 512GB', NULL, '2024-06-27', 6654450.00, 167, 'IKA RUSTIANA', '2013007', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(190, '04.06.24.002', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I316GBSSD 512GB', NULL, '2024-06-27', 6654450.00, 317, 'MUHAMMAD NAGANO', '2016041', 'HRD & GA', 'GA GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(191, '11.06.24.005', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I316GBSSD 512GB', NULL, '2024-06-27', 6654450.00, 354, 'EKO SANTOSO', '2017037', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(192, '11.06.24.006', 2, 'LENOVO IDEAPAD 3', 'LAPTOP, INTEL CORE I316GBSSD 512GB', NULL, '2024-06-27', 6654450.00, 147, 'SITI CHUMAIROH', '2012008', 'Production', 'PRODUCTION ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(193, '14.06.24.002', 2, 'LENOVO IDEAPAD 5', 'LAPTOP, INTEL CORE I516GBSSD 512GB', 'WINDOWS 11 OFFICE 365 MALWAREBYTE', '2024-06-27', 10861350.00, 512, 'HENDY RAMDITYO WICAKSONO', '2017195', 'HRD & GA', 'LEGAL COMPLIANCE & ORGANIZATION DEVELOPMENT JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(194, '03.06.24.002', 2, 'LENOVO IDEAPAD 5', 'LAPTOP, INTEL CORE I316GBSSD 512GB', NULL, '2024-06-27', 10861350.00, 177, 'PHONTAS ANTON SUDIBYO', '2013027', 'HRD & GA', 'HUMAN RESOURCE DEVELOPMENT JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(195, '03.01.24.001', 2, 'LENOVO IDEAPAD SLIM 5i', 'LAPTOP, INTEL CORE i516 GBSSD 512 GB', NULL, '2024-01-22', 10428450.00, 756, 'ANNISSA PASHA SETYADINAR', '2021019', 'Production', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(196, '13.01.24.001', 2, 'LENOVO IDEAPAD SLIM 5i', 'LAPTOP, INTEL CORE I7, 16GB, SSD512GB', NULL, '2024-01-22', 13098000.00, 155, 'AGUNG TRIWIBOWO', '2012020', 'Production', 'ENGINEERING JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 10:10:56'),
(197, '10.01.24.002', 2, 'LENOVO IDEAPAD SLIM 3i', 'LAPTOP, INTEEL CORE I316 GBSSD 512 GB', NULL, '2024-01-22', 6199350.00, 711, 'ANDI SUBAGIO', '2020051', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(198, '08.01.24.001', 2, 'LENOVO IDEAPAD SLIM 3i', 'LAPTOP, INTEEL CORE I316 GBSSD 512 GB', NULL, '2024-01-22', 6199350.00, 745, 'RAZAK DARDIRI', '2021007', 'Quality', 'PRODUCT DEVELOPMENT ANALYST', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(199, '10.01.24.003', 2, 'LENOVO IDEAPAD SLIM 3i', 'LAPTOP, INTEEL CORE I316 GBSSD 512 GB', NULL, '2024-01-22', 6199350.00, 355, 'ARI AYU RAHMAWATI', '2017038', 'Production', 'PRODUCTION ADMINISTRATION', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(200, '10.01.24.001', 2, 'LENOVO IDEAPAD SLIM 3i', 'LAPTOP, INTEEL CORE I316 GBSSD 512 GB', NULL, '2024-01-22', 6199350.00, 186, 'MOCHAMMAD FATHKUR ROZI', '2014004', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(201, '01.11.23.001', 2, 'LENOVO IDEAPAD SLIM 5', 'LAPTOP, INTEL CORE I516 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2023-11-01', 11044500.00, 118, 'ANADION SUNANTO', '2010009', 'ACC & FIN', 'ACCOUNTING JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(202, '01.11.23.002', 2, 'LENOVO IDEAPAD SLIM 5', 'LAPTOP, INTEL CORE I516 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2023-11-01', 11044500.00, 629, 'MUALLA MUFARRISTI', '2019033', 'ACC & FIN', 'FINANCE GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(203, '05.04.22.001', 2, 'LENOVO IDEAPAD 5', 'LAPTOP, INTEL CORE I5, RAM16 GB, SSD 512GB', NULL, '2022-04-30', 14850000.00, 118, 'ANADION SUNANTO', '2010009', 'ACC & FIN', 'ACCOUNTING JUNIOR MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 10:49:03'),
(204, '05.03.21.008', 2, 'ASUS A4166JA-FHD322', 'LAPTOP, INTEL CORE I38GBSSD 512GB', NULL, '2021-03-01', 8305000.00, 96, 'MUJIHARTO', '2005002', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(205, NULL, 2, 'Mini PC InteL', 'DESKTOP, INTELCELERON4 GBSSD 512GB', NULL, '2024-02-01', 1846847.00, 667, 'SURYA ADI SAPUTRA', '2020007', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(206, NULL, 2, 'Mini PC InteL', 'DESKTOP, INTELCELERON4 GBSSD 512GB', NULL, '2024-02-01', 1846847.00, 667, 'SURYA ADI SAPUTRA', '2020007', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(207, NULL, 2, 'Mini PC InteL', 'DESKTOP, INTELCELERON4 GBSSD 512GB', NULL, '2024-02-01', 1846847.00, 667, 'SURYA ADI SAPUTRA', '2020007', 'Production', 'ENGINEERING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(208, '09.02.25.001', 2, 'LENOVO V14 - G3-iAP', 'LAPTOP, INTEL CORE I316 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 5675000.00, 232, 'VRINDO MANDRA SAPUTRA', '2015026', 'Quality', 'QUALITY CONTROL ANALYST', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(209, '05.02.25.002', 2, 'MSI MS-14N', 'LAPTOP, INTEL CORE 516 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 14750000.00, 859, 'GHONI FADLY ADHIMA', '2022021', 'HRD & GA', 'GA SECTION CHIEF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(210, '01.02.25.002', 2, 'LENOVO V14 - G4 IRU', 'LAPTOP, INTEL CORE I516 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 7700000.00, 119, 'CHRISTINA EMELIA YUNIATI', '2010010', 'ACC & FIN', 'ACCOUNTING & TAXATION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(211, '01.02.25.001', 2, 'LENOVO V14 - G4 IRU', 'LAPTOP, INTEL CORE I516 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 7700000.00, 120, 'AJENG AYUNINGTIYAS', '2010011', 'ACC & FIN', 'ACCOUNTING GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(212, '01.02.25.003', 2, 'LENOVO V14 - G4 IRU', 'LAPTOP, INTEL CORE I516 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 7700000.00, 624, 'HANUM ATIKA RISWANTI', '2019028', 'ACC & FIN', 'FINANCE STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(213, '07.02.25.001', 2, 'LENOVO IDEAPAD SLIM 5', 'LAPTOP, AMD RYZEN 716 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 9775000.00, 109, 'MOCHAMAD PURWANTO', '2009005', 'Purchasing', 'PURCHASING ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(214, '10.02.25.001', 2, 'LENOVO IDEAPAD SLIM 5', 'LAPTOP, AMD RYZEN 716 GBSSD 512GB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2025-02-24', 9775000.00, 101, 'M MIFTACHUL ULUM', '2008002', 'Production', 'PRODUCTION ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(215, '01.06.24.001', 2, 'MSI PRESTIGE 14EVO', 'LAPTOP, INTEL CORE I716 GBSSD 1TB', 'WINDOWS 11 SL OFFICE 2013 STD Malwarebyte', '2024-06-01', 17343000.00, 317, 'MUHAMMAD NAGANO', '2016041', 'HRD & GA', 'GA GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(216, '05.09.25.006', 2, 'LENOVO IDEAPAD SLIM 3', 'LAPTOP, INTEL CORE I516 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 365 Malwarebyte', '2025-09-08', 10783784.00, 513, 'LUKMAN NUR HAKIIM', '2017196', 'Kaizen Development', 'KAIZEN PROMOTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(217, '05.09.25.007', 2, 'LENOVO IDEAPAD SLIM 3', 'LAPTOP, INTEL CORE I516 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 365 Malwarebyte', '2025-09-08', 10783784.00, 255, 'DENIS INDRA KUSUMAH', '2015055', 'Kaizen Development', 'KAIZEN DEVELOPMENT GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(218, '09.12.25.002', 2, 'ASUS E1404FA', 'LAPTOP, Ryzen 5 7520U16 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2024 Malwarebyte', '2025-11-01', 8254500.00, 642, 'MUFIDATUN NISAK', '2019046', 'Quality', 'QUALITY CONTROL GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(219, '11.12.25.005', 2, 'Lenovo IdeaPad Slim 3', 'LAPTOP, INTEL CORE I38 GBSSD 512 GB', 'WINDOWS 11 SL OFFICE 2024 Malwarebyte', '2025-11-01', 6904500.00, 174, 'ROHMAN', '2013021', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(220, '04.12.25.002', 2, 'Lenovo IdeaPad Slim 3', 'LAPTOP, INTEL CORE I3,  8 GB ,SSD 512 GB', 'WINDOWS 11 SL, OFFICE 365 online, Malwarebyte, Corel Draw', '2025-11-01', 6904500.00, 723, 'PRATIWI DWI AGESTI', '2020063', 'HRD & GA', 'GA STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 10:44:07'),
(221, NULL, 2, 'ASUS E1404FA', 'LAPTOP, RYZEN 5-7520U-16GB-512 GB', NULL, '2026-01-01', 9280000.00, 138, 'FIRDAUS', '2011015', 'Production', 'WAREHOUSE & LOGISTIC OPERATOR', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(222, NULL, 2, 'ASUS E1404FA', 'LAPTOP, RYZEN 5-7520U-8GB-512 GB', NULL, '2026-01-01', 7944000.00, 599, 'NUR SALIMAH', '2019003', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(223, NULL, 2, 'Asus E1404FA', 'LAPTOP, RYZEN 5-7520U-8GB-512 GB', NULL, '2026-01-01', 7944000.00, 711, 'ANDI SUBAGIO', '2020051', 'Production', 'PRODUCTION GROUP LEADER', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30'),
(224, NULL, 2, 'ASUS VIVOBOOK S14', 'LAPTOP, RYZEN 7 AI-16GB-1 TB', NULL, '2026-03-01', 15525000.00, 1055, 'ERLANGGA LESMANA PUTRA', '41221172', 'HRD & GA', 'IT STAFF', 'HEADQUARTERS / FACTORY', 'active', '2026-04-28 09:59:30', '2026-04-28 09:59:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_posting`
--

CREATE TABLE `job_posting` (
  `id` bigint UNSIGNED NOT NULL,
  `requisition_id` bigint UNSIGNED NOT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `needs` int DEFAULT NULL,
  `employee_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `apply_start` date DEFAULT NULL,
  `apply_end` date DEFAULT NULL,
  `noted` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `job_posting`
--

INSERT INTO `job_posting` (`id`, `requisition_id`, `position_id`, `department_id`, `section_id`, `area_id`, `status`, `title`, `qualification`, `needs`, `employee_status`, `publish_id`, `publish_code`, `publish_date`, `apply_start`, `apply_end`, `noted`, `created_at`, `updated_at`) VALUES
(4, 1, 52, 1, NULL, 1, 'PUBLISH', 'STAFF IT', 'PT Hisamitsu Pharma Indonesia, perusahaan yang bergerak di industri farmasi dengan standar operasional tinggi, membuka kesempatan berkarier bagi individu berpengalaman untuk bergabung sebagai Staff IT di Sidoarjo. Jika Anda memiliki keahlian teknis dalam sistem utilitas dan ketertarikan bekerja di lingkungan manufaktur yang dinamis, ini saat yang tepat untuk bergabung bersama kami!\r\n\r\nKriteria yang Dicari: \r\nApakah Anda Kandidat yang Kami Butuhkan?\r\nâ€¢ Pria, usia antara 24â€“28 tahun.\r\nâ€¢ Pendidikan minimal S1 Sistem Informasi, Teknik Informatika, atau Ilmu Komputer.\r\nâ€¢ Pengalaman kerja 2â€“3 tahun di posisi serupa.\r\nâ€¢ Memiliki kemampuan analisis dan pemecahan masalah yang baik.\r\nâ€¢ Mampu bekerja secara mandiri maupun dalam tim.\r\nâ€¢ Siap bekerja dalam lingkungan kerja yang dinamis dan penuh tantangan.\r\n\r\nTugas dan Tanggung Jawab: \r\nApa yang Akan Anda Lakukan?\r\nâ€¢ Menangani infrastruktur IT dan perangkat keras\r\nâ€¢ Instalasi dan maintenance Server (Windows/Linux).\r\nâ€¢ Pengelolaan jaringan (Router, Switch, Firewall, Access Point).\r\nâ€¢ Troubleshooting PC, Laptop, dan perangkat periferal (Printer, Scanner).\r\nâ€¢ Sistem CCTV dan PABX.\r\nâ€¢ Memahami administrasi jaringan, termasuk konfigurasi Mikrotik, Cisco, VLAN, dan manajemen Bandwidth.\r\nâ€¢ Melakukan manajemen software, termasuk instalasi OS, aplikasi perkantoran, dan pemeliharaan Database dasar (SQL).\r\nâ€¢ Menangani sistem backup data dan memastikan keamanan siber (Antivirus, Firewall) berjalan optimal.\r\nâ€¢ Melakukan perbaikan dan perawatan rutin pada perangkat jaringan serta perakitan unit komputer.\r\nâ€¢ Melakukan dokumentasi teknis terkait inventaris aset IT dan laporan pemeliharaan berkala.\r\n\r\nKeuntungan Bergabung dengan PT Hisamitsu Pharma Indonesia\r\nâ€¢ Bekerja di perusahaan farmasi terkemuka dengan standar tinggi.\r\nâ€¢ Peluang pengembangan keterampilan teknis dan profesional.\r\nâ€¢ Lingkungan kerja yang mendukung dan kolaboratif.\r\nâ€¢ Tunjangan dan fasilitas yang kompetitif sesuai pengalaman.', 3, 'Kontrak / Contract', 'JP25110001', 'staff-packing-112025', '2025-12-22 14:43:53', '2025-11-03', '2025-11-30', NULL, '2025-11-03 15:34:57', '2025-12-22 14:43:53'),
(5, 2, 1, 4, 2, 1, 'PUBLISH', 'OPERATOR PACKING', 'Sehat Fisik  dan Jasmani / Fit to Work\r\nTerampil Mengoperasikan Komputer lebih disukai / Able to operate computer is preferable\r\nBerpengalaman dalam pengoperasian mesin lebih disukai / experience in machine operation is prefebale\r\nç§ã¯å¤§å­¦ç”Ÿã§ã™', 1, 'Kontrak / Contract', 'JP25110002', 'operator-packing-112025', '2025-11-21 15:34:22', '2025-11-21', '2025-12-31', 'ASAP', '2025-11-21 14:19:57', '2025-11-21 15:34:22'),
(6, 3, 1, 4, 5, 1, 'DRAFT', 'OPERATOR CALENDER', 'Sehat Fisik dan Jasmani\r\nTerampil mengoperasikan komputer\r\nBerpengelaman dalam mengoperasikan mesin\r\nã‚ã‚ŠãŒã¨ã”ã–ã„ã¾ã™', 1, 'Kontrak / Contract', NULL, NULL, NULL, '2026-01-01', '2026-01-10', NULL, '2025-12-19 14:56:27', '2025-12-19 14:56:27'),
(7, 4, 1, 4, 4, 1, 'DRAFT', 'OPERATOR BUNBURY 75', 'â€¢ Pendidikan minimal SMK Teknik Mesin.\r\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\r\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\r\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\r\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\r\nâ€¢ Bersedia bekerja dengan sistem shift.\r\nâ€¢ Penempatan di Buduran, Sidoarjo.', 10, 'Kontrak / Contract', NULL, NULL, NULL, '2026-01-01', '2026-01-09', NULL, '2025-12-22 08:44:23', '2025-12-22 08:44:23'),
(8, 5, 1, 4, 6, 1, 'DRAFT', 'OPERATOR CUTTING', 'â€¢ Pendidikan minimal SMK Teknik Mesin.\r\nâ€¢ Pengalaman kerja sebagai Teknisi atau posisi serupa minimal 1 tahun.\r\nâ€¢ Menguasai mesin bubut, mesin frais, dan CNC.\r\nâ€¢ Keahlian dalam pengoperasian peralatan dan instrumen teknis.\r\nâ€¢ Kepatuhan terhadap prosedur keamanan dan aturan teknis.\r\nâ€¢ Bersedia bekerja dengan sistem shift.\r\nâ€¢ Penempatan di Buduran, Sidoarjo.', 23, 'Kontrak / Contract', NULL, NULL, NULL, '2026-01-01', '2026-01-09', NULL, '2025-12-22 08:44:43', '2025-12-22 08:44:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `knowledge_bases`
--

CREATE TABLE `knowledge_bases` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `knowledge_bases`
--

INSERT INTO `knowledge_bases` (`id`, `title`, `content`, `status`, `published_at`, `level`, `author_id`, `created_at`, `updated_at`) VALUES
(285, 'IT Service Desk', '<p>&quot;Selamat&nbsp;datang&nbsp;di&nbsp;<strong>Pusat&nbsp;Pengetahuan&nbsp;(Knowledge&nbsp;Base)&nbsp;IT&nbsp;Service&nbsp;Desk</strong>.&nbsp;Temukan&nbsp;panduan&nbsp;langkah&nbsp;demi&nbsp;langkah&nbsp;dan&nbsp;solusi&nbsp;cepat&nbsp;untuk&nbsp;berbagai&nbsp;kendala&nbsp;teknis.&quot;</p>', 'published', '2026-06-19 12:56:13', 'some_employees', 1093, '2026-04-27 15:54:19', '2026-06-19 12:56:13'),
(286, 'Prosedur Tetap \"Pengelolaan Perangkat IT\" No.HPI-IT-DK-09 Rev.02', '<h3><strong>1.&nbsp;Tujuan</strong></h3>\r\n\r\n<p>Mendokumentasikan&nbsp;suatu&nbsp;prosedur&nbsp;standar&nbsp;bagaimana&nbsp;mengelola&nbsp;perangkat&nbsp;<em>Information&nbsp;Technology</em>&nbsp;(IT)&nbsp;agar&nbsp;dapat&nbsp;berjalan&nbsp;optimal&nbsp;&nbsp;melalui&nbsp;<em>&nbsp;</em>sistem&nbsp;INTRANET<em>.</em></p>\r\n\r\n<hr />\r\n<h3><strong>2.&nbsp;Ruang&nbsp;Lingkup</strong></h3>\r\n\r\n<p>2.1&nbsp;Pengelolaan&nbsp;Perangkat&nbsp;IT&nbsp;pada&nbsp;sistem&nbsp;INTRANET&nbsp;berada&nbsp;pada&nbsp;menu&nbsp;<em>IT&nbsp;Service&nbsp;Management</em>&nbsp;yang&nbsp;meliputi,</p>\r\n\r\n<p>2.1.1&nbsp;Pengelolaan&nbsp;aset&nbsp;yang&nbsp;terdiri&nbsp;dari&nbsp;:&nbsp;registrasi&nbsp;aset,&nbsp;perawatan&nbsp;aset,&nbsp;pemindahan&nbsp;aset,&nbsp;dan&nbsp;disposal&nbsp;aset</p>\r\n\r\n<p>2.1.2&nbsp;Layanan&nbsp;IT&nbsp;yang&nbsp;terdiri&nbsp;dari&nbsp;:&nbsp;penanganan&nbsp;insiden,&nbsp;permintaan&nbsp;perangkat,&nbsp;dan&nbsp;manajemen&nbsp;perubahan</p>\r\n\r\n<p>2.1.3&nbsp;Analisa&nbsp;Dampak,&nbsp;Urgensi,&nbsp;Scope,&nbsp;dan&nbsp;daftar&nbsp;resiko&nbsp;untuk&nbsp;menilai&nbsp;prioritas&nbsp;dari&nbsp;layanan</p>\r\n\r\n<hr />\r\n<h3><strong>3.&nbsp;Tanggung&nbsp;Jawab</strong></h3>\r\n\r\n<p>3.1&nbsp;Kepala&nbsp;Bagian&nbsp;IT&nbsp;mengkaji&nbsp;secara&nbsp;berkala&nbsp;Prosedur&nbsp;Tetap&nbsp;ini&nbsp;sesuai&nbsp;dengan&nbsp;perkembangan&nbsp;kondisi&nbsp;yang&nbsp;ada</p>\r\n\r\n<p>3.2&nbsp;Kepala&nbsp;Sub.&nbsp;Bagian&nbsp;IT&nbsp;melakukan&nbsp;revisi&nbsp;secara&nbsp;perkala&nbsp;Prosedur&nbsp;Tetap&nbsp;ini&nbsp;sesuai&nbsp;dengan&nbsp;perkembangan&nbsp;kondisi&nbsp;yang&nbsp;ada</p>\r\n\r\n<p>3.3&nbsp;Semua&nbsp;user&nbsp;mengikuti&nbsp;prosedur&nbsp;sesuai&nbsp;dengan&nbsp;prosedur&nbsp;tetap&nbsp;ini</p>\r\n\r\n<hr />\r\n<h3><strong>4.&nbsp;Prosedur</strong></h3>\r\n\r\n<p>4.1&nbsp;Pengelolaan&nbsp;Aset&nbsp;IT</p>\r\n\r\n<p>4.1.1&nbsp;Setiap&nbsp;aset&nbsp;didaftarkan&nbsp;pada&nbsp;menu&nbsp;&quot;IT&nbsp;Assets&quot;&nbsp;(IT&nbsp;Service&nbsp;Management&nbsp;-&nbsp;IT&nbsp;Assets)</p>\r\n\r\n<p>4.1.2&nbsp;Perawatan&nbsp;komputer&nbsp;harus&nbsp;dijadwalkan&nbsp;setidaknya&nbsp;1&nbsp;tahun&nbsp;sekali&nbsp;yang&nbsp;dapat&nbsp;dilakukan&nbsp;pada&nbsp;menu&nbsp;&quot;Asset&nbsp;Maintenance&quot;&nbsp;(IT&nbsp;Service&nbsp;Management&nbsp;-&nbsp;IT&nbsp;Assets&nbsp;-&nbsp;Asset&nbsp;Maintenance)</p>\r\n\r\n<p>4.1.3&nbsp;Pemindahan&nbsp;Aset&nbsp;dilakukan&nbsp;karena&nbsp;adanya&nbsp;penggunaan&nbsp;ulang&nbsp;(<em>reuse</em>)&nbsp;dari&nbsp;user&nbsp;lama&nbsp;ke&nbsp;user&nbsp;baru&nbsp;melalui&nbsp;<em>action&nbsp;</em>&quot;<em>Movement</em>&quot;&nbsp;(IT&nbsp;Asset&nbsp;-&nbsp;Movement)</p>\r\n\r\n<p>4.1.4&nbsp;Pemusnahan&nbsp;aset&nbsp;IT&nbsp;dapat&nbsp;dilakukan&nbsp;melalui&nbsp;aksi&nbsp;&quot;Asset&nbsp;Dispose&quot;&nbsp;pada&nbsp;menu&nbsp;IT&nbsp;Assets&nbsp;dengan&nbsp;ketentuan&nbsp;:</p>\r\n\r\n<ol>\r\n	<li>Akan&nbsp;dibeli&nbsp;oleh&nbsp;karyawan&nbsp;yang&nbsp;akan&nbsp;resign&nbsp;atau&nbsp;pensiun&nbsp;dan&nbsp;sudah&nbsp;mendapatkan&nbsp;persetujuan&nbsp;President&nbsp;Director,&nbsp;atau&nbsp;kondisi&nbsp;rusak&nbsp;setelah&nbsp;dilakukan&nbsp;analisa&nbsp;oleh&nbsp;pihak&nbsp;IT.</li>\r\n	<li>Pembeli&nbsp;(<em>Buyer</em>)&nbsp;harus&nbsp;diterdaftar&nbsp;saat&nbsp;membawa&nbsp;aset&nbsp;keluar&nbsp;dari&nbsp;perusahaan</li>\r\n	<li>Pemusnahan&nbsp;aset&nbsp;harus&nbsp;mendapatkan&nbsp;persetujuan&nbsp;Accounting&nbsp;&amp;&nbsp;Finance&nbsp;Department&nbsp;sebagai&nbsp;pelepasan&nbsp;aset&nbsp;dari&nbsp;sisi&nbsp;keuangan</li>\r\n</ol>\r\n\r\n<p>4.2&nbsp;Pengelolaan&nbsp;Layanan&nbsp;IT</p>\r\n\r\n<p>4.2.1&nbsp;Karyawan&nbsp;dapat&nbsp;mengajukan&nbsp;permintaan&nbsp;layanan&nbsp;kebutuhan&nbsp;IT&nbsp;melalu&nbsp;menu&nbsp;<em>IT&nbsp;Service&nbsp;Desk&nbsp;</em>di&nbsp;sistem&nbsp;INTRANET&nbsp;masing-masing&nbsp;karyawan&nbsp;yang&nbsp;nantinya&nbsp;sistem&nbsp;akan&nbsp;menerbitkan&nbsp;nomor&nbsp;tiket&nbsp;dari&nbsp;layanan&nbsp;tersebut.</p>\r\n\r\n<p>4.2.2&nbsp;Pihak&nbsp;IT&nbsp;juga&nbsp;bisa&nbsp;menerbitkan&nbsp;layanan&nbsp;melalui&nbsp;&quot;IT&nbsp;Inisiative&quot;&nbsp;yang&nbsp;menghasilkan&nbsp;nomor&nbsp;tiket&nbsp;yang&nbsp;bisa&nbsp;ditujukan&nbsp;kepada&nbsp;karyawan&nbsp;yang&nbsp;terlibat.</p>\r\n\r\n<p>4.2.3&nbsp;Pihak&nbsp;IT&nbsp;akan&nbsp;melakukan&nbsp;respon&nbsp;dan&nbsp;analisa&nbsp;dari&nbsp;permintaan&nbsp;tersebut.&nbsp;Apabila&nbsp;hasil&nbsp;analisa&nbsp;memerlukan&nbsp;memerlukan&nbsp;pengadaan&nbsp;unit&nbsp;baru,&nbsp;maka&nbsp;persetujuan&nbsp;atasan&nbsp;dan&nbsp;kepala&nbsp;departemen&nbsp;karyawan&nbsp;tersebut&nbsp;diperlukan.</p>\r\n\r\n<p>4.2.4&nbsp;Pengadaan&nbsp;unit&nbsp;baru&nbsp;harus&nbsp;mempertimbangan&nbsp;aspek&nbsp;:</p>\r\n\r\n<ol>\r\n	<li>Finansial,&nbsp;Nilai&nbsp;Buku&nbsp;(Book&nbsp;Value)&nbsp;dari&nbsp;aset&nbsp;sudah&nbsp;bernilai&nbsp;0&nbsp;rupiah</li>\r\n	<li>Lifetime,&nbsp;masa&nbsp;pakai&nbsp;rata-rata&nbsp;dari&nbsp;kategori&nbsp;Aset</li>\r\n	<li>Ketersedian&nbsp;suku&nbsp;cadang,&nbsp;apakah&nbsp;masih&nbsp;tersedia&nbsp;di&nbsp;pasaran</li>\r\n	<li>Estimasi&nbsp;harga&nbsp;perbaikan&nbsp;memerlukan&nbsp;biaya&nbsp;50%&nbsp;dari&nbsp;harga&nbsp;unit,</li>\r\n</ol>\r\n\r\n<hr />\r\n<h3><strong>5.&nbsp;Lampiran</strong></h3>\r\n\r\n<p>5.1</p>\r\n\r\n<hr />\r\n<h3><strong>6.&nbsp;Dokumen&nbsp;Rujukan</strong></h3>\r\n\r\n<p>6.1</p>\r\n\r\n<hr />\r\n<h3><strong>7.&nbsp;Referensi</strong></h3>\r\n\r\n<p>7.1</p>\r\n\r\n<p>&nbsp;</p>', 'published', '2026-06-19 11:18:51', 'all_employees', 1093, '2026-04-27 16:03:05', '2026-06-19 11:18:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `knowledge_base_media`
--

CREATE TABLE `knowledge_base_media` (
  `id` bigint UNSIGNED NOT NULL,
  `knowledge_base_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'image or file',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `knowledge_base_users`
--

CREATE TABLE `knowledge_base_users` (
  `id` bigint UNSIGNED NOT NULL,
  `knowledge_base_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `knowledge_base_users`
--

INSERT INTO `knowledge_base_users` (`id`, `knowledge_base_id`, `employee_id`, `created_at`, `updated_at`) VALUES
(37, 285, 1090, '2026-04-29 13:52:26', '2026-04-29 13:52:26'),
(38, 285, 1093, '2026-04-29 13:53:05', '2026-04-29 13:53:05'),
(39, 286, 188, '2026-05-04 10:33:35', '2026-05-04 10:33:35'),
(40, 285, 1092, '2026-06-19 12:56:13', '2026-06-19 12:56:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `late_histories`
--

CREATE TABLE `late_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_attendance_id` bigint UNSIGNED NOT NULL,
  `security_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `security_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrd_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `hrd_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `knowledgeby_hrdName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_knowledge` tinyint(1) NOT NULL DEFAULT '0',
  `knowledgeby_headName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_in` time NOT NULL,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `late_histories`
--

INSERT INTO `late_histories` (`id`, `employee_attendance_id`, `security_knowledge`, `security_name`, `hrd_knowledge`, `hrd_name`, `knowledgeby_hrdName`, `head_knowledge`, `knowledgeby_headName`, `reason`, `actual_in`, `approval_token`, `created_at`, `updated_at`) VALUES
(1, 15, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:12:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(2, 17, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:25:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(3, 28, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:07:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(4, 36, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:23:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(5, 42, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:39:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(6, 51, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:43:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(7, 65, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:29:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(8, 66, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:37:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(9, 69, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:42:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(10, 77, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:23:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(11, 86, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:06:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(12, 102, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:45:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(13, 105, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:08:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(14, 107, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:02:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(15, 111, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:14:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(16, 118, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:38:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(17, 121, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:20:00', NULL, '2026-06-08 04:45:12', '2026-06-08 04:45:12'),
(18, 124, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:29:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(19, 132, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:13:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(20, 136, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:42:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(21, 140, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:18:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(22, 142, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:42:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(23, 166, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:19:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(24, 196, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:15:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(25, 214, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:05:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(26, 229, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:17:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(27, 240, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:01:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(28, 242, 1, 'TESTEMPLOYEE', 0, NULL, NULL, 1, 'TESTEMPLOYEE', 'Bangun kesiangan', '14:48:27', NULL, '2026-06-08 04:45:13', '2026-06-08 07:48:43'),
(29, 246, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:42:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(30, 252, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:12:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(31, 259, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:43:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(32, 265, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:28:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(33, 288, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:38:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(34, 293, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:20:00', NULL, '2026-06-08 04:45:13', '2026-06-08 04:45:13'),
(35, 295, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:27:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(36, 296, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:34:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(37, 304, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:21:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(38, 306, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:10:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(39, 316, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:24:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(40, 334, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:45:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(41, 352, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:23:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(42, 361, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:11:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(43, 370, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:32:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(44, 374, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:15:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(45, 401, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:41:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(46, 403, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:14:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(47, 410, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:36:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(48, 423, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:27:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(49, 429, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:27:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(50, 430, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:40:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(51, 443, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:36:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(52, 445, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:11:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(53, 452, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:24:00', NULL, '2026-06-08 04:45:14', '2026-06-08 04:45:14'),
(54, 474, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:27:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(55, 483, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:19:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(56, 506, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:28:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(57, 530, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:38:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(58, 534, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:32:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(59, 540, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:02:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(60, 543, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:07:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(61, 581, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:42:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(62, 583, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:05:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(63, 591, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:15:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(64, 607, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:06:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(65, 611, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:34:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(66, 614, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:09:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(67, 618, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:27:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(68, 626, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:41:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(69, 632, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:06:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(70, 634, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:14:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(71, 636, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:08:00', NULL, '2026-06-08 04:45:15', '2026-06-08 04:45:15'),
(72, 642, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:01:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(73, 643, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:35:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(74, 644, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:31:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(75, 653, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:21:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(76, 660, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:40:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(77, 663, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:34:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(78, 686, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:41:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(79, 688, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:31:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(80, 704, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:03:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(81, 713, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:35:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(82, 721, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:23:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(83, 737, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:20:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(84, 747, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:10:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(85, 752, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:12:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(86, 772, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:29:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(87, 776, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:00:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(88, 783, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:42:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(89, 788, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:31:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(90, 789, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:25:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(91, 795, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:13:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(92, 798, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:03:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(93, 813, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:38:00', NULL, '2026-06-08 04:45:16', '2026-06-08 04:45:16'),
(94, 815, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:14:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(95, 821, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:16:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(96, 831, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:45:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(97, 833, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:24:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(98, 851, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:02:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(99, 852, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:13:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(100, 854, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:26:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(101, 883, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:21:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(102, 889, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:25:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(103, 894, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:40:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(104, 895, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:33:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(105, 898, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:09:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(106, 899, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:09:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(107, 905, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:24:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(108, 906, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:09:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(109, 943, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:15:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(110, 949, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:05:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(111, 956, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:01:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(112, 979, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:02:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(113, 980, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:04:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(114, 983, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:21:00', NULL, '2026-06-08 04:45:17', '2026-06-08 04:45:17'),
(115, 990, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:08:00', NULL, '2026-06-08 04:45:18', '2026-06-08 04:45:18'),
(116, 992, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:10:00', NULL, '2026-06-08 04:45:18', '2026-06-08 04:45:18'),
(117, 1006, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:04:00', NULL, '2026-06-08 04:45:18', '2026-06-08 04:45:18'),
(118, 1027, 1, 'FERDISAPUTRO', 0, NULL, NULL, 1, 'FERDISAPUTRO', 'Macet di jalan', '13:56:16', NULL, '2026-06-17 07:02:31', '2026-06-19 14:29:20'),
(119, 1098, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:04:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(120, 1103, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:00:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(121, 1108, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:02:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(122, 1109, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:13:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(123, 1118, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:43:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(124, 1126, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:11:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(125, 1133, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:19:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(126, 1135, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:31:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(127, 1136, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:42:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(128, 1164, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:17:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(129, 1168, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:03:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(130, 1184, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:29:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(131, 1210, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:16:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(132, 1228, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:23:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(133, 1235, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:38:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(134, 1278, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:16:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(135, 1292, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:19:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(136, 1310, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:22:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(137, 1311, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:21:00', NULL, '2026-06-17 07:02:31', '2026-06-17 07:02:31'),
(138, 1331, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:44:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(139, 1339, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:07:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(140, 1340, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:27:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(141, 1368, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:43:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(142, 1390, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:19:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(143, 1392, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:01:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(144, 1400, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:04:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(145, 1402, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:06:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(146, 1404, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:20:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(147, 1405, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:15:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(148, 1410, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:43:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(149, 1417, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:30:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(150, 1443, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:15:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(151, 1458, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:23:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(152, 1468, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:06:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(153, 1486, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:44:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(154, 1491, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:18:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(155, 1492, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:25:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(156, 1493, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:30:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(157, 1494, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:25:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(158, 1505, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:04:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(159, 1512, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:21:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(160, 1515, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:18:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(161, 1523, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:33:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(162, 1529, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:15:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(163, 1531, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:04:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(164, 1538, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:42:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(165, 1539, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:44:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(166, 1547, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:05:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(167, 1568, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:24:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(168, 1573, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:33:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(169, 1578, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:42:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(170, 1583, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:31:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(171, 1595, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:31:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(172, 1596, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:05:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(173, 1601, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:25:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(174, 1608, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:08:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(175, 1610, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:04:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(176, 1618, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:30:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(177, 1643, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:21:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(178, 1644, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:17:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(179, 1648, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:12:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(180, 1652, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:23:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(181, 1656, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:22:00', NULL, '2026-06-17 07:02:32', '2026-06-17 07:02:32'),
(182, 1670, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:37:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(183, 1672, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:15:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(184, 1679, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:38:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(185, 1680, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:45:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(186, 1693, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:16:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(187, 1717, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:20:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(188, 1718, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:15:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(189, 1720, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:04:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(190, 1723, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:38:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(191, 1741, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:30:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(192, 1750, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:11:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(193, 1754, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:35:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(194, 1755, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:42:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(195, 1762, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:10:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(196, 1768, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:23:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(197, 1776, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:22:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(198, 1791, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:01:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(199, 1805, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:29:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(200, 1818, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:03:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(201, 1819, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:32:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(202, 1820, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:09:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(203, 1825, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:08:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(204, 1837, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:05:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(205, 1838, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:10:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(206, 1841, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:32:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(207, 1843, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:05:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(208, 1854, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:15:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(209, 1855, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:20:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(210, 1857, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:34:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(211, 1871, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:14:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(212, 1887, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:26:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(213, 1889, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:34:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(214, 1891, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:44:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(215, 1892, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:37:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(216, 1907, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:20:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(217, 1909, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:17:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(218, 1916, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:02:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(219, 1923, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:31:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(220, 1924, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:22:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(221, 1925, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:19:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(222, 1934, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:14:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(223, 1946, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:39:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(224, 1958, 0, NULL, 0, NULL, NULL, 0, NULL, 'Macet di jalan', '08:08:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(225, 1959, 0, NULL, 0, NULL, NULL, 0, NULL, 'Kendaraan bermasalah', '08:26:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(226, 1962, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:21:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(227, 1985, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:35:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(228, 1986, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:43:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(229, 1989, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:01:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(230, 1991, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:01:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(231, 2001, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:01:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(232, 2008, 0, NULL, 0, NULL, NULL, 0, NULL, 'Bangun kesiangan', '08:39:00', NULL, '2026-06-17 07:02:33', '2026-06-17 07:02:33'),
(233, 2016, 0, NULL, 0, NULL, NULL, 0, NULL, 'Cuaca buruk', '08:37:00', NULL, '2026-06-17 07:02:34', '2026-06-17 07:02:34'),
(236, 2031, 0, NULL, 0, NULL, NULL, 0, NULL, 'tess tess', '10:17:18', NULL, '2026-06-19 10:17:18', '2026-06-19 10:17:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_approvals`
--

CREATE TABLE `leave_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `leave_request_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED NOT NULL,
  `approver_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int NOT NULL,
  `status` enum('waiting','approved','rejected','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `approved_at` timestamp NULL DEFAULT NULL,
  `reason_reject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leave_balance` int NOT NULL,
  `remaining_days` int NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('waiting','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `nik`, `employee_name`, `position`, `area`, `department`, `leave_type_id`, `type`, `request_date`, `start_date`, `end_date`, `total_days`, `attachment`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(444, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-05', 3, 'leave_attachments/b2hDdBmCn2mTtw4G27jstEgiin5LV9xixnxYsyDN.jpg', 'nikah alhamdulillah', 'rejected', 'Steve Satterfield Jr.', '2026-04-29 02:29:06', '2026-04-29 02:44:13'),
(445, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-05', 3, 'leave_attachments/ksxfd9TszzmzzJDW7IgVI4G1ukORgCiOTTyXjsOc.jpg', 'nikah kok malah ditolak', 'approved', 'Steve Satterfield Jr.', '2026-04-29 02:45:20', '2026-05-06 08:51:28'),
(448, 4079, 'EMP00001', 'Prof. Forrest West', 'NN', 'OIWJ1', 'Regulatory', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/1j8IcC0brLawCat5NpCpTNzvC5JZ8CovofiurFrO.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:40:45', '2026-04-29 04:40:45'),
(449, 4080, 'EMP00002', 'Ardith Pollich PhD', 'ASPO EJ 1', 'EJ2', 'Engineering', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/1j8IcC0brLawCat5NpCpTNzvC5JZ8CovofiurFrO.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:40:45', '2026-04-29 04:40:45'),
(450, 4081, 'EMP00003', 'Mr. Omari Shields', 'SALES REPRESENTATIVE', 'OIE', 'NA', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/1j8IcC0brLawCat5NpCpTNzvC5JZ8CovofiurFrO.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:40:45', '2026-04-29 04:40:45'),
(451, 4082, 'EMP00004', 'Dr. Grayce Jones II', 'PURCHASING STAFF', 'EJ2', 'President Director', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/1j8IcC0brLawCat5NpCpTNzvC5JZ8CovofiurFrO.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:40:45', '2026-04-29 04:40:45'),
(452, 4084, 'EMP00006', 'Prof. Myles Ward', 'COMPLIANCE STAFF', 'CJ2', 'HRD & GA', 3, 'pribadi', '2026-04-29', '2026-04-30', '2026-05-05', 3, NULL, 'p', 'approved', 'TESTEMPLOYEE', '2026-04-29 04:43:04', '2026-04-29 04:43:04'),
(453, 4085, 'EMP00007', 'Dr. Trever Satterfield PhD', 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', 'JAKARTA 1', 'NA', 3, 'pribadi', '2026-04-29', '2026-04-30', '2026-05-05', 3, NULL, 'p', 'approved', 'TESTEMPLOYEE', '2026-04-29 04:43:04', '2026-04-29 04:43:04'),
(454, 4091, 'EMP00013', 'Warren Abbott', 'TECHNICIAN OPERATOR', 'OIWJ2', 'HSE', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/awAED9vQwElwHmsLDyaaSNeunuSJKObz8qW36OUX.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:57:40', '2026-04-29 04:57:40'),
(455, 4090, 'EMP00012', 'Connor Torp', 'MEDICAL DEVICE ADMINISTRATION', 'MEDAN', 'HRD & GA', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-02', 3, 'leave_attachments/awAED9vQwElwHmsLDyaaSNeunuSJKObz8qW36OUX.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 04:57:40', '2026-04-29 04:57:40'),
(456, 4100, 'EMP00022', 'Polly Hill', 'HRD & GA GENERAL MANAGER', 'OIE', 'NA', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-05', 3, 'leave_attachments/eL6qxePqAR0OaHoDJW3LsjWkNyDrIC9c4qLdmgE0.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 06:05:55', '2026-04-29 06:05:55'),
(457, 4099, 'EMP00021', 'Howell Roberts', 'QUALITY CONTROL ASSISTANT MANAGER', 'CJ1', 'Purchasing', 6, 'normatif', '2026-04-29', '2026-04-30', '2026-05-05', 3, 'leave_attachments/eL6qxePqAR0OaHoDJW3LsjWkNyDrIC9c4qLdmgE0.jpg', NULL, 'approved', 'TESTEMPLOYEE', '2026-04-29 06:05:55', '2026-04-29 06:05:55'),
(458, 4130, 'EMP00052', 'Diamond Kerluke', 'KAIZEN DEVELOPMENT GROUP LEADER', 'KALIMANTAN', 'Engineering', 6, 'normatif', '2026-04-29', '2026-05-08', '2026-05-12', 3, 'leave_attachments/GPyZ6XgXhQUiTBOBL3SKvATDPC9ahNZuouhWfTcQ.jpg', NULL, 'approved', 'Diamond Kerluke', '2026-04-29 07:30:09', '2026-05-06 08:53:46'),
(459, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 6, 'normatif', '2026-04-29', '2026-05-08', '2026-05-12', 3, 'leave_attachments/ZokKtMjr5vbyq1rfGl6m40aIJNGJm8EnlOGOGdHa.jpg', NULL, 'waiting', 'Steve Satterfield Jr.', '2026-04-29 07:30:54', '2026-04-29 07:30:54'),
(460, 4080, 'EMP00002', 'Ardith Pollich PhD', 'ASPO EJ 1', 'EJ2', 'Engineering', 1, 'pribadi', '2026-05-06', '2026-05-11', '2026-05-12', 2, NULL, NULL, 'approved', 'TESTEMPLOYEE', '2026-05-06 07:54:25', '2026-05-06 07:54:25'),
(461, 4130, 'EMP00052', 'Diamond Kerluke', 'KAIZEN DEVELOPMENT GROUP LEADER', 'KALIMANTAN', 'Engineering', 8, 'normatif', '2026-05-06', '2026-06-01', '2026-06-02', 1, 'leave_attachments/CboMiY5oXNd6jItLS0pnCMoSVfPVDRDv4kbOaXzv.jpg', NULL, 'waiting', 'Diamond Kerluke', '2026-05-06 08:54:35', '2026-05-06 08:54:35'),
(462, 4130, 'EMP00052', 'Diamond Kerluke', 'KAIZEN DEVELOPMENT GROUP LEADER', 'KALIMANTAN', 'Engineering', 8, 'normatif', '2026-05-06', '2026-05-20', '2026-05-20', 1, 'leave_attachments/odXoe6wNhbMNEtYtpLBHHnaF622fXvc7ImCDlezi.jpg', NULL, 'waiting', 'Diamond Kerluke', '2026-05-06 08:57:00', '2026-05-06 08:57:00'),
(463, 1078, '12341234', 'DYNAMIC', 'ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'HRD & GA', 1, 'pribadi', '2026-06-08', '2026-06-09', '2026-06-10', 2, NULL, NULL, 'approved', 'TESTEMPLOYEE', '2026-06-08 12:19:11', '2026-06-08 12:19:11'),
(464, 4079, 'EMP00001', 'Prof. Forrest West', 'NN', 'OIWJ1', 'Regulatory', 2, 'pribadi', '2026-06-08', '2026-06-09', '2026-06-10', 2, NULL, NULL, 'approved', 'TESTEMPLOYEE', '2026-06-08 12:19:11', '2026-06-08 12:19:11'),
(466, 4083, 'EMP00005', 'Steve Satterfield Jr.', 'QUALITY CONTROL STAFF', 'OIWJ2', 'Kaizen Development', 6, 'normatif', '2026-06-17', '2026-06-25', '2026-06-29', 3, 'leave_attachments/MYavDdbq2BxZ9oWFhTCtmHvB1PfeZ7AnZWyUTuOr.png', NULL, 'waiting', 'Steve Satterfield Jr.', '2026-06-17 07:53:44', '2026-06-17 07:53:44'),
(467, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 1, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, 'cuti', 'approved', 'FERDISAPUTRO', '2026-06-19 10:39:06', '2026-06-19 10:39:06'),
(477, 1093, 'e41231827', 'FERDISAPUTRO', 'IT STAFF', 'CJ1', 'Kaizen Development', 1, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:39:41', '2026-06-19 13:39:41'),
(478, 1092, 'E41231827-1', 'FERDIAN SAPUTRO', 'IT STAFF', 'HEADQUARTERS / FACTORY', 'Kaizen Development', 1, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:39:41', '2026-06-19 13:39:41'),
(479, 1078, '2024001', 'AHMAD SUBAGJO', 'OPERATOR', 'MEDAN', 'HRD & GA', 1, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:44:08', '2026-06-19 13:44:08'),
(480, 1075, '23456789', 'DUMMY', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 1, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:44:08', '2026-06-19 13:44:08'),
(485, 8, '1982001', 'TJIOE RAHMAWATI INGDRIYANI WIJAYA', 'ADVISOR', 'HEADQUARTERS / FACTORY', 'ACC & FIN', 3, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:00', '2026-06-19 13:45:00'),
(486, 9, '1982002', 'IRAWATI TANDJUNG', 'ASSISTANT MANAGER', 'HEADQUARTERS / FACTORY', 'ACC & FIN', 3, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:00', '2026-06-19 13:45:00'),
(487, 10, '1982003', 'SITI MAISAROH', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:00', '2026-06-19 13:45:00'),
(488, 11, '1982005', 'FADELUN', 'KARU/KASI', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-22', '2026-06-23', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:00', '2026-06-19 13:45:00'),
(489, 2, '1980001', 'MUJIANAH', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(490, 3, '1980004', 'KUSMIATININGSIH', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(491, 4, '1980005', 'SUMINI', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(492, 5, '1980006', 'ENDANG INDAWATI', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(493, 6, '1980007', 'ENDANG WIWIK MUJIASIH', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(494, 7, '1981001', 'MAANI', 'OPERATOR', 'HEADQUARTERS / FACTORY', 'Production', 3, 'pribadi', '2026-06-19', '2026-06-29', '2026-06-30', 2, NULL, NULL, 'approved', 'FERDISAPUTRO', '2026-06-19 13:45:39', '2026-06-19 13:45:39'),
(495, 1092, 'E41231827-1', 'FERDIAN SAPUTRO', 'IT STAFF', 'HEADQUARTERS / FACTORY', 'Kaizen Development', 1, 'pribadi', '2026-06-19', '2026-07-06', '2026-07-07', 2, NULL, NULL, 'waiting', 'FERDIAN SAPUTRO', '2026-06-19 14:13:13', '2026-06-19 14:13:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_settings`
--

CREATE TABLE `leave_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_years` int DEFAULT NULL,
  `max_years` int DEFAULT NULL,
  `number_of_days` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `leave_settings`
--

INSERT INTO `leave_settings` (`id`, `type`, `description`, `min_years`, `max_years`, `number_of_days`, `created_at`, `updated_at`) VALUES
(1, 'pribadi', 'cuti 1 - 10 tahun', 1, 10, 12, '2026-04-14 01:02:00', '2026-04-14 01:02:00'),
(2, 'pribadi', 'cuti 10 - 16 tahun', 10, 16, 14, '2026-04-14 01:02:19', '2026-04-14 01:02:19'),
(3, 'pribadi', 'cuti 16 tahun keatas', 16, NULL, 15, '2026-04-14 01:02:32', '2026-04-14 01:02:32'),
(4, 'normatif', 'cuti melahirkan', NULL, NULL, 90, '2026-04-14 01:02:49', '2026-04-14 01:02:49'),
(5, 'normatif', 'cuti keguguran', NULL, NULL, 45, '2026-04-14 01:03:01', '2026-04-14 01:03:01'),
(6, 'normatif', 'cuti menikah', NULL, NULL, 3, '2026-04-14 01:03:11', '2026-04-14 01:03:11'),
(7, 'normatif', 'cuti meninggal dunia ( ayah atau ibu atau anak )', NULL, NULL, 2, '2026-04-14 01:03:43', '2026-04-14 01:03:43'),
(8, 'normatif', 'cuti meninggal dunia kerabat', NULL, NULL, 1, '2026-04-14 01:04:03', '2026-04-14 01:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_catatan_training`
--

CREATE TABLE `log_catatan_training` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` int DEFAULT NULL,
  `kode_fkt` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_fpkt` int DEFAULT NULL,
  `ip_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `log_catatan_training`
--

INSERT INTO `log_catatan_training` (`id`, `id_user`, `kode_fkt`, `id_fpkt`, `ip_address`, `action`, `catatan`, `created_at`, `updated_at`) VALUES
(2, 101, NULL, 7, '192.168.2.179', 'reject atasan', 'test reject assessment atasan', '2026-05-13 11:49:59', '2026-05-13 11:49:59'),
(3, 101, NULL, 7, '192.168.2.179', 'reject atasan', 'test catatan reject atasan assessment ke 2', '2026-05-13 12:12:19', '2026-05-13 12:12:19'),
(4, 101, NULL, 7, '192.168.2.179', 'reject atasan', 'test 3', '2026-05-13 12:14:15', '2026-05-13 12:14:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint UNSIGNED NOT NULL,
  `year` int DEFAULT NULL,
  `month` int DEFAULT NULL,
  `day` int DEFAULT NULL,
  `it_asset_id` bigint UNSIGNED NOT NULL,
  `owner_id` bigint UNSIGNED NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `building` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `maintenances`
--

INSERT INTO `maintenances` (`id`, `year`, `month`, `day`, `it_asset_id`, `owner_id`, `department`, `building`, `area`, `created_at`, `updated_at`) VALUES
(380, 2026, 6, NULL, 133, 432, 'Production', 'N/A', 'HEADQUARTERS / FACTORY', '2026-05-15 09:51:33', '2026-05-15 09:51:33'),
(381, 2026, 6, NULL, 134, 507, 'Production', 'N/A', 'HEADQUARTERS / FACTORY', '2026-05-15 09:51:33', '2026-05-15 09:51:33'),
(382, 2026, 6, NULL, 135, 174, 'Production', 'N/A', 'HEADQUARTERS / FACTORY', '2026-05-15 09:51:33', '2026-05-15 09:51:33'),
(383, 2026, 6, NULL, 136, 108, 'Production', 'N/A', 'HEADQUARTERS / FACTORY', '2026-05-15 09:51:33', '2026-05-15 09:51:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_appraisal`
--

CREATE TABLE `master_appraisal` (
  `id` bigint UNSIGNED NOT NULL,
  `position_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `form_type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpi_weight` int NOT NULL,
  `ap_weight` int NOT NULL,
  `ap_managerial` int NOT NULL,
  `ap_ability_response` int NOT NULL,
  `ap_leadership` int NOT NULL,
  `ap_accuracy` int NOT NULL,
  `ap_capability` int NOT NULL,
  `ap_initiative` int NOT NULL,
  `ap_kaizen` int NOT NULL,
  `ap_responsibility` int NOT NULL,
  `ap_discipline` int NOT NULL,
  `ap_cooperation` int NOT NULL,
  `ap_total` int NOT NULL,
  `attendance` int NOT NULL,
  `total` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_appraisal`
--

INSERT INTO `master_appraisal` (`id`, `position_id`, `status`, `department_id`, `section_id`, `form_type`, `kpi_weight`, `ap_weight`, `ap_managerial`, `ap_ability_response`, `ap_leadership`, `ap_accuracy`, `ap_capability`, `ap_initiative`, `ap_kaizen`, `ap_responsibility`, `ap_discipline`, `ap_cooperation`, `ap_total`, `attendance`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 'Permanent', 4, 2, 'A', 40, 20, 0, 10, 0, 5, 10, 5, 10, 5, 10, 5, 60, 40, 100, '2025-08-07 01:03:01', '2025-08-07 01:27:27'),
(2, 1, 'Contract', 4, 2, 'A', 25, 15, 0, 5, 0, 5, 5, 5, 5, 5, 5, 5, 40, 60, 100, '2025-08-07 01:04:31', '2025-08-07 01:28:24'),
(3, 3, 'Permanent', 4, 2, 'A', 60, 15, 15, 10, 10, 5, 5, 5, 10, 5, 5, 5, 75, 25, 100, '2025-08-07 01:09:42', '2025-08-07 01:32:22'),
(4, 5, 'Permanent', 4, 2, 'A', 45, 20, 0, 10, 5, 5, 10, 5, 10, 5, 10, 5, 65, 35, 100, '2025-08-07 01:14:19', '2025-08-07 01:22:39'),
(5, 6, 'Permanent', 7, NULL, 'B', 60, 40, 10, 10, 10, 5, 5, 5, 0, 5, 5, 5, 60, 100, 100, '2025-08-07 01:36:12', '2025-08-07 01:36:12'),
(6, 7, 'Permanent', 7, NULL, 'B', 60, 40, 20, 15, 10, 5, 5, 5, 0, 5, 5, 5, 75, 100, 100, '2025-08-07 01:37:51', '2025-08-07 01:37:51'),
(7, 2, 'Permanent', 2, NULL, 'A', 70, 15, 20, 20, 10, 5, 5, 5, 5, 5, 5, 5, 85, 15, 100, '2025-08-07 01:40:01', '2025-08-07 01:40:01'),
(8, 9, 'Permanent', 13, NULL, 'A', 0, 60, 0, 10, 0, 5, 10, 5, 0, 5, 10, 5, 50, 40, 100, '2025-08-12 07:13:05', '2025-08-12 07:13:05'),
(9, 81, 'Permanent', 3, NULL, 'A', 60, 15, 15, 10, 10, 5, 5, 5, 10, 5, 5, 5, 75, 25, 100, '2025-08-27 04:40:38', '2025-08-27 04:40:38'),
(10, 129, 'Permanent', 3, NULL, 'A', 40, 20, 0, 10, 0, 5, 10, 5, 10, 5, 10, 5, 60, 40, 100, '2025-08-27 04:42:32', '2025-08-27 04:42:32'),
(11, 122, 'Contract', 5, 9, 'A', 35, 15, 0, 10, 10, 5, 5, 5, 5, 5, 5, 5, 55, 50, 100, '2025-09-10 07:07:50', '2025-09-10 07:07:50'),
(12, 123, 'Contract', 4, 1, 'A', 25, 15, 0, 5, 0, 5, 5, 5, 5, 5, 5, 5, 40, 60, 100, '2025-09-10 07:10:12', '2025-09-10 07:10:12'),
(13, 45, 'Permanent', 5, 3, 'A', 70, 15, 20, 15, 10, 5, 5, 5, 10, 5, 5, 5, 85, 15, 100, '2025-09-16 10:39:57', '2025-09-16 10:39:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_building`
--

CREATE TABLE `master_building` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_building`
--

INSERT INTO `master_building` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'B1', '2025-08-21 03:23:15', '2025-08-21 03:23:15'),
(2, 'B2', '2025-08-21 03:23:26', '2025-08-21 03:23:26'),
(3, 'MD', '2025-08-21 03:23:35', '2025-08-21 03:23:35'),
(4, 'Utility', '2025-09-10 05:03:07', '2025-09-10 05:03:07'),
(5, 'Quality', '2025-09-10 05:03:26', '2025-09-10 05:03:26'),
(6, 'Office 2F', '2025-09-23 14:30:44', '2025-09-23 14:30:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_contract`
--

CREATE TABLE `master_contract` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_contract`
--

INSERT INTO `master_contract` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'CONTRACT 1', '2026-05-12 11:28:30', '2026-05-12 11:28:30'),
(3, 'CONTRACT 2', '2026-05-12 11:28:35', '2026-05-12 11:28:35'),
(4, 'CONTRACT 3', '2026-05-12 11:28:39', '2026-05-12 11:28:39'),
(5, 'CONTRACT 4', '2026-05-12 11:28:44', '2026-05-12 11:28:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_drug`
--

CREATE TABLE `master_drug` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_drug`
--

INSERT INTO `master_drug` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Amlodipin 5 mg Rama', '2024-07-18 00:18:27', '2024-07-18 00:24:43'),
(2, 'Amoxicillin 500 gr', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(3, 'Mefenamic Acid', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(4, 'Caviplex', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(5, 'Erla T. Telinga', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(6, 'Etagastrin', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(7, 'Genoint skin ointment 15 gr', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(8, 'Grathazon', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(9, 'Histigo Betasithine Mesilate', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(10, 'Cefadroxil Monohydrate', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(11, 'Hydrocortisone 2,5 % 5 gr', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(12, 'Inamid', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(13, 'Lambucid', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(14, 'Meloxicam', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(15, 'Miconazole krim 2% 10 gr', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(16, 'Mirapect Ambroxol', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(17, 'Neuralgin', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(18, 'Omeric', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(19, 'Paracetamol', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(20, 'Ramabion', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(21, 'Reco T Mata 10 ml', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(22, 'Simvastatin 10 mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(23, 'Renadinac 50 mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(24, 'Spasminal', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(25, 'Vitamin C PIM isi 1000', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(26, 'Demacolin', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(27, 'Flucadex', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(28, 'Grantusif', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(29, 'Berlosid', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(30, 'Ketokonazole', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(31, 'Gentamycin', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(32, 'Ranitidin', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(33, 'Braito tetes mata', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(34, 'Omeprazole', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(35, 'Y-rins Obat mata', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(36, 'Hansaplast Plester Cepat', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(37, 'Plester Roll', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(38, 'Grafachlor', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(39, 'Nisagon Cream', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(40, 'Ometilson', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(41, 'Methylprednisolon (Inxilon)', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(42, 'Paracetamol (Flutamol)', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(43, 'Transmuco 30 mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(44, 'Cefixim 100mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(45, 'MEFENTAN (Mefenamic Acid) 500 mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(46, 'Simvastatis 20 mg', '2024-07-18 00:18:27', '2024-07-18 00:18:27'),
(47, 'Acyclovir CR 5% 5 gr', '2024-07-29 01:57:16', '2024-07-29 01:57:16'),
(48, 'Cefixime 200 mg', '2024-09-25 01:38:12', '2024-09-25 01:38:12'),
(49, 'Amboxol 30 mg', '2024-09-25 01:41:18', '2024-09-25 01:41:18'),
(50, 'Betahistine 6 mg', '2024-09-25 01:42:56', '2024-09-25 01:42:56'),
(51, 'Cetirizine 10 mg', '2024-10-01 06:14:10', '2024-10-01 06:14:10'),
(52, 'Domperidone 10 mg', '2024-10-01 06:16:12', '2024-10-01 06:16:12'),
(53, 'Eperison 50 mg', '2024-10-01 06:17:04', '2024-10-01 06:17:04'),
(54, 'Flutrop', '2024-10-01 06:18:09', '2024-10-01 06:18:09'),
(55, 'Meloxicam 7,5 mg', '2024-10-01 06:21:25', '2024-10-01 06:21:25'),
(56, 'Omeprazole 20 mg', '2024-10-01 06:23:35', '2024-10-01 06:23:35'),
(57, 'RAMAFLU', '2024-10-01 06:25:07', '2024-10-01 06:25:07'),
(58, 'Sonamin', '2024-10-01 06:26:04', '2024-10-01 06:26:04'),
(59, 'Lodia', '2024-12-09 01:22:32', '2024-12-09 01:22:32'),
(60, 'Sanexon 4 mg', '2024-12-09 01:22:49', '2024-12-09 01:33:00'),
(61, 'Neurosanbe Plus', '2024-12-09 01:23:07', '2024-12-09 01:23:07'),
(62, 'Flamar 50 Mg', '2024-12-09 01:23:24', '2024-12-09 01:23:24'),
(63, 'Relaxon', '2024-12-09 01:23:37', '2024-12-09 01:23:37'),
(64, 'Sanorin Gargle 0.1%', '2024-12-09 01:31:01', '2024-12-09 01:31:01'),
(65, 'Ketokonazol Cream', '2024-12-09 01:31:13', '2024-12-09 01:31:13'),
(66, 'Betason N', '2024-12-09 01:31:22', '2024-12-09 01:31:22'),
(67, 'Gentamicin Salep Mata', '2024-12-09 01:31:38', '2024-12-09 01:32:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_hiring`
--

CREATE TABLE `master_hiring` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_hiring`
--

INSERT INTO `master_hiring` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Preliminary Interview', '2025-11-11 10:23:09', '2025-11-11 10:23:17'),
(3, 'Skill & Knowledge Test', '2025-11-11 10:23:29', '2025-11-11 10:23:29'),
(4, 'Psychology Test', '2025-11-11 10:24:12', '2025-11-11 10:25:27'),
(5, 'Final Interview', '2025-11-11 10:24:42', '2025-11-21 13:36:29'),
(6, 'Medical Check Up', '2025-11-11 10:24:52', '2025-11-11 10:24:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_kota`
--

CREATE TABLE `master_kota` (
  `id` int UNSIGNED NOT NULL,
  `provinsi_id` varchar(50) NOT NULL,
  `kabupaten_kota` varchar(50) NOT NULL,
  `ibukota` varchar(50) NOT NULL,
  `kode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `master_kota`
--

INSERT INTO `master_kota` (`id`, `provinsi_id`, `kabupaten_kota`, `ibukota`, `kode`) VALUES
(1, '1', 'Kabupaten Aceh Barat', 'Meulaboh', 'MBO'),
(2, '1', 'Kabupaten Aceh Barat Daya', 'Blangpidie', 'BPD'),
(3, '1', 'Kabupaten Aceh Besar', 'Jantho', 'JTH'),
(4, '1', 'Kabupaten Aceh Jaya', 'Calang', 'CAG'),
(5, '1', 'Kabupaten Aceh Selatan', 'Tapak Tuan', 'TTN'),
(6, '1', 'Kabupaten Aceh Singkil', 'Singkil', 'SKL'),
(7, '1', 'Kabupaten Aceh Tamiang', 'Karang Baru', 'KRB'),
(8, '1', 'Kabupaten Aceh Tengah', 'Takengon', 'TKN'),
(9, '1', 'Kabupaten Aceh Tenggara', 'Kutacane', 'KTN'),
(10, '1', 'Kabupaten Aceh Timur', 'Langsa', 'LGS'),
(11, '1', 'Kabupaten Aceh Utara', 'Lhoksukon', 'LSK'),
(12, '1', 'Kabupaten Bener Meriah', 'Simpang Tiga Redelong', 'STR'),
(13, '1', 'Kabupaten Bireuen', 'Bireuen', 'BIR'),
(14, '1', 'Kabupaten Gayo Lues', 'Blangkejeren', 'BKJ'),
(15, '1', 'Kabupaten Nagan Raya', 'Suka Makmue', 'SKM'),
(16, '1', 'Kabupaten Pidie', 'Sigli', 'SGI'),
(17, '1', 'Kabupaten Pidie Jaya', 'Meureundu', 'MRN'),
(18, '1', 'Kabupaten Simeulue', 'Sinabang', 'SNB'),
(19, '1', 'Kota Banda Aceh', 'Banda Aceh', 'BNA'),
(20, '1', 'Kota Langsa', 'Langsa', 'LGS'),
(21, '1', 'Kota Lhokseumawe', 'Lhokseumawe', 'LSM'),
(22, '1', 'Kota Sabang', 'Sabang', 'SAB'),
(23, '1', 'Kota Subulussalam', 'Subulussalam', 'SUS'),
(24, '2', 'Kabupaten Asahan', 'Kisaran', 'KIS'),
(25, '2', 'Kabupaten Batu Bara', 'Lima Puluh', 'LMP'),
(26, '2', 'Kabupaten Dairi', 'Sidikalang', 'SDK'),
(27, '2', 'Kabupaten Deli Serdang', 'Lubuk Pakam', 'LBP'),
(28, '2', 'Kabupaten Humbang Hasundutan', 'Dolok Sanggul', 'DLS'),
(29, '2', 'Kabupaten Karo', 'Kabanjahe', 'KBJ'),
(30, '2', 'Kabupaten Labuhanbatu', 'Rantau Prapat', 'RAP'),
(31, '2', 'Kabupaten Labuhanbatu Selatan', 'Kota Pinang', 'KPI'),
(32, '2', 'Kabupaten Labuhanbatu Utara', 'Aek Kanopan', 'AKK'),
(33, '2', 'Kabupaten Langkat', 'Stabat', 'STB'),
(34, '2', 'Kabupaten Mandailing Natal', 'Panyabungan', 'PYB'),
(35, '2', 'Kabupaten Nias', 'Gunungsitoli', 'GST'),
(36, '2', 'Kabupaten Nias Barat', 'Lahomi', 'LHM'),
(37, '2', 'Kabupaten Nias Selatan', 'Teluk Dalam', 'TLD'),
(38, '2', 'Kabupaten Nias Utara', 'Lotu', 'LTU'),
(39, '2', 'Kabupaten Padang Lawas', 'Sibuhuan', 'SBH'),
(40, '2', 'Kabupaten Padang Lawas Utara', 'Gunung Tua', 'GNT'),
(41, '2', 'Kabupaten Pakpak Bharat', 'Salak', 'SAL'),
(42, '2', 'Kabupaten Samosir', 'Pangururan', 'PRR'),
(43, '2', 'Kabupaten Serdang Bedagai', 'Sei Rampah', 'SRH'),
(44, '2', 'Kabupaten Simalungun', 'Pematang Siantar', 'PMS'),
(45, '2', 'Kabupaten Tapanuli Selatan', 'Padang Sidempuan', 'PSP'),
(46, '2', 'Kabupaten Tapanuli Tengah', 'Sibolga', 'SBG'),
(47, '2', 'Kabupaten Tapanuli Utara', 'Tarutung', 'TRT'),
(48, '2', 'Kabupaten Toba Samosir', 'Balige', 'BLG'),
(49, '2', 'Kota Binjai', 'Binjai', 'BNJ'),
(50, '2', 'Kota Gunungsitoli', 'Gunungsitoli', 'GST'),
(51, '2', 'Kota Medan', 'Medan', 'MDN'),
(52, '2', 'Kota Padang Sidempuan', 'Padang Sidempuan', 'PSP'),
(53, '2', 'Kota Pematangsiantar', 'Pematangsiantar', 'PMS'),
(54, '2', 'Kota Sibolga', 'Sibolga', 'SBG'),
(55, '2', 'Kota Tanjung Balai', 'Tanjung Balai', 'TJB'),
(56, '2', 'Kota Tebing Tinggi', 'Tebing Tinggi', 'TBT'),
(57, '3', 'Kabupaten Agam', 'Lubuk Basung', 'LBB'),
(58, '3', 'Kabupaten Dharmasraya', 'Pulau Punjung', 'PLJ'),
(59, '3', 'Kabupaten Kepulauan Mentawai', 'Tuapejat', 'TPT'),
(60, '3', 'Kabupaten Lima Puluh Kota', 'Sarilamak', 'SRK'),
(61, '3', 'Kabupaten Padang Pariaman', 'Nagari Parit Malintang', 'NPM'),
(62, '3', 'Kabupaten Pasaman', 'Lubuk Sikaping', 'LBS'),
(63, '3', 'Kabupaten Pasaman Barat', 'Simpang Empat', 'SPE'),
(64, '3', 'Kabupaten Pesisir Selatan', 'Painan', 'PNN'),
(65, '3', 'Kabupaten Sijunjung (Sawah Lunto Sijunjung)', 'Muaro Sijunjung', 'MRJ'),
(66, '3', 'Kabupaten Solok', 'Arosuka', 'ARS'),
(67, '3', 'Kabupaten Solok Selatan', 'Padang Aro', 'PDA'),
(68, '3', 'Kabupaten Tanah Datar', 'Batusangkar', 'BSK'),
(69, '3', 'Kota Bukittinggi', 'Bukittinggi', 'BKT'),
(70, '3', 'Kota Padang', 'Padang', 'PAD'),
(71, '3', 'Kota Padang Panjang', 'Padang Panjang', 'PDP'),
(72, '3', 'Kota Pariaman', 'Pariaman', 'PMN'),
(73, '3', 'Kota Payakumbuh', 'Payakumbuh', 'PYH'),
(74, '3', 'Kota Sawahlunto', 'Sawahlunto', 'SWL'),
(75, '3', 'Kota Solok', 'Solok', 'SLK'),
(76, '4', 'Kabupaten Bengkalis', 'Bengkalis', 'BLS'),
(77, '4', 'Kabupaten Indragiri Hilir', 'Tembilahan', 'TBH'),
(78, '4', 'Kabupaten Indragiri Hulu', 'Rengat', 'RGT'),
(79, '4', 'Kabupaten Kampar', 'Bangkinang', 'BKN'),
(80, '4', 'Kabupaten Kepulauan Meranti', 'Tebing Tinggi', 'TTG'),
(81, '4', 'Kabupaten Kuantan Singingi', 'Teluk Kuantan', 'TLK'),
(82, '4', 'Kabupaten Pelalawan', 'Pangkalan Kerinci', 'PKK'),
(83, '4', 'Kabupaten Rokan Hilir', 'Ujung Tanjung', 'UJT'),
(84, '4', 'Kabupaten Rokan Hulu', 'Pasir Pengarairan', 'PRP'),
(85, '4', 'Kabupaten Siak', 'Siak Sriindrapura', 'SAK'),
(86, '4', 'Kota Dumai', 'Dumai', 'DUM'),
(87, '4', 'Kota Pekanbaru', 'Pekanbaru', 'PBR'),
(88, '5', 'Kabupaten Batanghari', 'Muara Bulian', 'MBN'),
(89, '5', 'Kabupaten Bungo', 'Muara Bungo', 'MRB'),
(90, '5', 'Kabupaten Kerinci', 'Sungai Penuh', 'SPN'),
(91, '5', 'Kabupaten Merangin', 'Bangko', 'BKO'),
(92, '5', 'Kabupaten Muaro Jambi', 'Sengeti', 'SNT'),
(93, '5', 'Kabupaten Sarolangun', 'Sarolangun', 'SRL'),
(94, '5', 'Kabupaten Tanjung Jabung Barat', 'Kuala Tungkal', 'KLT'),
(95, '5', 'Kabupaten Tanjung Jabung Timur', 'Muara Sabak', 'MSK'),
(96, '5', 'Kabupaten Tebo', 'Muara Tebo', 'MRT'),
(97, '5', 'Kota Jambi', 'Jambi', 'JMB'),
(98, '5', 'Kota Sungai Penuh', 'Sungai Penuh', 'SPN'),
(99, '6', 'Kabupaten Banyuasin', 'Pangkalan Balai', 'PKB'),
(100, '6', 'Kabupaten Empat Lawang', 'Tebing Tinggi', 'TBG'),
(101, '6', 'Kabupaten Lahat', 'Lahat', 'LHT'),
(102, '6', 'Kabupaten Muara Enim', 'Muara Enim', 'MRE'),
(103, '6', 'Kabupaten Musi Banyuasin', 'Sekayu', 'SKY'),
(104, '6', 'Kabupaten Musi Rawas', 'Muarabeliti', 'MBL'),
(105, '6', 'Kabupaten Musi Rawas Utara', 'Rupit', 'RPT'),
(106, '6', 'Kabupaten Ogan Ilir', 'Indralaya', 'IDL'),
(107, '6', 'Kabupaten Ogan Komering Ilir', 'Kayu Agung', 'KAG'),
(108, '6', 'Kabupaten Ogan Komering Ulu', 'Baturaja', 'BTA'),
(109, '6', 'Kabupaten Ogan Komering Ulu Selatan (Oku Selatan)', 'Muaradua', 'MRD'),
(110, '6', 'Kabupaten Ogan Komering Ulu Timur (Oku Timur)', 'Martapura', 'MPR'),
(111, '6', 'Kabupaten Penukal Abab Lematang Ilir', 'Talang Ubi', 'TLB'),
(112, '6', 'Kota Lubuk Linggau', 'Lubuk Linggau', 'LLG'),
(113, '6', 'Kota Pagar Alam', 'Pagar Alam', 'PGA'),
(114, '6', 'Kota Palembang', 'Pelembang', 'PLG'),
(115, '6', 'Kota Prabumulih', 'Prabumulih', 'PBM'),
(116, '7', 'Kabupaten Bengkulu Selatan', 'Manna', 'MNA'),
(117, '7', 'Kabupaten Bengkulu Tengah', 'Karang Tinggi', 'KRT'),
(118, '7', 'Kabupaten Bengkulu Utara', 'Arga Makmur', 'AGM'),
(119, '7', 'Kabupaten Kaur', 'Bintuhan', 'BHN'),
(120, '7', 'Kabupaten Kepahiang', 'Kepahiang', 'KPH'),
(121, '7', 'Kabupaten Lebong', 'Tubei', 'TUB'),
(122, '7', 'Kabupaten Muko Muko', 'Mukomuko', 'MKM'),
(123, '7', 'Kabupaten Rejang Lebong', 'Curup', 'CRP'),
(124, '7', 'Kabupaten Seluma', 'Tais', 'TAS'),
(125, '7', 'Kota Bengkulu', 'Bengkulu', 'BGL'),
(126, '8', 'Kabupaten Lampung Barat', 'Liwa', 'LIW'),
(127, '8', 'Kabupaten Lampung Selatan', 'Kalianda', 'KLA'),
(128, '8', 'Kabupaten Lampung Tengah', 'Gunung Sugih', 'GNS'),
(129, '8', 'Kabupaten Lampung Timur', 'Sukadana', 'SDN'),
(130, '8', 'Kabupaten Lampung Utara', 'Kotabumi', 'KTB'),
(131, '8', 'Kabupaten Mesuji', 'Mesuji', 'MSJ'),
(132, '8', 'Kabupaten Pesawaran', 'Gedong Tataan', 'GDT'),
(133, '8', 'Kabupaten Pesisir Barat', 'Krui', 'KRU'),
(134, '8', 'Kabupaten Pringsewu', 'Pringsewu', 'PRW'),
(135, '8', 'Kabupaten Tanggamus', 'Kota Agung', 'KOT'),
(136, '8', 'Kabupaten Tulang Bawang', 'Menggala', 'MGL'),
(137, '8', 'Kabupaten Tulang Bawang Barat', 'Tulang Bawang Tengah', 'TWG'),
(138, '8', 'Kabupaten Way Kanan', 'Blambangan Umpu', 'BBU'),
(139, '8', 'Kota Bandar Lampung', 'Bandar Lampung', 'BDL'),
(140, '8', 'Kota Metro', 'Metro', 'MET'),
(141, '9', 'Kabupaten Bangka', 'Sungai Liat', 'SGL'),
(142, '9', 'Kabupaten Bangka Barat', 'Mentok', 'MTK'),
(143, '9', 'Kabupaten Bangka Selatan', 'Toboali', 'TBL'),
(144, '9', 'Kabupaten Bangka Tengah', 'Koba', 'KBA'),
(145, '9', 'Kabupaten Belitung', 'Tanjung Pandan', 'TDN'),
(146, '9', 'Kabupaten Belitung Timur', 'Manggar', 'MGR'),
(147, '9', 'Kota Pangkal Pinang', 'Pangkal Pinang', 'PGP'),
(148, '10', 'Kabupaten Bintan', 'Bandar Seri Bentan', 'BSB'),
(149, '10', 'Kabupaten Karimun', 'Tanjung Balai Karimun', 'TBK'),
(150, '10', 'Kabupaten Kepulauan Anambas', 'Tarempa', 'TRP'),
(151, '10', 'Kabupaten Lingga', 'Daik Lingga', 'DKL'),
(152, '10', 'Kabupaten Natuna', 'Ranai', 'RAN'),
(153, '10', 'Kota Batam', 'Batam', 'BTM'),
(154, '10', 'Kota Tanjung Pinang', 'Tanjung Pinang', 'TPG'),
(155, '11', 'Kabupaten Adm. Kepulauan Seribu', 'Kepulauan Seribu Utara', 'KSU'),
(156, '11', 'Kota Adm. Jakarta Barat', 'Grogol Petamburan', 'GGP'),
(157, '11', 'Kota Adm. Jakarta Pusat', 'Tanah Abang', 'TNA'),
(158, '11', 'Kota Adm. Jakarta Selatan', 'Kebayoran Baru', 'KYB'),
(159, '11', 'Kota Adm. Jakarta Timur', 'Cakung', 'CKG'),
(160, '11', 'Kota Adm. Jakarta Utara', 'Tanjung Priok', 'TJP'),
(161, '12', 'Kabupaten Bandung', 'Soreang', 'SOR'),
(162, '12', 'Kabupaten Bandung Barat', 'Ngamprah', 'NPH'),
(163, '12', 'Kabupaten Bekasi', 'Cikarang', 'CKR'),
(164, '12', 'Kabupaten Bogor', 'Cibinong', 'CBI'),
(165, '12', 'Kabupaten Ciamis', 'Ciamis', 'CMS'),
(166, '12', 'Kabupaten Cianjur', 'Cianjur', 'CJR'),
(167, '12', 'Kabupaten Cirebon', 'Sumber', 'SBR'),
(168, '12', 'Kabupaten Garut', 'Garut', 'GRT'),
(169, '12', 'Kabupaten Indramayu', 'Indramayu', 'IDM'),
(170, '12', 'Kabupaten Karawang', 'Karawang', 'KWG'),
(171, '12', 'Kabupaten Kuningan', 'Kuningan', 'KNG'),
(172, '12', 'Kabupaten Majalengka', 'Majalengka', 'MJL'),
(173, '12', 'Kabupaten Pangandaran', 'Parigi', 'PAG'),
(174, '12', 'Kabupaten Purwakarta', 'Purwakarta', 'PWK'),
(175, '12', 'Kabupaten Subang', 'Subang', 'SNG'),
(176, '12', 'Kabupaten Sukabumi', 'Sukabumi', 'SBM'),
(177, '12', 'Kabupaten Sumedang', 'Sumedang', 'SMD'),
(178, '12', 'Kabupaten Tasikmalaya', 'Singaparna', 'SPA'),
(179, '12', 'Kota Bandung', 'Bandung', 'BDG'),
(180, '12', 'Kota Banjar', 'Banjar', 'BJR'),
(181, '12', 'Kota Bekasi', 'Bekasi', 'BKS'),
(182, '12', 'Kota Bogor', 'Bogor', 'BGR'),
(183, '12', 'Kota Cimahi', 'Cimahi', 'CMH'),
(184, '12', 'Kota Cirebon', 'Cirebon', 'CBN'),
(185, '12', 'Kota Depok', 'Depok', 'DPK'),
(186, '12', 'Kota Sukabumi', 'Sukabumi', 'SKB'),
(187, '12', 'Kota Tasikmalaya', 'Tasikmalaya', 'TSM'),
(188, '13', 'Kabupaten Banjarnegara', 'Banjarnegara', 'BNR'),
(189, '13', 'Kabupaten Banyumas', 'Purwokerto', 'PWT'),
(190, '13', 'Kabupaten Batang', 'Batang', 'BTG'),
(191, '13', 'Kabupaten Blora', 'Blora', 'BLA'),
(192, '13', 'Kabupaten Boyolali', 'Boyolali', 'BYL'),
(193, '13', 'Kabupaten Brebes', 'Brebes', 'BBS'),
(194, '13', 'Kabupaten Cilacap', 'Cilacap', 'CLP'),
(195, '13', 'Kabupaten Demak', 'Demak', 'DMK'),
(196, '13', 'Kabupaten Grobogan', 'Purwodadi', 'PWD'),
(197, '13', 'Kabupaten Jepara', 'Jepara', 'JPA'),
(198, '13', 'Kabupaten Karanganyar', 'Karanganyar', 'KRG'),
(199, '13', 'Kabupaten Kebumen', 'Kebumen', 'KBM'),
(200, '13', 'Kabupaten Kendal', 'Kendal', 'KDL'),
(201, '13', 'Kabupaten Klaten', 'Klaten', 'KLN'),
(202, '13', 'Kabupaten Kudus', 'Kudus', 'KDS'),
(203, '13', 'Kabupaten Magelang', 'Mungkid', 'MKD'),
(204, '13', 'Kabupaten Pati', 'Pati', 'PTI'),
(205, '13', 'Kabupaten Pekalongan', 'Kajen', 'KJN'),
(206, '13', 'Kabupaten Pemalang', 'Pemalang', 'PML'),
(207, '13', 'Kabupaten Purbalingga', 'Purbalingga', 'PBG'),
(208, '13', 'Kabupaten Purworejo', 'Purworejo', 'PWR'),
(209, '13', 'Kabupaten Rembang', 'Rembang', 'RBG'),
(210, '13', 'Kabupaten Semarang', 'Ungaran', 'UNR'),
(211, '13', 'Kabupaten Sragen', 'Sragen', 'SGN'),
(212, '13', 'Kabupaten Sukoharjo', 'Sukoharjo', 'SKH'),
(213, '13', 'Kabupaten Tegal', 'Slawi', 'SLW'),
(214, '13', 'Kabupaten Temanggung', 'Temanggung', 'TMG'),
(215, '13', 'Kabupaten Wonogiri', 'Wonogiri', 'WNG'),
(216, '13', 'Kabupaten Wonosobo', 'Wonosobo', 'WSB'),
(217, '13', 'Kota Magelang', 'Magelang', 'MGG'),
(218, '13', 'Kota Pekalongan', 'Pekalongan', 'PKL'),
(219, '13', 'Kota Salatiga', 'Salatiga', 'SLT'),
(220, '13', 'Kota Semarang', 'Semarang', 'SMG'),
(221, '13', 'Kota Surakarta (Solo)', 'Surakarta', 'SKT'),
(222, '13', 'Kota Tegal', 'Tegal', 'TGL'),
(223, '14', 'Kabupaten Bantul', 'Bantul', 'BTL'),
(224, '14', 'Kabupaten Gunung Kidul', 'Wonosari', 'WNO'),
(225, '14', 'Kabupaten Kulon Progo', 'Wates', 'WAT'),
(226, '14', 'Kabupaten Sleman', 'Sleman', 'SMN'),
(227, '14', 'Kota Yogyakarta', 'Yogyakarta', 'YYK'),
(228, '15', 'Kabupaten Bangkalan', 'Bangkalan', 'BKL'),
(229, '15', 'Kabupaten Banyuwangi', 'Banyuwangi', 'BYW'),
(230, '15', 'Kabupaten Blitar', 'Kanigoro', 'KNR'),
(231, '15', 'Kabupaten Bojonegoro', 'Bojonegoro', 'BJN'),
(232, '15', 'Kabupaten Bondowoso', 'Bondowoso', 'BDW'),
(233, '15', 'Kabupaten Gresik', 'Gresik', 'GSK'),
(234, '15', 'Kabupaten Jember', 'Jember', 'JMR'),
(235, '15', 'Kabupaten Jombang', 'Jombang', 'JBG'),
(236, '15', 'Kabupaten Kediri', 'Kediri', 'KDR'),
(237, '15', 'Kabupaten Lamongan', 'Lamongan', 'LMG'),
(238, '15', 'Kabupaten Lumajang', 'Lumajang', 'LMJ'),
(239, '15', 'Kabupaten Madiun', 'Mejayan', 'MJY'),
(240, '15', 'Kabupaten Magetan', 'Magetan', 'MGT'),
(241, '15', 'Kabupaten Malang', 'Kepanjen', 'KPN'),
(242, '15', 'Kabupaten Mojokerto', 'Mojokerto', 'MJK'),
(243, '15', 'Kabupaten Nganjuk', 'Nganjuk', 'NJK'),
(244, '15', 'Kabupaten Ngawi', 'Ngawi', 'NGW'),
(245, '15', 'Kabupaten Pacitan', 'Pacitan', 'PCT'),
(246, '15', 'Kabupaten Pamekasan', 'Pamekasan', 'PMK'),
(247, '15', 'Kabupaten Pasuruan', 'Pasuruan', 'PSR'),
(248, '15', 'Kabupaten Ponorogo', 'Ponorogo', 'PNG'),
(249, '15', 'Kabupaten Probolinggo', 'Kraksaan', 'KRS'),
(250, '15', 'Kabupaten Sampang', 'Sampang', 'SPG'),
(251, '15', 'Kabupaten Sidoarjo', 'Sidoarjo', 'SDA'),
(252, '15', 'Kabupaten Situbondo', 'Situbondo', 'SIT'),
(253, '15', 'Kabupaten Sumenep', 'Sumenep', 'SMP'),
(254, '15', 'Kabupaten Trenggalek', 'Trenggalek', 'TRK'),
(255, '15', 'Kabupaten Tuban', 'Tuban', 'TBN'),
(256, '15', 'Kabupaten Tulungagung', 'Tulungagung', 'TLG'),
(257, '15', 'Kota Batu', 'Batu', 'BTU'),
(258, '15', 'Kota Blitar', 'Blitar', 'BLT'),
(259, '15', 'Kota Kediri', 'Kediri', 'KDR'),
(260, '15', 'Kota Madiun', 'Madiun', 'MAD'),
(261, '15', 'Kota Malang', 'Malang', 'MLG'),
(262, '15', 'Kota Mojokerto', 'Mojokerto', 'MJK'),
(263, '15', 'Kota Pasuruan', 'Pasuruan', 'PSN'),
(264, '15', 'Kota Probolinggo', 'Probolinggo', 'PBL'),
(265, '15', 'Kota Surabaya', 'Surabaya', 'SBY'),
(266, '16', 'Kabupaten Lebak', 'Rangkas Bitung', 'RKB'),
(267, '16', 'Kabupaten Pandeglang', 'Pandeglang', 'PDG'),
(268, '16', 'Kabupaten Serang', 'Serang', 'SRG'),
(269, '16', 'Kabupaten Tangerang', 'Tigaraksa', 'TGR'),
(270, '16', 'Kota Cilegon', 'Cilegon', 'CLG'),
(271, '16', 'Kota Serang', 'Serang', 'SRG'),
(272, '16', 'Kota Tangerang', 'Tangerang', 'TNG'),
(273, '16', 'Kota Tangerang Selatan', 'Ciputat', 'CPT'),
(274, '17', 'Kabupaten Badung', 'Mengwi', 'MGW'),
(275, '17', 'Kabupaten Bangli', 'Bangli', 'BLI'),
(276, '17', 'Kabupaten Buleleng', 'Singaraja', 'SGR'),
(277, '17', 'Kabupaten Gianyar', 'Gianyar', 'GIN'),
(278, '17', 'Kabupaten Jembrana', 'Negara', 'NGA'),
(279, '17', 'Kabupaten Karangasem', 'Karangasem', 'KRA'),
(280, '17', 'Kabupaten Klungkung', 'Semarapura', 'SRP'),
(281, '17', 'Kabupaten Tabanan', 'Tabanan', 'TAB'),
(282, '17', 'Kota Denpasar', 'Denpasar', 'DPR'),
(283, '18', 'Kabupaten Bima', 'Woha', 'WHO'),
(284, '18', 'Kabupaten Dompu', 'Dompu', 'DPU'),
(285, '18', 'Kabupaten Lombok Barat', 'Gerung', 'GRG'),
(286, '18', 'Kabupaten Lombok Tengah', 'Praya', 'PYA'),
(287, '18', 'Kabupaten Lombok Timur', 'Selong', 'SEL'),
(288, '18', 'Kabupaten Lombok Utara', 'Tanjung', 'TJN'),
(289, '18', 'Kabupaten Sumbawa', 'Sumbawa Besar', 'SBW'),
(290, '18', 'Kabupaten Sumbawa Barat', 'Taliwang', 'TLW'),
(291, '18', 'Kota Bima', 'Bima', 'BIM'),
(292, '18', 'Kota Mataram', 'Mataram', 'MTR'),
(293, '19', 'Kabupaten Alor', 'Kalabahi', 'KLB'),
(294, '19', 'Kabupaten Belu', 'Atambua', 'ATB'),
(295, '19', 'Kabupaten Ende', 'Ende', 'END'),
(296, '19', 'Kabupaten Flores Timur', 'Larantuka', 'LRT'),
(297, '19', 'Kabupaten Kupang', 'Kupang', 'KPG'),
(298, '19', 'Kabupaten Lembata', 'Lewoleba', 'LWB'),
(299, '19', 'Kabupaten Malaka', 'Betun', 'BTN'),
(300, '19', 'Kabupaten Manggarai', 'Ruteng', 'RTG'),
(301, '19', 'Kabupaten Manggarai Barat', 'Labuan Bajo', 'LBJ'),
(302, '19', 'Kabupaten Manggarai Timur', 'Borong', 'BRG'),
(303, '19', 'Kabupaten Nagekeo', 'Mbay', 'MBY'),
(304, '19', 'Kabupaten Ngada', 'Bajawa', 'BJW'),
(305, '19', 'Kabupaten Rote Ndao', 'Baa', 'BAA'),
(306, '19', 'Kabupaten Sabu Raijua', 'Sabu Barat', 'SBB'),
(307, '19', 'Kabupaten Sikka', 'Maumere', 'MME'),
(308, '19', 'Kabupaten Sumba Barat', 'Waikabubak', 'WKB'),
(309, '19', 'Kabupaten Sumba Barat Daya', 'Tambolaka', 'TAM'),
(310, '19', 'Kabupaten Sumba Tengah', 'Waibakul', 'WBL'),
(311, '19', 'Kabupaten Sumba Timur', 'Waingapu', 'WGP'),
(312, '19', 'Kabupaten Timor Tengah Selatan', 'Soe', 'SOE'),
(313, '19', 'Kabupaten Timor Tengah Utara', 'Kefamenanu', 'KFM'),
(314, '19', 'Kota Kupang', 'Kupang', 'KPG'),
(315, '20', 'Kabupaten Bengkayang', 'Bengkayang', 'BEK'),
(316, '20', 'Kabupaten Kapuas Hulu', 'Putussibau', 'PTS'),
(317, '20', 'Kabupaten Kayong Utara', 'Sukadane', 'SKD'),
(318, '20', 'Kabupaten Ketapang', 'Ketapang', 'KTP'),
(319, '20', 'Kabupaten Kubu Raya', 'Sungai Raya', 'SRY'),
(320, '20', 'Kabupaten Landak', 'Ngabang', 'NBA'),
(321, '20', 'Kabupaten Melawi', 'Nanga Pinoh', 'NGP'),
(322, '20', 'Kabupaten Mempawah', 'Mempawah', 'MPW'),
(323, '20', 'Kabupaten Sambas', 'Sambas', 'SBS'),
(324, '20', 'Kabupaten Sanggau', 'Sanggau', 'SAG'),
(325, '20', 'Kabupaten Sekadau', 'Sekadau', 'SED'),
(326, '20', 'Kabupaten Sintang', 'Sintang', 'STG'),
(327, '20', 'Kota Pontianak', 'Pontianak', 'PTK'),
(328, '20', 'Kota Singkawang', 'Singkawang', 'SKW'),
(329, '21', 'Kabupaten Barito Selatan', 'Buntok', 'BNT'),
(330, '21', 'Kabupaten Barito Timur', 'Tamiang Layang', 'TML'),
(331, '21', 'Kabupaten Barito Utara', 'Muara Teweh', 'MTW'),
(332, '21', 'Kabupaten Gunung Mas', 'Kuala Kurun', 'KKN'),
(333, '21', 'Kabupaten Kapuas', 'Kuala Kapuas', 'KLK'),
(334, '21', 'Kabupaten Katingan', 'Kasongan', 'KSN'),
(335, '21', 'Kabupaten Kotawaringin Barat', 'Pangkalan Bun', 'PBU'),
(336, '21', 'Kabupaten Kotawaringin Timur', 'Sampit', 'SPT'),
(337, '21', 'Kabupaten Lamandau', 'Nanga Bulik', 'NGB'),
(338, '21', 'Kabupaten Murung Raya', 'Puruk Cahu', 'PRC'),
(339, '21', 'Kabupaten Pulang Pisau', 'Pulang Pisau', 'PPS'),
(340, '21', 'Kabupaten Seruyan', 'Kuala Pembuang', 'KLP'),
(341, '21', 'Kabupaten Sukamara', 'Sukamara', 'SKR'),
(342, '21', 'Kota Palangka Raya', 'Palangkaraya', 'PLK'),
(343, '22', 'Kabupaten Balangan', 'Paringin', 'PRN'),
(344, '22', 'Kabupaten Banjar', 'Martapura', 'MTP'),
(345, '22', 'Kabupaten Barito Kuala', 'Marabahan', 'MRH'),
(346, '22', 'Kabupaten Hulu Sungai Selatan', 'Kandangan', 'KGN'),
(347, '22', 'Kabupaten Hulu Sungai Tengah', 'Barabai', 'BRB'),
(348, '22', 'Kabupaten Hulu Sungai Utara', 'Amuntai', 'AMT'),
(349, '22', 'Kabupaten Kotabaru', 'Kotabaru', 'KBR'),
(350, '22', 'Kabupaten Tabalong', 'Tanjung', 'TJG'),
(351, '22', 'Kabupaten Tanah Bumbu', 'Batulicin', 'BLN'),
(352, '22', 'Kabupaten Tanah Laut', 'Pelaihari', 'PLI'),
(353, '22', 'Kabupaten Tapin', 'Rantau', 'RTA'),
(354, '22', 'Kota Banjarbaru', 'Banjarbaru', 'BJB'),
(355, '22', 'Kota Banjarmasin', 'Banjarmasin', 'BJM'),
(356, '23', 'Kabupaten Berau', 'Tanjung Redeb', 'TNR'),
(357, '23', 'Kabupaten Kutai Barat', 'Sendawar', 'SDW'),
(358, '23', 'Kabupaten Kutai Kartanegara', 'Tenggarong', 'TRG'),
(359, '23', 'Kabupaten Kutai Timur', 'Sanggatta', 'SGT'),
(360, '23', 'Kabupaten Mahakam Ulu', 'Ujoh Bilang', 'UJB'),
(361, '23', 'Kabupaten Paser', 'Tanah Grogot', 'TGT'),
(362, '23', 'Kabupaten Penajam Paser Utara', 'Penajam', 'PNJ'),
(363, '23', 'Kota Balikpapan', 'Balikpapan', 'BPP'),
(364, '23', 'Kota Bontang', 'Bontang', 'BON'),
(365, '23', 'Kota Samarinda', 'Samarinda', 'SMR'),
(366, '24', 'Kabupaten Bulungan (Bulongan)', 'Tanjung Selor', 'TJS'),
(367, '24', 'Kabupaten Malinau', 'Malinau', 'MLN'),
(368, '24', 'Kabupaten Nunukan', 'Nunukan', 'NNK'),
(369, '24', 'Kabupaten Tana Tidung', 'Tideng Pale', 'TDP'),
(370, '24', 'Kota Tarakan', 'Tarakan', 'TAR'),
(371, '25', 'Kabupaten Bolaang Mongondow', 'Lolak', 'LLK'),
(372, '25', 'Kabupaten Bolaang Mongondow Selatan', 'Bolaang Uki', 'BLU'),
(373, '25', 'Kabupaten Bolaang Mongondow Timur', 'Tutuyan', 'TTY'),
(374, '25', 'Kabupaten Bolaang Mongondow Utara', 'Boroko', 'BRK'),
(375, '25', 'Kabupaten Kepulauan Sangihe', 'Tahuna', 'THN'),
(377, '25', 'Kabupaten Kepulauan Talaud', 'Melongguane', 'MGN'),
(378, '25', 'Kabupaten Minahasa', 'Tondano', 'TNN'),
(379, '25', 'Kabupaten Minahasa Selatan', 'Amurang', 'AMR'),
(380, '25', 'Kabupaten Minahasa Tenggara', 'Ratahan', 'RTN'),
(381, '25', 'Kabupaten Minahasa Utara', 'Air Madidi', 'ARM'),
(382, '25', 'Kota Bitung', 'Bitung', 'BIT'),
(383, '25', 'Kota Kotamobagu', 'Kotamobagu', 'KTG'),
(384, '25', 'Kota Manado', 'Manado', 'MND'),
(385, '25', 'Kota Tomohon', 'Tomohon', 'TMH'),
(386, '26', 'Kabupaten Banggai', 'Luwuk', 'LWK'),
(387, '26', 'Kabupaten Banggai Kepulauan', 'Salakan', 'SKN'),
(388, '26', 'Kabupaten Banggai Laut', 'Banggai', 'BGI'),
(389, '26', 'Kabupaten Buol', 'Buol', 'BUL'),
(390, '26', 'Kabupaten Donggala', 'Donggala', 'DGL'),
(391, '26', 'Kabupaten Morowali', 'Bungku', 'BGK'),
(392, '26', 'Kabupaten Morowali Utara', 'Kolonodale', 'KND'),
(393, '26', 'Kabupaten Parigi Moutong', 'Parigi', 'PRG'),
(394, '26', 'Kabupaten Poso', 'Poso', 'PSO'),
(395, '26', 'Kabupaten Sigi', 'Sigi Biromaru', 'SGB'),
(396, '26', 'Kabupaten Tojo Una-Una', 'Ampana', 'APN'),
(397, '26', 'Kabupaten Toli-Toli', 'Toli Toli', 'TLI'),
(398, '26', 'Kota Palu', 'Palu', 'PAL'),
(399, '27', 'Kabupaten Bantaeng', 'Bantaeng', 'BAN'),
(400, '27', 'Kabupaten Barru', 'Barru', 'BAR'),
(401, '27', 'Kabupaten Bone', 'Watampone', 'WTP'),
(402, '27', 'Kabupaten Bulukumba', 'Bulukumba', 'BLK'),
(403, '27', 'Kabupaten Enrekang', 'Enrekang', 'ENR'),
(404, '27', 'Kabupaten Gowa', 'Sungguminasa', 'SGM'),
(405, '27', 'Kabupaten Jeneponto', 'Jeneponto', 'JNP'),
(406, '27', 'Kabupaten Selayar (Kepulauan Selayar)', 'Benteng', 'BEN'),
(407, '27', 'Kabupaten Luwu', 'Palopo', 'PLP'),
(408, '27', 'Kabupaten Luwu Timur', 'Malili', 'MLL'),
(409, '27', 'Kabupaten Luwu Utara', 'Masamba', 'MSB'),
(410, '27', 'Kabupaten Maros', 'Maros', 'MRS'),
(411, '27', 'Kabupaten Pangkajene Kepulauan', 'Pangkajene', 'PKJ'),
(412, '27', 'Kabupaten Pinrang', 'Pinrang', 'PIN'),
(413, '27', 'Kabupaten Sidenreng Rappang (Sidrap)', 'Sidenreng', 'SDR'),
(414, '27', 'Kabupaten Sinjai', 'Sinjai', 'SNJ'),
(415, '27', 'Kabupaten Soppeng', 'Watan Soppeng', 'WNS'),
(416, '27', 'Kabupaten Takalar', 'Takalar', 'TKA'),
(417, '27', 'Kabupaten Tana Toraja', 'Makale', 'MAK'),
(418, '27', 'Kabupaten Toraja Utara', 'Rantepao', 'RTP'),
(419, '27', 'Kabupaten Wajo', 'Sengkang', 'SKG'),
(420, '27', 'Kota Makassar', 'Makassar', 'MKS'),
(421, '27', 'Kota Palopo', 'Palopo', 'PLP'),
(422, '27', 'Kota Parepare', 'Pare Pare', 'PRE'),
(423, '28', 'Kabupaten Bombana', 'Rumbia', 'RMB'),
(424, '28', 'Kabupaten Buton', 'Pasar Wajo', 'PSW'),
(425, '28', 'Kabupaten Buton Selatan', 'Batauga', 'BAG'),
(426, '28', 'Kabupaten Buton Tengah', 'Labungkari', 'LBK'),
(427, '28', 'Kabupaten Buton Utara', 'Buranga', 'BNG'),
(428, '28', 'Kabupaten Kolaka', 'Kolaka', 'KKA'),
(429, '28', 'Kabupaten Kolaka Timur', 'Tirawuta', 'TRW'),
(430, '28', 'Kabupaten Kolaka Utara', 'Lasusua', 'LSS'),
(431, '28', 'Kabupaten Konawe', 'Unaaha', 'UNH'),
(432, '28', 'Kabupaten Konawe Kepulauan', 'Langara', 'LGR'),
(433, '28', 'Kabupaten Konawe Selatan', 'Andoolo', 'ADL'),
(434, '28', 'Kabupaten Konawe Utara', 'Wanggudu', 'WGD'),
(435, '28', 'Kabupaten Muna', 'Raha', 'RAH'),
(436, '28', 'Kabupaten Muna Barat', 'Sawerigadi', 'SWG'),
(437, '28', 'Kabupaten Wakatobi', 'Wangi Wangi', 'WGW'),
(438, '28', 'Kota Baubau', 'Bau-Bau', 'BAU'),
(439, '28', 'Kota Kendari', 'Kendari', 'KDI'),
(440, '29', 'Kabupaten Boalemo', 'Tilamuta', 'TMT'),
(441, '29', 'Kabupaten Bone Bolango', 'Suwawa', 'SWW'),
(442, '29', 'Kabupaten Gorontalo', 'Limboto', 'LBT'),
(443, '29', 'Kabupaten Gorontalo Utara', 'Kwandang', 'KWD'),
(444, '29', 'Kabupaten Pohuwato', 'Marisa', 'MAR'),
(445, '29', 'Kota Gorontalo', 'Gorontalo', 'GTO'),
(446, '30', 'Kabupaten Majene', 'Majene', 'MJN'),
(447, '30', 'Kabupaten Mamasa', 'Mamasa', 'MMS'),
(448, '30', 'Kabupaten Mamuju', 'Mamuju', 'MAM'),
(449, '30', 'Kabupaten Mamuju Tengah', 'Tobadak', 'TBD'),
(450, '30', 'Kabupaten Mamuju Utara', 'Pasangkayu', 'PKY'),
(451, '30', 'Kabupaten Polewali Mandar', 'Polewali', 'PLW'),
(452, '31', 'Kabupaten Buru', 'Namlea', 'NLA'),
(453, '31', 'Kabupaten Buru Selatan', 'Namrole', 'NMR'),
(454, '31', 'Kabupaten Kepulauan Aru', 'Dobo', 'DOB'),
(455, '31', 'Kabupaten Maluku Barat Daya', 'Tiakur', 'TKR'),
(456, '31', 'Kabupaten Maluku Tengah', 'Masohi', 'MSH'),
(457, '31', 'Kabupaten Maluku Tenggara', 'Tual', 'TUL'),
(458, '31', 'Kabupaten Maluku Tenggara Barat', 'Saumlaki', 'SML'),
(459, '31', 'Kabupaten Seram Bagian Barat', 'Dataran Hunipopu', 'DRH'),
(460, '31', 'Kabupaten Seram Bagian Timur', 'Dataran Hunimoa', 'DTH'),
(461, '31', 'Kota Ambon', 'Ambon', 'AMB'),
(462, '31', 'Kota Tual', 'Tual', 'TUL'),
(463, '32', 'Kabupaten Halmahera Barat', 'Jailolo', 'JLL'),
(464, '32', 'Kabupaten Halmahera Selatan', 'Labuha', 'LBA'),
(465, '32', 'Kabupaten Halmahera Tengah', 'Weda', 'WED'),
(466, '32', 'Kabupaten Halmahera Timur', 'Maba', 'MAB'),
(467, '32', 'Kabupaten Halmahera Utara', 'Tobelo', 'TOB'),
(468, '32', 'Kabupaten Kepulauan Sula', 'Sanana', 'SNN'),
(469, '32', 'Kabupaten Pulau Morotai', 'Daruba', 'DRB'),
(470, '32', 'Kabupaten Pulau Taliabu', 'Bobong', 'BOB'),
(471, '32', 'Kota Ternate', 'Ternate', 'TTE'),
(472, '32', 'Kota Tidore Kepulauan', 'Tidore', 'TDR'),
(473, '33', 'Kabupaten Asmat', 'Agats', 'AGT'),
(474, '33', 'Kabupaten Biak Numfor', 'Biak', 'BIK'),
(475, '33', 'Kabupaten Boven Digoel', 'Tanah Merah', 'TMR'),
(476, '33', 'Kabupaten Deiyai (Deliyai)', 'Tigi', 'TIG'),
(477, '33', 'Kabupaten Dogiyai', 'Kigamani', 'KGM'),
(478, '33', 'Kabupaten Intan Jaya', 'Sugapa', 'SGP'),
(479, '33', 'Kabupaten Jayapura', 'Jayapura', 'JAP'),
(480, '33', 'Kabupaten Jayawijaya', 'Wamena', 'WAM'),
(481, '33', 'Kabupaten Keerom', 'Waris', 'WRS'),
(482, '33', 'Kabupaten Kepulauan Yapen (Yapen Waropen)', 'Serui', 'SRU'),
(483, '33', 'Kabupaten Lanny Jaya', 'Tiom', 'TOM'),
(484, '33', 'Kabupaten Mamberamo Raya', 'Burmeso', 'BRM'),
(485, '33', 'Kabupaten Mamberamo Tengah', 'Kobakma', 'KBK'),
(486, '33', 'Kabupaten Mappi', 'Kepi', 'KEP'),
(487, '33', 'Kabupaten Merauke', 'Merauke', 'MRK'),
(488, '33', 'Kabupaten Mimika', 'Timika', 'TIM'),
(489, '33', 'Kabupaten Nabire', 'Nabire', 'NAB'),
(490, '33', 'Kabupaten Nduga', 'Kenyam', 'KYM'),
(491, '33', 'Kabupaten Paniai', 'Enarotali', 'ERT'),
(492, '33', 'Kabupaten Pegunungan Bintang', 'Oksibil', 'OSB'),
(493, '33', 'Kabupaten Puncak', 'Ilaga', 'ILG'),
(494, '33', 'Kabupaten Puncak Jaya', 'Mulia', 'MUL'),
(495, '33', 'Kabupaten Sarmi', 'Sarmi', 'SMI'),
(496, '33', 'Kabupaten Supiori', 'Sorendiweri', 'SRW'),
(497, '33', 'Kabupaten Tolikara', 'Karubaga', 'KBG'),
(498, '33', 'Kabupaten Waropen', 'Botawa', 'BTW'),
(499, '33', 'Kabupaten Yahukimo', 'Sumohai', 'SMH'),
(500, '33', 'Kabupaten Yalimo', 'Elelim', 'ELL'),
(501, '33', 'Kota Jayapura', 'Jayapura', 'JAP'),
(502, '34', 'Kabupaten Fakfak', 'Fak Fak', 'FFK'),
(503, '34', 'Kabupaten Kaimana', 'Kaimana', 'KMN'),
(504, '34', 'Kabupaten Manokwari', 'Manokwari', 'MNK'),
(505, '34', 'Kabupaten Manokwari Selatan', 'Ransiki', 'RNK'),
(506, '34', 'Kabupaten Maybrat', 'Aifat', 'AFT'),
(507, '34', 'Kabupaten Pegunungan Arfak', 'Anggi', 'ANG'),
(508, '34', 'Kabupaten Raja Ampat', 'Waisai', 'WAS'),
(509, '34', 'Kabupaten Sorong', 'Aimas', 'AMS'),
(510, '34', 'Kabupaten Sorong Selatan', 'Teminabuan', 'TMB'),
(511, '34', 'Kabupaten Tambrauw', 'Fef', 'FEF'),
(512, '34', 'Kabupaten Teluk Bintuni', 'Bintuni', 'BTI'),
(513, '34', 'Kabupaten Teluk Wondama', 'Rasiei', 'RAS'),
(514, '34', 'Kota Sorong', 'Sorong', 'SON');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_lab`
--

CREATE TABLE `master_lab` (
  `id` bigint UNSIGNED NOT NULL,
  `id_vendor` int NOT NULL,
  `pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_rujukan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_leave`
--

CREATE TABLE `master_leave` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_leave`
--

INSERT INTO `master_leave` (`id`, `nama`, `badge`, `created_at`, `updated_at`) VALUES
(1, 'National Collective Leave', 'bg-soft-warning border-warning', '2023-12-06 01:34:40', '2024-11-28 07:33:07'),
(2, 'National holiday', 'bg-soft-danger border-danger', '2023-12-06 01:36:27', '2023-12-06 01:36:27'),
(3, 'Company Collective Leave', 'bg-soft-info border-info', '2023-12-06 01:36:49', '2024-11-28 07:33:23'),
(4, 'Company Event', 'bg-soft-success border-success', '2024-01-03 06:26:27', '2024-01-03 06:26:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_level`
--

CREATE TABLE `master_level` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_level`
--

INSERT INTO `master_level` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'OPERATOR', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(2, 'MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(3, 'ASSISTANT MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(4, 'KARU/KASI', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(5, 'GROUP LEADER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(6, 'JUNIOR MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(7, 'SENIOR GENERAL MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(8, 'STAFF', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(9, 'ADMIN', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(10, 'GENERAL MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(11, 'CANTEEN', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(12, 'AREA MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(13, 'SALES REPRESENTATIVE', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(14, 'SECTION CHIEF', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(15, 'SALES SUPERVISOR', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(16, 'ASPO', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(17, 'SENIOR SECRETARY', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(18, 'KAE', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(19, 'TECHNICIAN', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(20, 'JUNIOR DIRECTOR', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(21, 'ASPO ASSISTANT', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(22, 'SENIOR MANAGER', '2023-11-15 08:08:39', '2023-11-15 08:08:39'),
(23, 'BOARD OF DIRECTOR', '2024-10-30 03:47:18', '2024-10-30 03:47:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_line_approval`
--

CREATE TABLE `master_line_approval` (
  `id` bigint UNSIGNED NOT NULL,
  `approval_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `building_id` bigint UNSIGNED DEFAULT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `approve_1` bigint UNSIGNED DEFAULT NULL,
  `approve_2` bigint UNSIGNED DEFAULT NULL,
  `approve_3` bigint UNSIGNED DEFAULT NULL,
  `approve_4` bigint UNSIGNED DEFAULT NULL,
  `approve_5` bigint UNSIGNED DEFAULT NULL,
  `approve_6` bigint UNSIGNED DEFAULT NULL,
  `approve_7` bigint UNSIGNED DEFAULT NULL,
  `approve_8` bigint UNSIGNED DEFAULT NULL,
  `drafter` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_line_approval_employees`
--

CREATE TABLE `master_line_approval_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `line_approval_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_position`
--

CREATE TABLE `master_position` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_position`
--

INSERT INTO `master_position` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'OPERATOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(2, 'ADVISOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(3, 'ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(4, 'KARU/KASI', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(5, 'GROUP LEADER PRODUCTION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(6, 'SALES AREA MANAGER', '2023-11-15 08:06:21', '2024-11-13 08:00:36'),
(7, 'SALES & TRADE MARKETING SENIOR GENERAL MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(8, 'MARKETING LOGISTIC & OFFICE STAFF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(9, 'MARKETING ASSISTANT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(10, 'TECHNICIAN', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(11, 'CANTEEN', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(12, 'ADMIN SALES', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(13, 'SALES REPRESENTATIVE', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(14, 'WAREHOUSE & LOGISTIC ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(15, 'SALES ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(16, 'OFFICE BOY', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(17, 'QUALITY CONTROL ANALYST', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(18, 'PRODUCTION GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(19, 'QUALITY ASSURANCE DOCUMENTATION STAFF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(20, 'SALES SUPERVISOR GT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(21, 'SALES SUPERVISOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(22, 'WAREHOUSE & LOGISTIC OPERATOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(23, 'SALES ASSISTANT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(24, 'WAREHOUSE & LOGISTIC JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(25, 'REGIONAL SALES MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(26, 'ASPO CJ 2', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(27, 'ENGINEERING GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(28, 'PRODUCTION ADMINISTRATION GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(29, 'PRODUCTION ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(30, 'NN', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(31, 'WAREHOUSE & LOGISTIC GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(32, 'PURCHASING ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(33, 'PRODUCT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(34, 'ASPO', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(35, 'ASPO WJ', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(36, 'QUALITY ASSURANCE GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(37, 'ACCOUNTING JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(38, 'ACCOUNTING & TAXATION GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(39, 'ACCOUNTING GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(40, 'ASSISTANT MANAGER WAREHOUSE & LOGISTIC', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(41, 'SALES SUPERVISOR MIX', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(42, 'vacant', '2023-11-15 08:06:21', '2024-11-13 08:00:52'),
(43, 'PRODUCTION ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(44, 'ASSISTANT MANAGER GA', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(45, 'QUALITY GENERAL MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(46, 'ENGINEERING JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(47, 'MARKETING GENERAL MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(48, 'ASPO KAL', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(49, 'QUALITY CONTROL SAMPLING ANALYST', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(50, 'HUMAN RESOURCE DEVELOPMENT JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(51, 'KAIZEN DEVELOPMENT & PDSO JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(52, 'STAFF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(53, 'HR & COMBEN SECTION CHIEF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(54, 'SENIOR SECRETARY & TRANSLATOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(55, 'SECTION CHIEF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(56, 'PRODUCTION JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(57, 'KAE', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(58, 'IT JUNIOR MANAGER', '2023-11-15 08:06:21', '2025-02-04 01:34:12'),
(59, 'KEY ACCOUNT ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(60, 'ASPO EJ 2', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(61, 'DESIGN & CREATIVE EXECUTIVE', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(62, 'JUNIOR PRODUCT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(63, 'GENERAL MANAGER ACCOUNTING & FINANCE', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(64, 'PRODUCTION SECTION CHIEF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(65, 'RESEARCH & DEVELOPMENT ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(66, 'STAFF GA', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(67, 'KEY ACCOUNT ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(68, 'ASPO EJ 1', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(69, 'JUNIOR MANAGER BRAND ACTIVATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(70, 'KAIZEN DEVELOPMENT GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(71, 'ADMIN', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(72, 'ASPO CJ 1', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(73, 'QUALITY CONTROL JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(74, 'STAFF ACC & FNC', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(75, 'ASPO MAKASSAR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(76, 'GA GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(77, 'STAFF HSE', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(78, 'QUALITY CONTROL ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(79, 'ENGINEERING ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(80, 'SALES SUPERVISOR MT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(81, 'HSE ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(82, 'QUALITY ASSURANCE ASSISTANT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(83, 'SENIOR PRODUCT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(84, 'LEGAL COMPLIANCE & ORGANIZATION DEVELOPMENT JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(85, 'KAIZEN PROMOTION GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(86, 'STAFF PURCHASING', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(87, 'SENIOR BUDGET CONTROL OFFICER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(88, 'ASPO ASSISTANT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(89, 'IT GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(90, 'TRADE MARKETING JUNIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(91, 'ASPO MEDAN', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(92, 'ASPO OIWJ 1', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(93, 'GENERAL MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(94, 'SENIOR MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(95, 'QUALITY ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(96, 'FINANCE STAFF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(97, 'PRODUCT DEVELOPMENT SUPPORT', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(98, 'KEY ACCOUNT MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(99, 'ENGINEERING SECTION CHIEF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(100, 'FINANCE GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(101, 'HRD & GA GENERAL MANAGER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(102, 'SECRETARY & TRANSLATOR', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(103, 'HR STAFF', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(104, 'QUALITY CONTROL GROUP LEADER', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(105, 'MEDICAL DEVICE ADMINISTRATION', '2023-11-15 08:06:21', '2023-11-15 08:06:21'),
(106, 'TECHNIC OPERATOR', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(107, 'KEY ACCOUNT EXECUTIVE', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(108, 'SALES SUPERVISOR MT CJ 1', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(109, 'SALES SUPERVISOR MIX WJ', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(110, 'ASPO OIWJ 2', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(111, 'CREATIVE & GRAPHIC DESIGNER', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(112, 'PRODUCTION & RELIABILITY JUNIOR MANAGER', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(113, 'GENERAL MANAGER ACC & FNC', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(114, 'PURCHASING STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(115, 'GA STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(116, 'SALES SUPERVISOR - MT', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(117, 'SALES SUPERVISOR - GT', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(118, 'PRODUCT DEVELOPMENT ANALYST', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(119, 'STAFF HR', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(120, 'SALES SUPERVISOR - MIX', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(121, 'RESEACH & DEVELOPMENT ANALYST', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(122, 'QUALITY CONTROL STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(123, 'UTILITY TECHNICIAN', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(124, 'ACCOUNTING MANAGEMENT JUNIOR MANAGER', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(125, 'ASPO JAKARTA', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(126, 'GA SECTION CHIEF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(127, 'COMPLIANCE STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(128, 'QUALITY ASSURANCE ADMINISTRATION', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(129, 'HSE STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(130, 'TRADE MARKETING STAFF', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(131, 'TECHNICIAN OPERATOR', '2023-11-15 08:06:22', '2023-11-15 08:06:22'),
(132, 'SECURITY', '2024-03-18 02:17:47', '2024-03-18 02:17:47'),
(133, 'OFFICE BOY / GIRL', '2024-03-18 02:17:56', '2024-03-18 02:17:56'),
(134, 'IT SECTION CHIEF', '2024-10-29 06:52:15', '2024-10-29 06:52:15'),
(135, 'PRESIDENT DIRECTOR', '2024-10-30 03:47:01', '2024-10-30 03:47:01'),
(136, 'ASPO OIE', '2024-11-07 08:40:20', '2024-11-07 08:40:20'),
(137, 'ASPO JAKARTA 1', '2024-11-07 08:58:29', '2024-11-07 08:58:29'),
(138, 'IT STAFF', '2025-06-10 11:37:52', '2025-06-10 11:37:52'),
(139, 'REGULATORY MANAGER', '2025-06-10 11:45:29', '2025-06-10 11:45:29'),
(140, 'PRODUCTION DIRECTOR', '2025-08-07 03:47:21', '2026-05-04 10:54:13'),
(141, 'IT Manager', '2026-03-03 10:42:34', '2026-03-03 10:42:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_positioning`
--

CREATE TABLE `master_positioning` (
  `id` bigint UNSIGNED NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `max_distance` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_positioning`
--

INSERT INTO `master_positioning` (`id`, `area`, `latitude`, `longitude`, `max_distance`, `created_at`, `updated_at`) VALUES
(1, '1', -7.4157966, 112.7251519, 20, '2026-06-19 09:49:10', '2026-06-19 09:49:10'),
(3, '12', -7.4158553, 112.7251404, 10, '2026-06-19 09:57:29', '2026-06-19 10:07:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_provinsi`
--

CREATE TABLE `master_provinsi` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `ibukota` varchar(50) NOT NULL,
  `kode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `master_provinsi`
--

INSERT INTO `master_provinsi` (`id`, `nama`, `ibukota`, `kode`) VALUES
(1, 'Aceh', 'Banda Aceh', 'ID-AC'),
(2, 'Sumatra Utara', 'Medan', 'ID-SU'),
(3, 'Sumatra Barat', 'Padang', 'ID-SB'),
(4, 'Riau', 'Pekanbaru', 'ID-RI'),
(5, 'Jambi', 'Jambi', 'ID-JA'),
(6, 'Sumatra Selatan', 'Palembang', 'ID-SS'),
(7, 'Bengkulu', 'Bengkulu', 'ID-BE'),
(8, 'Lampung', 'Bandar Lampung', 'ID-LA'),
(9, 'Kepulauan Bangka Belitung', 'Pangkalpinang', 'ID-BB'),
(10, 'Kepulauan Riau', 'Tanjungpinang', 'ID-KR'),
(11, 'Daerah Khusus Ibukota Jakarta', 'Jakarta', 'ID-JB'),
(12, 'Jawa Barat', 'Bandung', 'ID-JB'),
(13, 'Jawa Tengah', 'Semarang', 'ID-JT'),
(14, 'Daerah Istimewa Yogyakarta', 'Yogyakarta', 'ID-YO'),
(15, 'Jawa Timur', 'Surabaya', 'ID-JI'),
(16, 'Banten', 'Serang', 'ID-BT'),
(17, 'Bali', 'Denpasar', 'ID-BA'),
(18, 'Nusa Tenggara Barat', 'Mataram', 'ID-NB'),
(19, 'Nusa Tenggara Timur', 'Kupang', 'ID-NT'),
(20, 'Kalimantan Barat', 'Pontianak', 'ID-KB'),
(21, 'Kalimantan Tengah', 'Palangka Raya', 'ID-KT'),
(22, 'Kalimantan Selatan', 'Banjarmasin', 'ID-KS'),
(23, 'Kalimantan Timur', 'Samarinda', 'ID-KI'),
(24, 'Kalimantan Utara', 'Tanjung Selor', 'ID-KU'),
(25, 'Sulawesi Utara', 'Manado', 'ID-SA'),
(26, 'Sulawesi Tengah', 'Palu', 'ID-ST'),
(27, 'Sulawesi Selatan', 'Makassar', 'ID-SN'),
(28, 'Sulawesi Tenggara', 'Kendari', 'ID-SG'),
(29, 'Gorontalo', 'Gorontalo', 'ID-GO'),
(30, 'Sulawesi Barat', 'Mamuju', 'ID-SR'),
(31, 'Maluku', 'Ambon', 'ID-MA'),
(32, 'Maluku Utara', 'Sofifi', 'ID-MU'),
(33, 'Papua', 'Jayapura', 'ID-PA'),
(34, 'Papua Barat', 'Manokwari', 'ID-PB');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_room`
--

CREATE TABLE `master_room` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_room`
--

INSERT INTO `master_room` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'CANTEEN', '2024-04-23 06:40:02', '2024-04-24 02:23:28'),
(2, 'GUEST ROOM A', '2024-04-23 06:44:18', '2024-04-23 06:44:18'),
(3, 'GUEST ROOM B', '2024-04-23 06:44:31', '2024-04-23 06:44:31'),
(4, 'HALL ROOM', '2024-04-23 06:44:40', '2024-04-23 06:44:40'),
(5, 'LOBBY', '2024-04-23 06:44:46', '2024-04-23 06:44:46'),
(6, 'MEETING ROOM OFFICE 2F', '2024-04-23 06:45:04', '2024-04-23 06:45:04'),
(7, 'MEETING ROOM PROD B2', '2024-04-23 07:27:39', '2024-04-23 07:27:39'),
(8, 'VISITOR CORRIDOR', '2024-04-23 07:28:02', '2024-04-23 07:28:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_section`
--

CREATE TABLE `master_section` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_section`
--

INSERT INTO `master_section` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'ENGINEERING', '2025-08-05 03:31:18', '2025-09-10 04:59:30'),
(2, 'PACKING', '2025-08-06 01:28:21', '2025-09-10 05:00:29'),
(3, 'NA', '2025-08-14 08:52:38', '2025-08-14 08:52:38'),
(4, 'BUNBURY 75', '2025-09-10 04:58:28', '2025-09-10 04:58:28'),
(5, 'CALENDER', '2025-09-10 04:58:53', '2025-09-10 04:58:53'),
(6, 'CUTTING', '2025-09-10 04:59:09', '2025-09-10 04:59:09'),
(7, 'FILLING', '2025-09-10 04:59:56', '2025-09-10 04:59:56'),
(8, 'LINGELCREAM', '2025-09-10 05:00:12', '2025-09-10 05:00:12'),
(9, 'QUALITY CONTROL', '2025-09-10 05:00:44', '2025-09-10 05:00:44'),
(10, 'WEIGHING', '2025-09-10 05:00:57', '2025-09-10 05:00:57'),
(11, 'WIDENING', '2025-10-31 13:19:20', '2025-10-31 13:19:20'),
(12, 'CLEANING', '2025-10-31 13:19:29', '2025-10-31 13:19:29'),
(13, 'IPC', '2025-10-31 13:19:38', '2025-10-31 13:19:38'),
(14, 'TIMBANG', '2025-10-31 13:19:46', '2025-10-31 13:19:46'),
(15, 'IT', '2025-11-20 12:06:41', '2025-11-20 12:06:41'),
(16, 'HRD', '2025-11-20 12:06:51', '2025-11-20 12:06:51'),
(17, 'GA', '2025-11-20 12:06:56', '2025-11-20 12:06:56'),
(18, 'HSE', '2025-11-20 12:07:03', '2025-11-20 12:07:03'),
(19, 'Sales', '2025-11-20 12:08:27', '2025-11-20 12:08:27'),
(20, 'Trade Marketing', '2025-11-20 12:08:44', '2025-11-20 12:08:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_work_hour`
--

CREATE TABLE `master_work_hour` (
  `id` bigint UNSIGNED NOT NULL,
  `work_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_work_hour`
--

INSERT INTO `master_work_hour` (`id`, `work_name`, `created_at`, `updated_at`) VALUES
(1, 'IT Team', '2026-03-12 11:50:50', '2026-03-12 11:50:50'),
(2, 'QA and QC', '2026-03-12 11:52:54', '2026-03-12 11:52:54'),
(4, 'Production', '2026-03-16 04:38:38', '2026-03-16 04:38:38'),
(13, 'Office HQ', '2026-03-31 08:08:44', '2026-03-31 08:08:44'),
(14, 'Office Ramadhan 2027', '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(15, 'Shift 1', '2026-03-31 08:16:28', '2026-03-31 08:16:28'),
(17, 'Shift Temporary', '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(18, 'shift 3', '2026-05-05 08:38:45', '2026-05-05 08:38:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical`
--

CREATE TABLE `medical` (
  `id` bigint UNSIGNED NOT NULL,
  `id_employees` int DEFAULT NULL,
  `id_vendor` int DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jk` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `umur` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ktp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paket` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_mcu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_lab` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lab` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `foto_thorax` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ekg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `audiometri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fisik_dokter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kesimpulan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `saran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `skor_framigham` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kriteria_sehat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_hemoglobin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_eritrosit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_hematokrit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_mcv` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_mch` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_mchc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_rdw` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_leukosit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_eos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_baso` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_neutro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_limfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_mono` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_eos_absolut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_baso_absolut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_neutro_absolut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_limfo_absolut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_mono_absolut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_trombosit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hm_led` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_warna` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_kejernihan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_berat_jenis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_ph` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_protein_albumin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_glukosa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_keton` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_bilirubin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_urobilinogen` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_nitrit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_leukosit_esterase` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_darah_haem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_eri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_leuko` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_epithel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_silinder` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_kristal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `u_lain` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fh_sgot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fh_sgpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fl_kolesterol_total` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fl_hdl_kolesterol` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fl_ldl_kolesterol` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fl_trigliserida` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gd_glukosa_puasa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gd_jpp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fg_bun` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fg_ureum` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fg_kreatinin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fg_egfr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `asam_urat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hbsag` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_template` int DEFAULT NULL,
  `tanggal_mcu` date DEFAULT NULL,
  `lampiran_mcu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_awal` date DEFAULT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_vendor`
--

CREATE TABLE `medical_vendor` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` enum('medical','training') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2023_08_31_015613_create_areas_table', 2),
(7, '2023_09_02_024706_create_departments_table', 3),
(8, '2023_09_02_033417_create_employees_table', 4),
(9, '2023_09_26_064635_add_employee_id_to_users', 5),
(10, '2023_09_26_084827_create_permission_tables', 6),
(12, '2023_10_11_062240_create_medical_table', 7),
(13, '2023_10_18_024800_create_medical_vendor_table', 8),
(15, '2023_10_20_030618_create_template_medical_table', 9),
(16, '2023_10_27_062139_create_master_lab_table', 10),
(18, '2023_11_15_132428_create_master_position_table', 12),
(19, '2023_11_15_132731_create_master_section_table', 12),
(20, '2023_11_15_132754_create_master_level_table', 12),
(21, '2023_11_22_104924_create_internal_rules_table', 13),
(22, '2023_11_22_110748_create_permission_internal_rules_table', 14),
(23, '2023_12_04_144024_create_temp_calendar_table', 15),
(24, '2023_12_04_144048_create_calendar_table', 15),
(25, '2023_12_05_154858_create_master_leave_table', 16),
(26, '2023_12_28_115852_create_pkb_table', 17),
(29, '2024_04_17_100306_create_tr_fkt_table', 18),
(30, '2024_04_17_101033_create_tr_fpkt_table', 18),
(31, '2024_04_17_114051_create_training_record_table', 19),
(32, '2024_04_23_130814_create_master_room_table', 20),
(33, '2024_04_24_095916_create_booking_record_table', 21),
(34, '2024_06_11_103915_create_news_event_table', 22),
(35, '2024_07_11_110201_create_qr_code_fkt_table', 23),
(36, '2024_07_11_110253_create_qr_code_fpkt_table', 23),
(37, '2024_07_18_095038_create_drug_masuk_table', 24),
(38, '2024_07_18_095143_create_drug_keluar_table', 24),
(39, '2024_07_18_095858_create_prestock_drug_table', 24),
(40, '2024_07_18_100603_create_master_drug_table', 24),
(41, '2024_09_06_085652_create_guests_table', 25),
(42, '2024_09_09_074718_create_training_periode_table', 26),
(43, '2024_09_20_152138_create_patient_table', 27),
(44, '2024_09_24_154714_create_doctor_account_table', 28),
(45, '2024_10_16_085318_create_training_evaluasi_table', 29),
(46, '2024_11_08_085641_create_training_status_table', 30),
(47, '2024_12_19_102613_create_security_account_table', 31),
(48, '2024_12_26_083833_create_log_catatan_training_table', 32),
(51, '2025_08_04_075901_create_performance_appraisal_matrix_table', 33),
(56, '2025_08_05_095815_create_master_appraisal_table', 36),
(73, '2025_08_13_112536_create_evaluation_histories_table', 49),
(74, '2025_08_15_083731_create_evaluation_attachments_table', 50),
(75, '2025_08_21_100040_create_master_building_table', 51),
(88, '2023_11_02_090336_create_user_log_table', 56),
(95, '2025_08_21_112235_create_master_line_approval_table', 60),
(111, '2025_08_06_084158_create_evaluations_table', 61),
(112, '2025_09_23_134459_create_master_hiring_table', 62),
(113, '2025_09_26_093555_create_employee_requisition_table', 63),
(115, '2025_11_05_083515_create_candidate_table', 64),
(116, '2025_11_12_150201_create_selection_table', 65),
(117, '2026_03_16_131936_create_sessions_table', 66),
(118, '2026_05_11_151704_create_master_contract_table', 67),
(119, '2026_05_11_151936_add_employee_detail', 68);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(55, 'App\\Models\\User', 2),
(55, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(1, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(55, 'App\\Models\\User', 11),
(4, 'App\\Models\\User', 12),
(39, 'App\\Models\\User', 13),
(1, 'App\\Models\\User', 14),
(39, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(4, 'App\\Models\\User', 17),
(1, 'App\\Models\\User', 18),
(4, 'App\\Models\\User', 19),
(2, 'App\\Models\\User', 20),
(39, 'App\\Models\\User', 21),
(39, 'App\\Models\\User', 22),
(2, 'App\\Models\\User', 31),
(37, 'App\\Models\\User', 32),
(39, 'App\\Models\\User', 34),
(39, 'App\\Models\\User', 35),
(39, 'App\\Models\\User', 37),
(4, 'App\\Models\\User', 39),
(4, 'App\\Models\\User', 40),
(4, 'App\\Models\\User', 41),
(4, 'App\\Models\\User', 42),
(4, 'App\\Models\\User', 43),
(4, 'App\\Models\\User', 44),
(4, 'App\\Models\\User', 45),
(4, 'App\\Models\\User', 46),
(4, 'App\\Models\\User', 47),
(4, 'App\\Models\\User', 48),
(4, 'App\\Models\\User', 49),
(4, 'App\\Models\\User', 50),
(4, 'App\\Models\\User', 51),
(4, 'App\\Models\\User', 52),
(4, 'App\\Models\\User', 53),
(4, 'App\\Models\\User', 54),
(4, 'App\\Models\\User', 55),
(4, 'App\\Models\\User', 56),
(39, 'App\\Models\\User', 57),
(4, 'App\\Models\\User', 58),
(4, 'App\\Models\\User', 59),
(4, 'App\\Models\\User', 60),
(39, 'App\\Models\\User', 61),
(39, 'App\\Models\\User', 62),
(4, 'App\\Models\\User', 63),
(4, 'App\\Models\\User', 64),
(4, 'App\\Models\\User', 65),
(39, 'App\\Models\\User', 66),
(39, 'App\\Models\\User', 67),
(4, 'App\\Models\\User', 68),
(4, 'App\\Models\\User', 69),
(4, 'App\\Models\\User', 70),
(4, 'App\\Models\\User', 71),
(4, 'App\\Models\\User', 72),
(4, 'App\\Models\\User', 73),
(4, 'App\\Models\\User', 74),
(4, 'App\\Models\\User', 75),
(4, 'App\\Models\\User', 76),
(4, 'App\\Models\\User', 77),
(4, 'App\\Models\\User', 78),
(39, 'App\\Models\\User', 79),
(4, 'App\\Models\\User', 81),
(4, 'App\\Models\\User', 82),
(4, 'App\\Models\\User', 83),
(4, 'App\\Models\\User', 84),
(4, 'App\\Models\\User', 85),
(4, 'App\\Models\\User', 86),
(4, 'App\\Models\\User', 87),
(4, 'App\\Models\\User', 88),
(4, 'App\\Models\\User', 89),
(4, 'App\\Models\\User', 90),
(4, 'App\\Models\\User', 91),
(4, 'App\\Models\\User', 92),
(4, 'App\\Models\\User', 93),
(4, 'App\\Models\\User', 94),
(4, 'App\\Models\\User', 95),
(4, 'App\\Models\\User', 96),
(39, 'App\\Models\\User', 97),
(4, 'App\\Models\\User', 98),
(4, 'App\\Models\\User', 99),
(4, 'App\\Models\\User', 100),
(39, 'App\\Models\\User', 101),
(4, 'App\\Models\\User', 102),
(39, 'App\\Models\\User', 103),
(39, 'App\\Models\\User', 104),
(4, 'App\\Models\\User', 105),
(4, 'App\\Models\\User', 106),
(4, 'App\\Models\\User', 107),
(4, 'App\\Models\\User', 108),
(4, 'App\\Models\\User', 109),
(4, 'App\\Models\\User', 110),
(4, 'App\\Models\\User', 111),
(4, 'App\\Models\\User', 112),
(39, 'App\\Models\\User', 113),
(4, 'App\\Models\\User', 114),
(4, 'App\\Models\\User', 115),
(4, 'App\\Models\\User', 116),
(39, 'App\\Models\\User', 117),
(4, 'App\\Models\\User', 118),
(4, 'App\\Models\\User', 119),
(39, 'App\\Models\\User', 120),
(4, 'App\\Models\\User', 121),
(4, 'App\\Models\\User', 122),
(4, 'App\\Models\\User', 123),
(4, 'App\\Models\\User', 124),
(4, 'App\\Models\\User', 125),
(4, 'App\\Models\\User', 126),
(4, 'App\\Models\\User', 127),
(4, 'App\\Models\\User', 129),
(4, 'App\\Models\\User', 130),
(4, 'App\\Models\\User', 131),
(4, 'App\\Models\\User', 132),
(4, 'App\\Models\\User', 133),
(4, 'App\\Models\\User', 134),
(4, 'App\\Models\\User', 135),
(4, 'App\\Models\\User', 136),
(4, 'App\\Models\\User', 137),
(4, 'App\\Models\\User', 138),
(4, 'App\\Models\\User', 139),
(4, 'App\\Models\\User', 140),
(4, 'App\\Models\\User', 141),
(4, 'App\\Models\\User', 142),
(39, 'App\\Models\\User', 143),
(39, 'App\\Models\\User', 144),
(4, 'App\\Models\\User', 145),
(4, 'App\\Models\\User', 146),
(4, 'App\\Models\\User', 147),
(4, 'App\\Models\\User', 148),
(4, 'App\\Models\\User', 149),
(4, 'App\\Models\\User', 150),
(4, 'App\\Models\\User', 151),
(39, 'App\\Models\\User', 152),
(4, 'App\\Models\\User', 153),
(4, 'App\\Models\\User', 154),
(4, 'App\\Models\\User', 155),
(4, 'App\\Models\\User', 156),
(4, 'App\\Models\\User', 157),
(4, 'App\\Models\\User', 158),
(4, 'App\\Models\\User', 159),
(4, 'App\\Models\\User', 160),
(4, 'App\\Models\\User', 161),
(4, 'App\\Models\\User', 162),
(4, 'App\\Models\\User', 163),
(39, 'App\\Models\\User', 164),
(4, 'App\\Models\\User', 165),
(4, 'App\\Models\\User', 166),
(4, 'App\\Models\\User', 167),
(4, 'App\\Models\\User', 168),
(4, 'App\\Models\\User', 169),
(39, 'App\\Models\\User', 170),
(39, 'App\\Models\\User', 171),
(4, 'App\\Models\\User', 172),
(4, 'App\\Models\\User', 173),
(4, 'App\\Models\\User', 174),
(4, 'App\\Models\\User', 175),
(4, 'App\\Models\\User', 176),
(4, 'App\\Models\\User', 177),
(4, 'App\\Models\\User', 178),
(4, 'App\\Models\\User', 179),
(4, 'App\\Models\\User', 180),
(4, 'App\\Models\\User', 181),
(4, 'App\\Models\\User', 182),
(4, 'App\\Models\\User', 183),
(4, 'App\\Models\\User', 184),
(4, 'App\\Models\\User', 185),
(4, 'App\\Models\\User', 186),
(4, 'App\\Models\\User', 187),
(4, 'App\\Models\\User', 188),
(4, 'App\\Models\\User', 189),
(4, 'App\\Models\\User', 190),
(4, 'App\\Models\\User', 191),
(4, 'App\\Models\\User', 192),
(4, 'App\\Models\\User', 193),
(4, 'App\\Models\\User', 194),
(4, 'App\\Models\\User', 195),
(4, 'App\\Models\\User', 196),
(4, 'App\\Models\\User', 197),
(4, 'App\\Models\\User', 198),
(4, 'App\\Models\\User', 199),
(4, 'App\\Models\\User', 200),
(4, 'App\\Models\\User', 201),
(4, 'App\\Models\\User', 202),
(4, 'App\\Models\\User', 203),
(4, 'App\\Models\\User', 204),
(4, 'App\\Models\\User', 205),
(4, 'App\\Models\\User', 206),
(4, 'App\\Models\\User', 207),
(4, 'App\\Models\\User', 208),
(4, 'App\\Models\\User', 209),
(4, 'App\\Models\\User', 210),
(4, 'App\\Models\\User', 211),
(4, 'App\\Models\\User', 212),
(4, 'App\\Models\\User', 213),
(4, 'App\\Models\\User', 214),
(4, 'App\\Models\\User', 215),
(4, 'App\\Models\\User', 216),
(4, 'App\\Models\\User', 217),
(4, 'App\\Models\\User', 218),
(4, 'App\\Models\\User', 219),
(4, 'App\\Models\\User', 220),
(4, 'App\\Models\\User', 221),
(4, 'App\\Models\\User', 222),
(4, 'App\\Models\\User', 223),
(4, 'App\\Models\\User', 224),
(4, 'App\\Models\\User', 225),
(4, 'App\\Models\\User', 226),
(4, 'App\\Models\\User', 227),
(4, 'App\\Models\\User', 228),
(4, 'App\\Models\\User', 229),
(4, 'App\\Models\\User', 230),
(4, 'App\\Models\\User', 231),
(4, 'App\\Models\\User', 232),
(4, 'App\\Models\\User', 233),
(4, 'App\\Models\\User', 234),
(4, 'App\\Models\\User', 235),
(4, 'App\\Models\\User', 236),
(4, 'App\\Models\\User', 237),
(4, 'App\\Models\\User', 238),
(4, 'App\\Models\\User', 239),
(4, 'App\\Models\\User', 240),
(4, 'App\\Models\\User', 241),
(4, 'App\\Models\\User', 242),
(4, 'App\\Models\\User', 243),
(4, 'App\\Models\\User', 244),
(4, 'App\\Models\\User', 245),
(4, 'App\\Models\\User', 246),
(4, 'App\\Models\\User', 247),
(4, 'App\\Models\\User', 248),
(4, 'App\\Models\\User', 249),
(4, 'App\\Models\\User', 250),
(4, 'App\\Models\\User', 251),
(4, 'App\\Models\\User', 252),
(4, 'App\\Models\\User', 253),
(4, 'App\\Models\\User', 254),
(4, 'App\\Models\\User', 255),
(4, 'App\\Models\\User', 256),
(4, 'App\\Models\\User', 257),
(39, 'App\\Models\\User', 258),
(4, 'App\\Models\\User', 259),
(4, 'App\\Models\\User', 260),
(4, 'App\\Models\\User', 261),
(4, 'App\\Models\\User', 262),
(4, 'App\\Models\\User', 263),
(4, 'App\\Models\\User', 264),
(4, 'App\\Models\\User', 265),
(4, 'App\\Models\\User', 266),
(4, 'App\\Models\\User', 267),
(4, 'App\\Models\\User', 268),
(4, 'App\\Models\\User', 269),
(4, 'App\\Models\\User', 270),
(4, 'App\\Models\\User', 271),
(4, 'App\\Models\\User', 272),
(4, 'App\\Models\\User', 273),
(4, 'App\\Models\\User', 274),
(4, 'App\\Models\\User', 275),
(4, 'App\\Models\\User', 276),
(4, 'App\\Models\\User', 277),
(4, 'App\\Models\\User', 278),
(4, 'App\\Models\\User', 279),
(4, 'App\\Models\\User', 280),
(4, 'App\\Models\\User', 281),
(4, 'App\\Models\\User', 282),
(4, 'App\\Models\\User', 283),
(4, 'App\\Models\\User', 284),
(4, 'App\\Models\\User', 285),
(4, 'App\\Models\\User', 286),
(4, 'App\\Models\\User', 287),
(4, 'App\\Models\\User', 288),
(4, 'App\\Models\\User', 289),
(4, 'App\\Models\\User', 290),
(4, 'App\\Models\\User', 291),
(39, 'App\\Models\\User', 292),
(4, 'App\\Models\\User', 293),
(4, 'App\\Models\\User', 294),
(4, 'App\\Models\\User', 295),
(4, 'App\\Models\\User', 296),
(4, 'App\\Models\\User', 297),
(4, 'App\\Models\\User', 298),
(4, 'App\\Models\\User', 299),
(4, 'App\\Models\\User', 300),
(4, 'App\\Models\\User', 301),
(4, 'App\\Models\\User', 302),
(4, 'App\\Models\\User', 303),
(4, 'App\\Models\\User', 304),
(4, 'App\\Models\\User', 305),
(4, 'App\\Models\\User', 306),
(4, 'App\\Models\\User', 307),
(4, 'App\\Models\\User', 308),
(4, 'App\\Models\\User', 309),
(4, 'App\\Models\\User', 310),
(4, 'App\\Models\\User', 311),
(4, 'App\\Models\\User', 312),
(4, 'App\\Models\\User', 313),
(4, 'App\\Models\\User', 314),
(4, 'App\\Models\\User', 315),
(4, 'App\\Models\\User', 316),
(4, 'App\\Models\\User', 317),
(4, 'App\\Models\\User', 318),
(4, 'App\\Models\\User', 319),
(4, 'App\\Models\\User', 320),
(4, 'App\\Models\\User', 321),
(4, 'App\\Models\\User', 322),
(4, 'App\\Models\\User', 323),
(4, 'App\\Models\\User', 324),
(4, 'App\\Models\\User', 325),
(4, 'App\\Models\\User', 326),
(4, 'App\\Models\\User', 327),
(4, 'App\\Models\\User', 328),
(4, 'App\\Models\\User', 329),
(4, 'App\\Models\\User', 330),
(4, 'App\\Models\\User', 331),
(4, 'App\\Models\\User', 332),
(4, 'App\\Models\\User', 333),
(4, 'App\\Models\\User', 334),
(4, 'App\\Models\\User', 335),
(4, 'App\\Models\\User', 336),
(4, 'App\\Models\\User', 337),
(4, 'App\\Models\\User', 338),
(4, 'App\\Models\\User', 339),
(4, 'App\\Models\\User', 340),
(4, 'App\\Models\\User', 341),
(4, 'App\\Models\\User', 342),
(4, 'App\\Models\\User', 343),
(4, 'App\\Models\\User', 344),
(4, 'App\\Models\\User', 345),
(4, 'App\\Models\\User', 346),
(4, 'App\\Models\\User', 347),
(4, 'App\\Models\\User', 348),
(4, 'App\\Models\\User', 349),
(4, 'App\\Models\\User', 350),
(4, 'App\\Models\\User', 351),
(4, 'App\\Models\\User', 352),
(4, 'App\\Models\\User', 353),
(39, 'App\\Models\\User', 354),
(4, 'App\\Models\\User', 355),
(4, 'App\\Models\\User', 356),
(4, 'App\\Models\\User', 357),
(4, 'App\\Models\\User', 358),
(4, 'App\\Models\\User', 359),
(4, 'App\\Models\\User', 360),
(4, 'App\\Models\\User', 361),
(4, 'App\\Models\\User', 362),
(4, 'App\\Models\\User', 363),
(4, 'App\\Models\\User', 364),
(4, 'App\\Models\\User', 365),
(4, 'App\\Models\\User', 366),
(4, 'App\\Models\\User', 367),
(4, 'App\\Models\\User', 368),
(4, 'App\\Models\\User', 369),
(4, 'App\\Models\\User', 370),
(4, 'App\\Models\\User', 371),
(4, 'App\\Models\\User', 372),
(4, 'App\\Models\\User', 373),
(4, 'App\\Models\\User', 374),
(4, 'App\\Models\\User', 375),
(4, 'App\\Models\\User', 376),
(4, 'App\\Models\\User', 377),
(4, 'App\\Models\\User', 378),
(4, 'App\\Models\\User', 379),
(4, 'App\\Models\\User', 380),
(4, 'App\\Models\\User', 381),
(4, 'App\\Models\\User', 382),
(4, 'App\\Models\\User', 383),
(4, 'App\\Models\\User', 384),
(4, 'App\\Models\\User', 385),
(4, 'App\\Models\\User', 386),
(4, 'App\\Models\\User', 387),
(4, 'App\\Models\\User', 388),
(36, 'App\\Models\\User', 390),
(4, 'App\\Models\\User', 392),
(39, 'App\\Models\\User', 393),
(4, 'App\\Models\\User', 394),
(4, 'App\\Models\\User', 395),
(49, 'App\\Models\\User', 396),
(4, 'App\\Models\\User', 398),
(1, 'App\\Models\\User', 400),
(4, 'App\\Models\\User', 402),
(49, 'App\\Models\\User', 403),
(4, 'App\\Models\\User', 408),
(4, 'App\\Models\\User', 409),
(4, 'App\\Models\\User', 410),
(4, 'App\\Models\\User', 411),
(4, 'App\\Models\\User', 413),
(4, 'App\\Models\\User', 415),
(4, 'App\\Models\\User', 417),
(4, 'App\\Models\\User', 419),
(4, 'App\\Models\\User', 422),
(4, 'App\\Models\\User', 423),
(4, 'App\\Models\\User', 424),
(4, 'App\\Models\\User', 425),
(4, 'App\\Models\\User', 426),
(4, 'App\\Models\\User', 427),
(4, 'App\\Models\\User', 428),
(4, 'App\\Models\\User', 429),
(4, 'App\\Models\\User', 430),
(4, 'App\\Models\\User', 431),
(4, 'App\\Models\\User', 432),
(4, 'App\\Models\\User', 433),
(4, 'App\\Models\\User', 434),
(4, 'App\\Models\\User', 435),
(4, 'App\\Models\\User', 436),
(4, 'App\\Models\\User', 437),
(4, 'App\\Models\\User', 438),
(4, 'App\\Models\\User', 439),
(4, 'App\\Models\\User', 440),
(4, 'App\\Models\\User', 441),
(4, 'App\\Models\\User', 442),
(4, 'App\\Models\\User', 443),
(4, 'App\\Models\\User', 444),
(4, 'App\\Models\\User', 445),
(4, 'App\\Models\\User', 446),
(4, 'App\\Models\\User', 447),
(4, 'App\\Models\\User', 448),
(4, 'App\\Models\\User', 449),
(4, 'App\\Models\\User', 450),
(4, 'App\\Models\\User', 451),
(4, 'App\\Models\\User', 452),
(4, 'App\\Models\\User', 453),
(4, 'App\\Models\\User', 454),
(4, 'App\\Models\\User', 455),
(4, 'App\\Models\\User', 456),
(4, 'App\\Models\\User', 457),
(4, 'App\\Models\\User', 458),
(4, 'App\\Models\\User', 459),
(4, 'App\\Models\\User', 460),
(4, 'App\\Models\\User', 461),
(4, 'App\\Models\\User', 462),
(4, 'App\\Models\\User', 463),
(4, 'App\\Models\\User', 464),
(4, 'App\\Models\\User', 465),
(4, 'App\\Models\\User', 466),
(4, 'App\\Models\\User', 467),
(4, 'App\\Models\\User', 468),
(4, 'App\\Models\\User', 469),
(4, 'App\\Models\\User', 470),
(4, 'App\\Models\\User', 471),
(4, 'App\\Models\\User', 472),
(4, 'App\\Models\\User', 473),
(4, 'App\\Models\\User', 474),
(4, 'App\\Models\\User', 475),
(4, 'App\\Models\\User', 476),
(4, 'App\\Models\\User', 477),
(4, 'App\\Models\\User', 478),
(4, 'App\\Models\\User', 479),
(4, 'App\\Models\\User', 480),
(4, 'App\\Models\\User', 481),
(4, 'App\\Models\\User', 482),
(4, 'App\\Models\\User', 483),
(4, 'App\\Models\\User', 484),
(4, 'App\\Models\\User', 485),
(4, 'App\\Models\\User', 486),
(4, 'App\\Models\\User', 487),
(4, 'App\\Models\\User', 488),
(4, 'App\\Models\\User', 489),
(4, 'App\\Models\\User', 490),
(4, 'App\\Models\\User', 491),
(4, 'App\\Models\\User', 492),
(4, 'App\\Models\\User', 493),
(4, 'App\\Models\\User', 494),
(4, 'App\\Models\\User', 495),
(4, 'App\\Models\\User', 496),
(4, 'App\\Models\\User', 497),
(4, 'App\\Models\\User', 498),
(4, 'App\\Models\\User', 499),
(4, 'App\\Models\\User', 500),
(4, 'App\\Models\\User', 501),
(4, 'App\\Models\\User', 502),
(4, 'App\\Models\\User', 503),
(4, 'App\\Models\\User', 504),
(4, 'App\\Models\\User', 505),
(4, 'App\\Models\\User', 506),
(4, 'App\\Models\\User', 507),
(4, 'App\\Models\\User', 508),
(4, 'App\\Models\\User', 509),
(4, 'App\\Models\\User', 510),
(4, 'App\\Models\\User', 511),
(4, 'App\\Models\\User', 512),
(4, 'App\\Models\\User', 513),
(4, 'App\\Models\\User', 514),
(4, 'App\\Models\\User', 515),
(4, 'App\\Models\\User', 516),
(4, 'App\\Models\\User', 517),
(4, 'App\\Models\\User', 518),
(4, 'App\\Models\\User', 519),
(4, 'App\\Models\\User', 520),
(4, 'App\\Models\\User', 521),
(4, 'App\\Models\\User', 522),
(4, 'App\\Models\\User', 523),
(4, 'App\\Models\\User', 524),
(4, 'App\\Models\\User', 525),
(4, 'App\\Models\\User', 526),
(4, 'App\\Models\\User', 527),
(4, 'App\\Models\\User', 528),
(4, 'App\\Models\\User', 529),
(4, 'App\\Models\\User', 530),
(4, 'App\\Models\\User', 531),
(4, 'App\\Models\\User', 532),
(4, 'App\\Models\\User', 533),
(4, 'App\\Models\\User', 534),
(4, 'App\\Models\\User', 535),
(4, 'App\\Models\\User', 536),
(4, 'App\\Models\\User', 537),
(4, 'App\\Models\\User', 538),
(4, 'App\\Models\\User', 539),
(4, 'App\\Models\\User', 540),
(48, 'App\\Models\\User', 542),
(4, 'App\\Models\\User', 543),
(4, 'App\\Models\\User', 544),
(4, 'App\\Models\\User', 545),
(4, 'App\\Models\\User', 546),
(4, 'App\\Models\\User', 547),
(4, 'App\\Models\\User', 548),
(4, 'App\\Models\\User', 549),
(4, 'App\\Models\\User', 550),
(4, 'App\\Models\\User', 551),
(4, 'App\\Models\\User', 552),
(4, 'App\\Models\\User', 553),
(4, 'App\\Models\\User', 554),
(4, 'App\\Models\\User', 555),
(4, 'App\\Models\\User', 556),
(4, 'App\\Models\\User', 557),
(4, 'App\\Models\\User', 558),
(4, 'App\\Models\\User', 559),
(4, 'App\\Models\\User', 560),
(4, 'App\\Models\\User', 561),
(4, 'App\\Models\\User', 562),
(4, 'App\\Models\\User', 563),
(4, 'App\\Models\\User', 564),
(4, 'App\\Models\\User', 565),
(4, 'App\\Models\\User', 566),
(4, 'App\\Models\\User', 567),
(4, 'App\\Models\\User', 568),
(4, 'App\\Models\\User', 569),
(4, 'App\\Models\\User', 570),
(4, 'App\\Models\\User', 571),
(4, 'App\\Models\\User', 572),
(4, 'App\\Models\\User', 573),
(4, 'App\\Models\\User', 579),
(4, 'App\\Models\\User', 580),
(4, 'App\\Models\\User', 581),
(4, 'App\\Models\\User', 582),
(4, 'App\\Models\\User', 583),
(4, 'App\\Models\\User', 584),
(4, 'App\\Models\\User', 585),
(4, 'App\\Models\\User', 586),
(4, 'App\\Models\\User', 588),
(4, 'App\\Models\\User', 589),
(4, 'App\\Models\\User', 590),
(4, 'App\\Models\\User', 591),
(4, 'App\\Models\\User', 592),
(4, 'App\\Models\\User', 595),
(4, 'App\\Models\\User', 596),
(4, 'App\\Models\\User', 597),
(4, 'App\\Models\\User', 598),
(4, 'App\\Models\\User', 599),
(4, 'App\\Models\\User', 600),
(4, 'App\\Models\\User', 601),
(4, 'App\\Models\\User', 602),
(49, 'App\\Models\\User', 604),
(51, 'App\\Models\\User', 608);

-- --------------------------------------------------------

--
-- Struktur dari tabel `news_event`
--

CREATE TABLE `news_event` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_news` date DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tumbnail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gambar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link_video` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lampiran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `news_event`
--

INSERT INTO `news_event` (`id`, `judul`, `tanggal_news`, `detail`, `tumbnail`, `gambar`, `link_video`, `lampiran`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dummy', '2026-02-03', '<p>Lorem&nbsp;ipsum&nbsp;dolor&nbsp;sit&nbsp;amet,&nbsp;consectetur&nbsp;adipiscing&nbsp;elit.&nbsp;Ut&nbsp;luctus&nbsp;dictum&nbsp;congue.&nbsp;Nullam&nbsp;eu&nbsp;erat&nbsp;diam.&nbsp;Fusce&nbsp;auctor,&nbsp;erat&nbsp;ut&nbsp;dictum&nbsp;rutrum,&nbsp;massa&nbsp;tellus&nbsp;vehicula&nbsp;dui,&nbsp;a&nbsp;dictum&nbsp;eros&nbsp;tortor&nbsp;vitae&nbsp;est.&nbsp;Donec&nbsp;arcu&nbsp;enim,&nbsp;fermentum&nbsp;tristique&nbsp;lacus&nbsp;in,&nbsp;maximus&nbsp;pulvinar&nbsp;libero.&nbsp;Pellentesque&nbsp;porttitor&nbsp;libero&nbsp;nunc,&nbsp;a&nbsp;tincidunt&nbsp;quam&nbsp;pretium&nbsp;a.&nbsp;Nulla&nbsp;facilisi.&nbsp;Etiam&nbsp;dignissim&nbsp;dictum&nbsp;enim,&nbsp;eu&nbsp;vehicula&nbsp;neque&nbsp;interdum&nbsp;vel.&nbsp;Maecenas&nbsp;eleifend&nbsp;neque&nbsp;justo,&nbsp;vel&nbsp;fringilla&nbsp;ligula&nbsp;scelerisque&nbsp;sit&nbsp;amet.&nbsp;Mauris&nbsp;fringilla,&nbsp;mauris&nbsp;et&nbsp;dapibus&nbsp;rhoncus,&nbsp;risus&nbsp;mi&nbsp;tempor&nbsp;quam,&nbsp;non&nbsp;pulvinar&nbsp;nisi&nbsp;lectus&nbsp;id&nbsp;augue.&nbsp;Orci&nbsp;varius&nbsp;natoque&nbsp;penatibus&nbsp;et&nbsp;magnis&nbsp;dis&nbsp;parturient&nbsp;montes,&nbsp;nascetur&nbsp;ridiculus&nbsp;mus.&nbsp;Sed&nbsp;cursus&nbsp;tincidunt&nbsp;urna,&nbsp;quis&nbsp;semper&nbsp;turpis.&nbsp;Curabitur&nbsp;pellentesque&nbsp;arcu&nbsp;mi,&nbsp;non&nbsp;tincidunt&nbsp;tellus&nbsp;vulputate&nbsp;nec.&nbsp;Suspendisse&nbsp;rutrum&nbsp;elementum&nbsp;nibh&nbsp;eu&nbsp;euismod.&nbsp;Lorem&nbsp;ipsum&nbsp;dolor&nbsp;sit&nbsp;amet,&nbsp;consectetur&nbsp;adipiscing&nbsp;elit.&nbsp;Nunc&nbsp;convallis&nbsp;purus&nbsp;nec&nbsp;lectus&nbsp;sagittis,&nbsp;ornare&nbsp;maximus&nbsp;velit&nbsp;ornare.</p>\r\n\r\n<p>Sed&nbsp;et&nbsp;consectetur&nbsp;neque.&nbsp;Aenean&nbsp;ac&nbsp;vestibulum&nbsp;erat.&nbsp;Etiam&nbsp;bibendum&nbsp;dui&nbsp;ut&nbsp;venenatis&nbsp;rutrum.&nbsp;Nam&nbsp;malesuada&nbsp;et&nbsp;metus&nbsp;in&nbsp;volutpat.&nbsp;Fusce&nbsp;posuere&nbsp;enim&nbsp;commodo&nbsp;ullamcorper&nbsp;viverra.&nbsp;Suspendisse&nbsp;eros&nbsp;ex,&nbsp;venenatis&nbsp;sed&nbsp;rutrum&nbsp;sit&nbsp;amet,&nbsp;euismod&nbsp;non&nbsp;tellus.&nbsp;Mauris&nbsp;vel&nbsp;orci&nbsp;a&nbsp;elit&nbsp;eleifend&nbsp;accumsan.&nbsp;Morbi&nbsp;faucibus&nbsp;faucibus&nbsp;mauris&nbsp;eget&nbsp;elementum.&nbsp;Duis&nbsp;ac&nbsp;dui&nbsp;est.&nbsp;Nam&nbsp;feugiat&nbsp;fermentum&nbsp;elit,&nbsp;imperdiet&nbsp;accumsan&nbsp;dui&nbsp;ornare&nbsp;et.</p>', '1770084978.png', '69815be8041ad.jpg', 'https://www.youtube.com', '1770085456.pdf', 'release', '2026-02-03 09:16:18', '2026-02-03 14:34:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtime_approvals`
--

CREATE TABLE `overtime_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `overtime_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int NOT NULL,
  `status` enum('waiting','approved','rejected','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `approved_at` timestamp NULL DEFAULT NULL,
  `reason_reject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approval_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `patient`
--

CREATE TABLE `patient` (
  `id` bigint UNSIGNED NOT NULL,
  `visit_date` date DEFAULT NULL,
  `id_dokter` int DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `keluhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `diagnosa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tensi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'hrd.master.appraisal.read', 'web', NULL, '2025-08-04 01:46:38'),
(3, 'administrator.role.read', 'web', '2023-09-27 06:11:49', '2023-09-27 06:11:49'),
(4, 'administrator.permission.read', 'web', '2023-09-27 06:14:53', '2023-09-27 06:14:53'),
(5, 'administrator.user.update', 'web', '2023-09-27 06:15:29', '2023-10-02 07:09:15'),
(6, 'administrator.permission.update', 'web', '2023-09-27 06:15:47', '2023-10-02 07:09:24'),
(7, 'administrator.role.update', 'web', '2023-09-27 06:16:29', '2023-10-02 07:10:48'),
(8, 'hrd.employee.read', 'web', '2023-09-27 07:50:17', '2023-09-27 08:37:11'),
(11, 'hrd.employee.update', 'web', '2023-09-27 08:20:13', '2023-10-02 07:11:31'),
(12, 'hrd.master.area.update', 'web', '2023-09-27 08:38:18', '2023-10-18 04:57:10'),
(14, 'hrd.master.area.read', 'web', '2023-10-02 07:13:09', '2023-10-18 04:56:56'),
(15, 'hrd.master.department.read', 'web', '2023-10-02 07:14:26', '2023-10-18 05:04:30'),
(16, 'hrd.master.department.update', 'web', '2023-10-02 07:14:43', '2023-10-18 06:09:57'),
(17, 'administrator.menu', 'web', '2023-10-02 07:17:09', '2023-10-02 07:17:09'),
(18, 'hrd.menu', 'web', '2023-10-02 07:17:19', '2023-10-16 03:40:02'),
(27, 'hrd.menu.medical-record', 'web', '2023-10-18 04:53:38', '2023-10-18 04:53:38'),
(28, 'hrd.medical-record.reguler.read', 'web', '2023-10-18 04:54:19', '2023-10-18 04:54:19'),
(29, 'hrd.medical-record.ireguler.read', 'web', '2023-10-18 04:54:32', '2023-10-18 04:54:32'),
(30, 'hrd.menu.master', 'web', '2023-10-18 04:55:31', '2023-10-18 04:55:31'),
(31, 'hrd.master.vendor.create', 'web', '2023-10-18 04:55:57', '2023-11-15 06:55:47'),
(32, 'hrd.master.vendor.update', 'web', '2023-10-18 06:48:11', '2023-10-18 06:48:11'),
(33, 'hrd.medical-record.reguler.create', 'web', '2023-11-09 07:06:16', '2023-11-09 07:06:16'),
(34, 'hrd.medical-record.reguler.detail', 'web', '2023-11-09 07:08:24', '2023-11-09 07:08:24'),
(35, 'hrd.medical-record.reguler.update', 'web', '2023-11-09 07:08:53', '2023-11-09 07:08:53'),
(36, 'hrd.medical-record.reguler.detail.view-more', 'web', '2023-11-09 07:21:12', '2023-11-09 07:21:12'),
(37, 'hrd.medical-record.reguler.detail.update', 'web', '2023-11-09 07:21:29', '2023-11-09 07:21:29'),
(38, 'hrd.medical-record.reguler.detail.upload', 'web', '2023-11-09 07:22:04', '2023-11-09 07:22:04'),
(39, 'hrd.medical-record.reguler.detail.delete', 'web', '2023-11-09 07:22:17', '2023-11-09 07:22:17'),
(40, 'hrd.medical-record.ireguler.create', 'web', '2023-11-09 08:17:45', '2023-11-09 08:17:45'),
(41, 'hrd.medical-record.ireguler.upload', 'web', '2023-11-09 08:19:12', '2023-11-09 08:19:12'),
(42, 'hrd.medical-record.ireguler.view-pdf', 'web', '2023-11-09 08:20:07', '2023-11-09 08:20:07'),
(43, 'hrd.employee.create', 'web', '2023-11-09 08:30:30', '2023-11-09 08:30:30'),
(44, 'administrator.log.read', 'web', '2023-11-09 09:11:12', '2023-11-09 09:11:12'),
(47, 'emp.employee.read', 'web', '2023-11-10 07:02:33', '2023-11-10 07:02:33'),
(48, 'emp.menu', 'web', '2023-11-10 09:33:09', '2023-11-13 10:10:10'),
(49, 'emp.medical.read', 'web', '2023-11-13 11:03:21', '2023-11-13 11:03:21'),
(50, 'emp.calendar.read', 'web', '2023-11-13 11:16:02', '2023-11-13 11:16:02'),
(51, 'hrd.master.vendor.read', 'web', '2023-11-15 06:59:57', '2023-11-15 06:59:57'),
(52, 'hrd.master.section.read', 'web', '2023-11-15 07:03:31', '2023-11-15 07:03:31'),
(53, 'hrd.master.section.create', 'web', '2023-11-15 07:06:17', '2023-11-15 07:06:17'),
(54, 'hrd.master.section.update', 'web', '2023-11-15 07:06:34', '2023-11-15 07:06:34'),
(56, 'hrd.master.position.read', 'web', '2023-11-15 07:17:07', '2023-11-15 07:17:07'),
(57, 'hrd.master.position.create', 'web', '2023-11-15 07:17:17', '2023-11-15 07:17:17'),
(58, 'hrd.master.position.update', 'web', '2023-11-15 07:17:28', '2023-11-15 07:17:28'),
(59, 'hrd.master.level.read', 'web', '2023-11-15 07:29:49', '2023-11-15 07:29:49'),
(60, 'hrd.master.level.create', 'web', '2023-11-15 07:30:03', '2023-11-15 07:30:03'),
(61, 'hrd.master.level.update', 'web', '2023-11-15 07:30:14', '2023-11-15 07:30:14'),
(62, 'hrd.internal-rules.read', 'web', '2023-11-22 03:39:29', '2023-11-22 03:39:29'),
(63, 'hrd.internal-rules.create', 'web', '2023-11-22 04:52:11', '2023-11-22 04:52:11'),
(64, 'hrd.internal-rules.update', 'web', '2023-11-22 07:53:54', '2023-11-22 07:53:54'),
(65, 'hrd.internal-rules.setting', 'web', '2023-11-23 01:56:10', '2023-11-23 01:56:10'),
(66, 'hrd.internal-rules.revisi', 'web', '2023-11-28 07:34:15', '2023-11-28 07:34:15'),
(67, 'hrd.internal-rules.detail', 'web', '2023-11-29 04:13:33', '2023-11-30 06:50:37'),
(68, 'emp.internal-rule.read', 'web', '2023-11-30 01:16:12', '2023-11-30 01:16:12'),
(69, 'hrd.internal-rules.pdf', 'web', '2023-11-30 06:52:44', '2023-11-30 06:52:44'),
(70, 'hrd.calendar.read', 'web', '2023-12-04 07:23:18', '2023-12-04 07:23:18'),
(71, 'hrd.master.leave.read', 'web', '2023-12-06 01:32:04', '2023-12-06 01:32:04'),
(72, 'hrd.medical-record.reguler.upload.excel', 'web', '2023-12-08 03:41:32', '2023-12-08 03:41:32'),
(73, 'hrd.medical-record.ireguler.surat-pelaksanaan-mcu', 'web', '2023-12-08 03:48:28', '2023-12-08 03:48:28'),
(74, 'hrd.calendar.template.create', 'web', '2023-12-18 09:31:00', '2023-12-18 09:31:00'),
(75, 'hrd.calendar.template.update', 'web', '2023-12-18 09:31:13', '2023-12-18 09:31:13'),
(76, 'hrd.calendar.template.upload', 'web', '2023-12-18 09:31:35', '2023-12-18 09:31:35'),
(77, 'hrd.calendar.template.view', 'web', '2023-12-18 09:32:18', '2023-12-18 09:32:18'),
(78, 'hrd.calendar.event.create', 'web', '2023-12-18 09:32:41', '2023-12-18 09:32:41'),
(79, 'hrd.calendar.event.update', 'web', '2023-12-18 09:32:53', '2023-12-18 09:32:53'),
(80, 'hrd.calendar.event.delete', 'web', '2023-12-18 09:33:12', '2023-12-18 09:33:12'),
(81, 'hrd.calendar.template.detail', 'web', '2023-12-18 09:33:57', '2023-12-18 09:33:57'),
(82, 'hrd.calendar.download', 'web', '2023-12-19 03:59:41', '2023-12-19 03:59:41'),
(83, 'hrd.medical-record.reguler.high-risk.excel', 'web', '2023-12-20 04:45:32', '2023-12-20 04:45:32'),
(84, 'hrd.pkb.read', 'web', '2023-12-28 06:17:12', '2023-12-28 06:17:12'),
(85, 'administrator.menu.user', 'web', '2024-01-17 04:18:12', '2024-01-17 04:18:12'),
(86, 'emp.pkb.read', 'web', '2024-03-06 03:30:43', '2024-03-06 03:30:43'),
(87, 'hrd.benefit.read', 'web', '2024-03-07 04:32:23', '2024-03-07 04:32:23'),
(88, 'emp.benefit.read', 'web', '2024-03-27 00:39:43', '2024-03-27 00:39:43'),
(89, 'hrd.training.read', 'web', '2024-04-17 03:47:13', '2024-04-17 03:47:13'),
(90, 'hrd.master.room.read', 'web', '2024-04-23 06:26:00', '2024-04-23 06:26:00'),
(91, 'hrd.jobdesk.read', 'web', '2024-05-22 07:49:21', '2024-05-22 07:49:21'),
(92, 'hrd.internal-event.read', 'web', '2024-05-22 07:52:42', '2024-05-22 07:52:42'),
(93, 'hrd.pkb.create', 'web', '2024-05-22 07:56:30', '2024-05-22 07:56:30'),
(94, 'hrd.pkb.update', 'web', '2024-05-22 07:56:51', '2024-05-22 07:56:51'),
(95, 'hrd.pkb.pdf', 'web', '2024-05-22 07:57:05', '2024-05-22 07:57:05'),
(96, 'hrd.benefit.create', 'web', '2024-05-22 08:01:21', '2024-05-22 08:01:21'),
(97, 'hrd.benefit.setting', 'web', '2024-05-22 08:01:36', '2024-05-22 08:01:36'),
(98, 'hrd.benefit.delete', 'web', '2024-05-22 08:01:48', '2024-05-22 08:01:48'),
(99, 'emp.booking-room.read', 'web', '2024-05-30 03:23:25', '2024-05-30 03:23:25'),
(100, 'hrd.home.read', 'web', '2024-06-06 02:24:12', '2024-06-06 02:24:12'),
(101, 'hrd.booking-room.read', 'web', '2024-06-06 03:22:11', '2024-06-06 03:22:11'),
(102, 'emp.internal-rule.download', 'web', '2024-06-07 09:41:31', '2024-06-07 09:41:31'),
(103, 'emp.internal-rule.pdf', 'web', '2024-06-07 09:41:47', '2024-06-07 09:41:47'),
(104, 'hrd.news-and-event.read', 'web', '2024-06-11 03:58:35', '2024-06-11 03:58:35'),
(105, 'hrd.news-and-event.create', 'web', '2024-06-11 04:24:03', '2024-06-11 04:24:03'),
(106, 'hrd.menu.clinic', 'web', '2024-07-17 08:41:17', '2024-07-17 08:41:17'),
(107, 'hrd.master.drug.read', 'web', '2024-07-18 06:46:14', '2024-07-18 06:46:14'),
(108, 'hrd.clinic.masuk.read', 'web', '2024-07-29 03:16:40', '2024-07-29 03:16:40'),
(110, 'hrd.clinic.keluar.read', 'web', '2024-07-29 08:44:24', '2024-07-29 08:44:24'),
(111, 'security.guest.read', 'web', '2024-09-06 01:16:25', '2024-09-06 03:23:14'),
(112, 'security.guest.update', 'web', '2024-09-06 03:23:25', '2024-09-06 03:23:25'),
(113, 'hrd.clinic.opname.read', 'web', '2024-09-17 09:42:50', '2024-09-17 09:42:50'),
(114, 'hrd.clinic.patient.read', 'web', '2024-09-20 08:10:37', '2024-09-20 08:10:37'),
(115, 'hrd.clinic.patient.delete', 'web', '2024-09-24 01:12:48', '2024-09-24 01:12:48'),
(116, 'hrd.clinic.masuk.delete', 'web', '2024-09-24 01:15:38', '2024-09-24 01:15:38'),
(117, 'hrd.clinic.keluar.delete', 'web', '2024-09-24 01:17:00', '2024-09-24 01:17:00'),
(118, 'hrd.clinic.stock.read', 'web', '2024-09-24 03:36:12', '2024-09-24 03:36:12'),
(119, 'hrd.menu.clinic.patient', 'web', '2024-09-24 04:53:41', '2024-09-24 04:53:41'),
(120, 'hrd.menu.clinic.medicine', 'web', '2024-09-24 04:53:52', '2024-09-24 04:53:52'),
(121, 'hrd.menu.profile', 'web', '2024-10-25 01:51:12', '2024-10-25 01:51:12'),
(122, 'hrd.clinic.masuk.create', 'web', '2024-10-25 04:03:07', '2024-10-25 04:03:07'),
(123, 'hrd.clinic.keluar.create', 'web', '2024-10-25 04:03:23', '2024-10-25 04:03:23'),
(124, 'hrd.clinic.opname.create', 'web', '2024-10-25 04:03:40', '2024-10-25 04:03:40'),
(125, 'hrd.clinic.patient.create', 'web', '2024-10-30 04:06:22', '2024-10-30 04:06:22'),
(126, 'hrd.clinic.patient.resume.excel', 'web', '2024-10-30 04:07:52', '2024-10-30 04:07:52'),
(127, 'hrd.menu.training', 'web', '2024-11-01 01:41:59', '2024-11-01 01:41:59'),
(128, 'hrd.training.record', 'web', '2024-11-01 01:42:34', '2024-11-01 01:42:34'),
(129, 'hrd.training.calendar', 'web', '2024-11-01 01:42:51', '2024-11-01 01:42:51'),
(130, 'hrd.training.ptt', 'web', '2024-11-01 01:43:05', '2024-11-01 01:43:05'),
(131, 'hrd.training.pti', 'web', '2024-11-01 01:43:25', '2024-11-01 01:43:25'),
(132, 'hrd.training.periode', 'web', '2024-11-01 01:43:39', '2024-11-01 01:43:39'),
(133, 'hrd.training.laporan', 'web', '2024-11-01 01:43:55', '2024-11-01 01:43:55'),
(134, 'hrd.training.record.detail', 'web', '2024-11-01 01:51:07', '2024-11-01 01:51:07'),
(135, 'hrd.training.record.detail.edit', 'web', '2024-11-01 02:00:58', '2024-11-01 02:00:58'),
(136, 'hrd.training.record.detail.sertifikat', 'web', '2024-11-01 02:01:26', '2024-11-01 02:01:26'),
(137, 'hrd.training.ptt.verification', 'web', '2024-11-01 02:27:49', '2024-11-01 02:27:49'),
(138, 'hrd.training.ptt.approve.direktur-produksi', 'web', '2024-11-01 02:30:00', '2024-11-01 02:30:00'),
(139, 'hrd.training.ptt.approve.presiden-direktur', 'web', '2024-11-01 02:34:03', '2024-11-01 02:34:03'),
(140, 'hrd.training.ptt.notification.direktur-produksi', 'web', '2024-11-01 03:38:56', '2024-11-01 03:38:56'),
(141, 'hrd.employee.detail', 'web', '2024-11-01 03:48:17', '2024-11-01 03:48:17'),
(142, 'hrd.training.ptt.schedule', 'web', '2024-11-01 04:13:20', '2024-11-01 04:13:20'),
(143, 'emp.training.read', 'web', '2024-11-22 05:52:59', '2024-11-22 05:52:59'),
(144, 'hrd.training.proggress', 'web', '2024-11-25 02:26:37', '2024-11-25 02:26:37'),
(145, 'about.read', 'web', '2025-07-07 06:31:19', '2025-07-07 06:31:30'),
(146, 'about.editor', 'web', '2025-07-07 06:52:55', '2025-07-07 06:52:55'),
(147, 'hrd.master.appraisal.create', 'web', '2025-08-04 03:12:01', '2025-08-04 03:12:01'),
(148, 'hrd.master.appraisal.update', 'web', '2025-08-04 03:12:18', '2025-08-04 03:12:18'),
(150, 'administrator.user.read', 'web', '2025-08-14 00:52:47', '2025-08-14 00:52:47'),
(151, 'emp.evaluation.read', 'web', '2025-08-14 01:25:10', '2025-08-14 01:25:10'),
(152, 'hrd.evaluation.read', 'web', '2025-08-14 07:28:18', '2025-08-14 07:28:18'),
(153, 'hrd.master.building.read', 'web', '2025-08-21 03:20:38', '2025-08-21 03:20:38'),
(154, 'hrd.master.building.create', 'web', '2025-08-21 03:20:58', '2025-08-21 03:20:58'),
(155, 'hrd.master.building.update', 'web', '2025-08-21 03:21:14', '2025-08-21 03:21:14'),
(156, 'hrd.master.line-approval.read', 'web', '2025-08-21 06:08:29', '2025-08-21 06:08:29'),
(157, 'hrd.master.line-approval.create', 'web', '2025-08-21 06:08:47', '2025-08-21 06:08:47'),
(158, 'hrd.master.line-approval.update', 'web', '2025-08-21 06:09:18', '2025-08-21 06:09:18'),
(159, 'hrd.master.line-approval.delete', 'web', '2025-08-21 06:09:41', '2025-08-21 06:09:41'),
(160, 'hrd.evaluation.note', 'web', '2025-09-11 07:46:13', '2025-09-11 07:46:13'),
(161, 'hrd.master.line-approval.export_xlsx', 'web', '2025-09-17 15:40:00', '2025-09-17 15:40:00'),
(162, 'hrd.evaluation.delete', 'web', '2025-09-17 15:42:40', '2025-09-17 15:42:40'),
(163, 'hrd.master.building.delete', 'web', '2025-09-23 15:57:12', '2025-09-23 15:57:12'),
(164, 'hrd.master.hiring.read', 'web', '2025-09-23 15:57:30', '2025-09-23 15:57:30'),
(165, 'hrd.master.hiring.create', 'web', '2025-09-23 15:57:43', '2025-09-23 15:57:43'),
(166, 'hrd.master.hiring.update', 'web', '2025-09-23 15:57:58', '2025-09-23 15:57:58'),
(167, 'hrd.master.hiring.delete', 'web', '2025-09-23 15:58:11', '2025-09-23 15:58:11'),
(168, 'hrd.recruitment.read', 'web', '2025-10-20 14:48:16', '2025-10-20 14:48:16'),
(169, 'emp.recruitment.read', 'web', '2025-10-20 14:48:36', '2025-10-20 14:48:36'),
(170, 'hrd.job-posting.delete', 'web', '2025-10-28 15:27:29', '2025-10-28 15:27:29'),
(171, 'hrd.candidate.delete', 'web', '2025-11-06 16:01:17', '2025-11-06 16:01:17'),
(172, 'hrd.selection.delete', 'web', '2025-11-19 13:22:29', '2025-11-19 13:22:29'),
(173, 'itsm.asset-type.read', 'web', '2026-03-02 14:19:03', '2026-03-02 14:19:03'),
(174, 'itsm.asset-type.create', 'web', '2026-03-02 14:19:13', '2026-03-02 14:19:13'),
(175, 'itsm.asset-type.update', 'web', '2026-03-02 14:19:24', '2026-03-02 14:19:24'),
(176, 'itsm.asset-type.delete', 'web', '2026-03-02 14:19:36', '2026-03-02 14:19:36'),
(177, 'itsm.it-asset.read', 'web', '2026-03-02 14:20:55', '2026-03-02 14:35:42'),
(178, 'itsm.it-asset.create', 'web', '2026-03-02 14:21:14', '2026-03-02 14:34:56'),
(179, 'itsm.it-asset.update', 'web', '2026-03-02 14:21:24', '2026-03-02 14:35:04'),
(180, 'itsm.it-asset.detail', 'web', '2026-03-02 14:21:35', '2026-03-02 14:35:14'),
(181, 'itsm.it-asset.movement', 'web', '2026-03-02 14:21:49', '2026-03-02 14:35:25'),
(182, 'itsm.asset-disposal.read', 'web', '2026-03-02 14:22:14', '2026-03-02 14:22:14'),
(183, 'itsm.asset-disposal.create', 'web', '2026-03-02 14:22:27', '2026-03-02 14:22:27'),
(184, 'itsm.asset-disposal.update', 'web', '2026-03-02 14:22:41', '2026-03-02 14:22:41'),
(185, 'itsm.asset-disposal.delete', 'web', '2026-03-02 14:22:51', '2026-03-02 14:22:51'),
(186, 'itsm.asset-disposal.detail', 'web', '2026-03-02 14:23:01', '2026-03-02 14:23:01'),
(188, 'itsm.knowledge-base.read', 'web', '2026-04-24 14:10:27', '2026-04-24 14:10:27'),
(189, 'itsm.knowledge-base.create', 'web', '2026-04-24 14:10:45', '2026-04-24 14:10:45'),
(190, 'itsm.knowledge-base.update', 'web', '2026-04-24 14:11:12', '2026-04-24 14:11:12'),
(191, 'itsm.knowledge-base.delete', 'web', '2026-04-24 14:11:26', '2026-04-24 14:11:26'),
(192, 'itsm.service-desk.read', 'web', '2026-04-24 14:16:33', '2026-04-24 14:16:33'),
(193, 'itsm.service-desk.analyze', 'web', '2026-04-24 14:16:53', '2026-04-24 14:16:53'),
(194, 'itsm.service-desk.request-change', 'web', '2026-04-24 14:17:02', '2026-04-24 14:17:02'),
(195, 'itsm.service-desk.update', 'web', '2026-04-24 14:17:10', '2026-04-24 14:17:10'),
(196, 'itsm.service-change.read', 'web', '2026-04-24 14:17:45', '2026-04-24 14:17:45'),
(197, 'itsm.priority.read', 'web', '2026-04-24 14:17:53', '2026-04-24 14:17:53'),
(198, 'itsm.priority.update', 'web', '2026-04-24 14:18:04', '2026-04-24 14:18:04'),
(199, 'itsm.priority.create', 'web', '2026-04-24 14:18:17', '2026-04-24 14:18:17'),
(200, 'itsm.priority.delete', 'web', '2026-04-24 14:18:36', '2026-04-24 14:18:36'),
(201, 'emp.service-desk.read', 'web', '2026-04-24 14:18:51', '2026-04-24 14:18:51'),
(202, 'emp.service-desk.create', 'web', '2026-04-24 14:19:01', '2026-04-24 14:19:01'),
(203, 'emp.service-desk.cancel', 'web', '2026-04-24 14:19:16', '2026-04-24 14:19:16'),
(204, 'itsm.it-asset.delete', 'web', '2026-04-28 09:10:14', '2026-04-28 09:10:14'),
(205, 'hrd.master.contract.read', 'web', '2026-05-11 16:47:38', '2026-05-11 16:47:38'),
(206, 'hrd.master.contract.create', 'web', '2026-05-11 16:47:48', '2026-05-11 16:47:48'),
(207, 'hrd.master.contract.update', 'web', '2026-05-11 16:47:57', '2026-05-11 16:47:57'),
(208, 'hrd.master.contract.delete', 'web', '2026-05-11 16:48:06', '2026-05-11 16:48:06'),
(209, 'hrd.attendance.menu', 'web', '2026-06-18 14:26:25', '2026-06-18 14:26:25'),
(210, 'hrd.employee-attendance.read', 'web', '2026-06-18 14:27:18', '2026-06-18 14:27:18'),
(211, 'hrd.group-employee-workhour.read', 'web', '2026-06-18 14:44:58', '2026-06-18 14:44:58'),
(212, 'hrd.attendance-permit.read', 'web', '2026-06-18 14:45:26', '2026-06-18 14:45:26'),
(213, 'hrd.attendance.menu.master', 'web', '2026-06-18 14:45:51', '2026-06-18 14:45:51'),
(215, 'hrd.attendance-calendar.read', 'web', '2026-06-18 14:48:59', '2026-06-18 14:48:59'),
(216, 'hrd.workhour.read', 'web', '2026-06-18 14:54:36', '2026-06-18 14:54:36'),
(217, 'hrd.positioning.read', 'web', '2026-06-18 14:54:57', '2026-06-18 14:54:57'),
(218, 'hrd.leave-setting.read', 'web', '2026-06-18 14:55:21', '2026-06-18 14:55:21'),
(219, 'hrd.business-trip-allowance.read', 'web', '2026-06-18 14:55:36', '2026-06-18 14:55:36'),
(221, 'hrd.group-employee-workhour.create', 'web', '2026-06-18 15:24:51', '2026-06-18 15:24:51'),
(222, 'group-employee-workhour.edit', 'web', '2026-06-18 15:25:07', '2026-06-18 15:25:07'),
(223, 'hrd.group-employee-workhour.destroy', 'web', '2026-06-18 15:26:15', '2026-06-18 15:26:15'),
(224, 'hrd.employee-attendance.update', 'web', '2026-06-18 15:26:51', '2026-06-18 15:26:51'),
(225, 'hrd.employee-leave.leave-balance-create', 'web', '2026-06-18 15:27:09', '2026-06-18 15:27:09'),
(226, 'hrd.employee-leave.leave-balance-destroy', 'web', '2026-06-18 15:27:23', '2026-06-18 15:27:23'),
(227, 'hrd.group-employee-workhour.edit', 'web', '2026-06-18 15:32:35', '2026-06-18 15:32:35'),
(228, 'emp.attendance-permit.read', 'web', '2026-06-19 07:51:47', '2026-06-19 07:51:47'),
(229, 'emp.late.read', 'web', '2026-06-19 07:52:08', '2026-06-19 07:52:08'),
(230, 'emp.overtime.read', 'web', '2026-06-19 07:52:26', '2026-06-19 07:52:26'),
(231, 'emp.business-trip.read', 'web', '2026-06-19 07:52:43', '2026-06-19 07:52:43'),
(232, 'emp.employee-leave.read', 'web', '2026-06-19 07:52:59', '2026-06-19 07:52:59'),
(233, 'mobile.medical-checkup', 'web', '2026-06-19 09:07:07', '2026-06-19 09:07:07'),
(234, 'mobile.pkb', 'web', '2026-06-19 09:07:28', '2026-06-19 09:07:28'),
(235, 'mobile.booking-room', 'web', '2026-06-19 09:07:46', '2026-06-19 09:07:46'),
(236, 'mobile.it-service-desk', 'web', '2026-06-19 09:09:14', '2026-06-19 09:09:14'),
(237, 'mobile.pengajuan-cuti', 'web', '2026-06-19 09:09:31', '2026-06-19 09:09:31'),
(238, 'mobile.izin-absensi', 'web', '2026-06-19 09:09:46', '2026-06-19 09:09:46'),
(239, 'mobile.pengajuan-lembur', 'web', '2026-06-19 09:10:00', '2026-06-19 09:10:00'),
(240, 'mobile.keterlambatan', 'web', '2026-06-19 09:10:14', '2026-06-19 09:10:14'),
(241, 'mobile.perjalanan-dinas', 'web', '2026-06-19 09:10:26', '2026-06-19 09:10:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permission_internal_rules`
--

CREATE TABLE `permission_internal_rules` (
  `id_internal_rule` int DEFAULT NULL,
  `id_dept` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_employee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `benefit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_nominal` bigint DEFAULT NULL,
  `value_textual` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pkb`
--

CREATE TABLE `pkb` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_berlaku` date DEFAULT NULL,
  `tgl_berakhir` date DEFAULT NULL,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_pkb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestock_drug`
--

CREATE TABLE `prestock_drug` (
  `id` bigint UNSIGNED NOT NULL,
  `id_drug` int DEFAULT NULL,
  `nama_drug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jml_drug` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `priority_metrics`
--

CREATE TABLE `priority_metrics` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `definition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `qr_code_fkt`
--

CREATE TABLE `qr_code_fkt` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_fkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_approval` datetime DEFAULT NULL,
  `type` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `qr_code_fpkt`
--

CREATE TABLE `qr_code_fpkt` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fpkt` int DEFAULT NULL,
  `kode_fpkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_approval` datetime NOT NULL,
  `type` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `risk_registers`
--

CREATE TABLE `risk_registers` (
  `id` bigint UNSIGNED NOT NULL,
  `risk_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `impact` int DEFAULT NULL,
  `probability` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `mitigation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contingency_plan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super User', 'web', '2024-10-30 09:43:44', '2024-10-30 09:43:44'),
(2, 'HRD', 'web', '2024-10-30 09:43:44', '2024-10-30 09:43:44'),
(3, 'GA', 'web', '2023-09-27 09:01:25', '2023-09-27 09:01:25'),
(4, 'Employee', 'web', '2023-11-10 14:01:13', '2023-11-10 14:01:14'),
(36, 'Doctor', 'web', '2023-12-08 02:36:57', '2023-12-08 02:36:57'),
(37, 'LCOD', 'web', '2024-05-22 07:39:33', '2024-05-22 07:39:33'),
(39, 'HQ Office', 'web', '2024-06-03 06:53:12', '2024-06-03 06:53:12'),
(48, 'Security', 'web', '2024-09-25 02:41:56', '2024-09-25 02:41:56'),
(49, 'President Director', 'web', '2024-10-25 01:14:44', '2025-07-12 02:31:19'),
(51, 'Production Director', 'web', '2024-10-31 06:35:07', '2024-10-31 06:35:07'),
(55, 'BOD', 'web', '2025-09-24 08:58:45', '2025-09-24 08:58:45'),
(56, 'Operator', 'web', '2025-11-26 13:22:11', '2025-11-26 13:22:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(11, 1),
(12, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(87, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(100, 1),
(101, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(140, 1),
(141, 1),
(142, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(148, 1),
(150, 1),
(152, 1),
(153, 1),
(154, 1),
(155, 1),
(156, 1),
(157, 1),
(158, 1),
(159, 1),
(160, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(166, 1),
(167, 1),
(168, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(200, 1),
(201, 1),
(202, 1),
(203, 1),
(204, 1),
(205, 1),
(206, 1),
(207, 1),
(208, 1),
(209, 1),
(210, 1),
(211, 1),
(212, 1),
(213, 1),
(215, 1),
(216, 1),
(217, 1),
(218, 1),
(219, 1),
(221, 1),
(222, 1),
(223, 1),
(224, 1),
(225, 1),
(226, 1),
(227, 1),
(233, 1),
(234, 1),
(235, 1),
(236, 1),
(237, 1),
(238, 1),
(239, 1),
(240, 1),
(241, 1),
(1, 2),
(8, 2),
(11, 2),
(12, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(69, 2),
(70, 2),
(71, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(84, 2),
(85, 2),
(87, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(100, 2),
(101, 2),
(121, 2),
(127, 2),
(128, 2),
(129, 2),
(130, 2),
(131, 2),
(132, 2),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(140, 2),
(141, 2),
(142, 2),
(144, 2),
(147, 2),
(148, 2),
(152, 2),
(153, 2),
(154, 2),
(155, 2),
(156, 2),
(157, 2),
(158, 2),
(159, 2),
(160, 2),
(161, 2),
(163, 2),
(164, 2),
(165, 2),
(166, 2),
(167, 2),
(168, 2),
(170, 2),
(171, 2),
(172, 2),
(209, 2),
(210, 2),
(211, 2),
(212, 2),
(213, 2),
(215, 2),
(216, 2),
(217, 2),
(218, 2),
(219, 2),
(221, 2),
(222, 2),
(223, 2),
(224, 2),
(225, 2),
(226, 2),
(227, 2),
(18, 3),
(27, 3),
(28, 3),
(29, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(42, 3),
(62, 3),
(70, 3),
(72, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(87, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(99, 3),
(100, 3),
(101, 3),
(104, 3),
(105, 3),
(106, 3),
(108, 3),
(110, 3),
(113, 3),
(114, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
(126, 3),
(47, 4),
(48, 4),
(49, 4),
(50, 4),
(86, 4),
(151, 4),
(228, 4),
(229, 4),
(230, 4),
(231, 4),
(232, 4),
(18, 36),
(106, 36),
(108, 36),
(110, 36),
(113, 36),
(114, 36),
(118, 36),
(119, 36),
(120, 36),
(122, 36),
(123, 36),
(124, 36),
(125, 36),
(126, 36),
(18, 37),
(62, 37),
(67, 37),
(69, 37),
(84, 37),
(87, 37),
(95, 37),
(121, 37),
(47, 39),
(48, 39),
(49, 39),
(50, 39),
(86, 39),
(99, 39),
(143, 39),
(151, 39),
(201, 39),
(202, 39),
(203, 39),
(18, 48),
(101, 48),
(111, 48),
(112, 48),
(8, 49),
(18, 49),
(27, 49),
(28, 49),
(29, 49),
(34, 49),
(36, 49),
(42, 49),
(62, 49),
(67, 49),
(69, 49),
(70, 49),
(77, 49),
(81, 49),
(84, 49),
(86, 49),
(87, 49),
(89, 49),
(92, 49),
(95, 49),
(100, 49),
(106, 49),
(114, 49),
(119, 49),
(121, 49),
(127, 49),
(131, 49),
(133, 49),
(8, 51),
(18, 51),
(27, 51),
(28, 51),
(29, 51),
(34, 51),
(62, 51),
(67, 51),
(69, 51),
(70, 51),
(77, 51),
(81, 51),
(84, 51),
(87, 51),
(89, 51),
(92, 51),
(95, 51),
(100, 51),
(106, 51),
(111, 51),
(114, 51),
(119, 51),
(121, 51),
(127, 51),
(131, 51),
(133, 51),
(47, 55),
(48, 55),
(49, 55),
(50, 55),
(68, 55),
(86, 55),
(88, 55),
(99, 55),
(102, 55),
(143, 55),
(151, 55),
(169, 55),
(47, 56),
(48, 56),
(49, 56),
(50, 56),
(86, 56);

-- --------------------------------------------------------

--
-- Struktur dari tabel `security_account`
--

CREATE TABLE `security_account` (
  `id` bigint UNSIGNED NOT NULL,
  `id_security` int DEFAULT NULL,
  `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `security_guest`
--

CREATE TABLE `security_guest` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat_pribadi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `nomor_visitor` text,
  `nomor_kartu_identitas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `perusahaan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `alamat_perusahaan` text,
  `nama_pic` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `id_employee` bigint UNSIGNED DEFAULT NULL,
  `resiko_kesehatan` enum('rendah','sedang','tinggi') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `suhu` decimal(5,2) DEFAULT NULL,
  `q1` tinyint(1) DEFAULT NULL,
  `q2` tinyint(1) DEFAULT NULL,
  `q3` tinyint(1) DEFAULT NULL,
  `q4` tinyint(1) DEFAULT NULL,
  `q5` tinyint(1) DEFAULT NULL,
  `q6` tinyint(1) DEFAULT NULL,
  `lama_kunjungan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `tujuan_kunjungan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `jenis_kendaraan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `muatan_kendaraan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `nomor_polisi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `waktu_bertemu` datetime DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `selection_process`
--

CREATE TABLE `selection_process` (
  `id` bigint UNSIGNED NOT NULL,
  `requisition_id` bigint UNSIGNED NOT NULL,
  `requisition_hiring_step_id` bigint UNSIGNED NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0=Draft; 1=Release; 2=Done;',
  `noted` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `selection_process_assessments`
--

CREATE TABLE `selection_process_assessments` (
  `id` bigint UNSIGNED NOT NULL,
  `sel_process_candidate_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `result_status` tinyint NOT NULL DEFAULT '0' COMMENT '0=Scheduled; 1=Passed; 2=Failed',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `selection_process_candidates`
--

CREATE TABLE `selection_process_candidates` (
  `id` bigint UNSIGNED NOT NULL,
  `candidate_id` bigint UNSIGNED NOT NULL,
  `selection_process_id` bigint UNSIGNED NOT NULL,
  `email_notification_sent_at` datetime DEFAULT NULL,
  `is_present` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Absent; 1=Present',
  `result_status` tinyint NOT NULL DEFAULT '0' COMMENT '0=Scheduled; 1=Passed; 2=Failed; 3=Done',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `selection_process_employees`
--

CREATE TABLE `selection_process_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `selection_process_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_catalogs`
--

CREATE TABLE `service_catalogs` (
  `id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_catalog` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_changes`
--

CREATE TABLE `service_changes` (
  `id` bigint UNSIGNED NOT NULL,
  `change_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_ticket_id` bigint UNSIGNED NOT NULL,
  `it_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `change_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'proposed',
  `done_at` timestamp NULL DEFAULT NULL,
  `planned_start` datetime DEFAULT NULL,
  `planned_end` datetime DEFAULT NULL,
  `actual_start` datetime DEFAULT NULL,
  `actual_end` datetime DEFAULT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `proposer_id` bigint UNSIGNED NOT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `proposed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_status_histories`
--

CREATE TABLE `service_status_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `from_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_ticket_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL COMMENT 'the person that interact with this record',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `started_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_tickets`
--

CREATE TABLE `service_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `no_ticket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `report_for` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitter_id` bigint UNSIGNED NOT NULL,
  `report_for_id` bigint UNSIGNED DEFAULT NULL,
  `employee_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catalog` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impact` int DEFAULT NULL,
  `urgency` int DEFAULT NULL,
  `scope` int DEFAULT NULL,
  `risk_register_id` bigint UNSIGNED DEFAULT NULL,
  `risk_register_score` int DEFAULT NULL,
  `itsm_priority_id` bigint UNSIGNED DEFAULT NULL,
  `visibility` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `supervisor_id` bigint UNSIGNED DEFAULT NULL,
  `dept_head_id` bigint UNSIGNED DEFAULT NULL,
  `supervisor_approval` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_approval_at` datetime DEFAULT NULL,
  `supervisor_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept_head_approval` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept_head_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept_head_approval_at` datetime DEFAULT NULL,
  `dept_head_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept_head_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept_head_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `it_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Internal note for IT team, invisible to user',
  `submitted_for_approval_at` datetime DEFAULT NULL COMMENT 'The timestamp when IT team make the request approval action, used for tracking the date of approval request and signature generation',
  `current_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_release` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `it_handler_id` bigint UNSIGNED DEFAULT NULL,
  `it_handler_department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `it_handler_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `it_handler_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_ticket_assets`
--

CREATE TABLE `service_ticket_assets` (
  `id` bigint UNSIGNED NOT NULL,
  `service_ticket_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `it_asset_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_ticket_ccs`
--

CREATE TABLE `service_ticket_ccs` (
  `id` bigint UNSIGNED NOT NULL,
  `service_ticket_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_ticket_media`
--

CREATE TABLE `service_ticket_media` (
  `id` bigint UNSIGNED NOT NULL,
  `service_ticket_message_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_ticket_messages`
--

CREATE TABLE `service_ticket_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `service_ticket_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_medical`
--

CREATE TABLE `template_medical` (
  `id` bigint UNSIGNED NOT NULL,
  `id_vendor` int NOT NULL,
  `total_employee` int DEFAULT NULL,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `template_medical`
--

INSERT INTO `template_medical` (`id`, `id_vendor`, `total_employee`, `tanggal_awal`, `tanggal_akhir`, `created_at`, `updated_at`) VALUES
(1, 1, 496, '2026-02-01', '2026-02-28', '2026-02-13 15:08:32', '2026-02-13 15:08:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `temp_calendar`
--

CREATE TABLE `temp_calendar` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_calendar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_evaluasi`
--

CREATE TABLE `training_evaluasi` (
  `id` bigint UNSIGNED NOT NULL,
  `id_training_record` int NOT NULL,
  `dt_1` int DEFAULT NULL,
  `dt_2` int NOT NULL,
  `dt_3` int NOT NULL,
  `dt_4` int NOT NULL,
  `dt_5` int NOT NULL,
  `fap_1` int NOT NULL,
  `fap_2` int NOT NULL,
  `fap_3` int NOT NULL,
  `fap_4` int NOT NULL,
  `trainer_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `et_1` int NOT NULL,
  `et_2` int NOT NULL,
  `et_3` int NOT NULL,
  `et_4` int NOT NULL,
  `trainer_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `et_5` int DEFAULT NULL,
  `et_6` int DEFAULT NULL,
  `et_7` int DEFAULT NULL,
  `et_8` int DEFAULT NULL,
  `trainer_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `et_9` int DEFAULT NULL,
  `et_10` int DEFAULT NULL,
  `et_11` int DEFAULT NULL,
  `et_12` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_fkt`
--

CREATE TABLE `training_fkt` (
  `id` bigint UNSIGNED NOT NULL,
  `id_pemohon` int DEFAULT NULL,
  `dept_pemohon` int DEFAULT NULL,
  `date_pemohon` datetime DEFAULT NULL,
  `tahun_usulan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_pelaksanaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_peserta` int DEFAULT NULL,
  `kode_judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pelatihan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sifat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bulan_pelaksanaan` int DEFAULT NULL,
  `id_vendor` int DEFAULT NULL,
  `nama_vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya_fkt` bigint DEFAULT NULL,
  `penginapan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_checker` int DEFAULT NULL,
  `date_checker` datetime DEFAULT NULL,
  `id_verified` int DEFAULT NULL,
  `date_verified` datetime DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_fpkt`
--

CREATE TABLE `training_fpkt` (
  `id` bigint UNSIGNED NOT NULL,
  `id_fkt` int DEFAULT NULL,
  `kode_fpkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latar_belakang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `biaya_fpkt` bigint DEFAULT NULL,
  `id_vendor` int DEFAULT NULL,
  `nama_vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_pelaksanaan` date DEFAULT NULL,
  `kode_judul_fpkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_fpkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_fpkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tujuan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kompetensi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `skill` json DEFAULT NULL,
  `level_peserta` json DEFAULT NULL,
  `level_atasan` json DEFAULT NULL,
  `level_rata` json DEFAULT NULL,
  `level_kebutuhan` json DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `analisa_satu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `analisa_dua` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `analisa_tiga` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_pemohon` int DEFAULT NULL,
  `date_pemohon` datetime DEFAULT NULL,
  `id_peserta` int DEFAULT NULL,
  `date_peserta` datetime DEFAULT NULL,
  `id_atasan` int DEFAULT NULL,
  `date_atasan` datetime DEFAULT NULL,
  `id_dept_head` int DEFAULT NULL,
  `date_dept_head` datetime DEFAULT NULL,
  `id_bod1` int DEFAULT NULL,
  `date_bod1` datetime DEFAULT NULL,
  `id_bod2` int DEFAULT NULL,
  `date_bod2` datetime DEFAULT NULL,
  `id_hrd` int DEFAULT NULL,
  `date_hrd` datetime DEFAULT NULL,
  `alasan_pti` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_periode`
--

CREATE TABLE `training_periode` (
  `id` bigint UNSIGNED NOT NULL,
  `periode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_record`
--

CREATE TABLE `training_record` (
  `id` bigint UNSIGNED NOT NULL,
  `id_employee` int DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `id_vendor` int DEFAULT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sertifikat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `materi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `id_fpkt` int DEFAULT NULL,
  `id_fkt` int DEFAULT NULL,
  `kode_fkt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_laporan` date DEFAULT NULL,
  `isi_pelatihan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dipelajari` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `implementasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hasil` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ttd_presiden` int DEFAULT NULL,
  `tgl_ttd_presiden` datetime DEFAULT NULL,
  `ttd_direktur` int DEFAULT NULL,
  `tgl_ttd_direktur` datetime DEFAULT NULL,
  `ttd_general_manager` int DEFAULT NULL,
  `tgl_ttd_general_manager` datetime DEFAULT NULL,
  `ttd_manager` int DEFAULT NULL,
  `tgl_ttd_manager` datetime DEFAULT NULL,
  `ttd_atasan` int DEFAULT NULL,
  `tgl_ttd_atasan` datetime DEFAULT NULL,
  `ttd_hrd_ga_gm` int DEFAULT NULL,
  `tgl_ttd_hrd_ga_gm` datetime DEFAULT NULL,
  `ttd_pic` int DEFAULT NULL,
  `tgl_ttd_pic` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `training_status`
--

CREATE TABLE `training_status` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `bod` int UNSIGNED DEFAULT NULL,
  `count_log` int DEFAULT NULL,
  `disclaimer` int DEFAULT NULL,
  `last_update_password` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `biometric_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Key used for biometric fingerprint authentication',
  `biometric_device_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Device ID for biometric binding'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `status`, `employee_id`, `bod`, `count_log`, `disclaimer`, `last_update_password`, `created_at`, `updated_at`, `biometric_key`, `biometric_device_id`) VALUES
(17, 'DUMMY', 'dummy@gmail.com', NULL, '$2y$10$giQ0b0LBkJKxS3x537vi3.8HGDQUOICa.3cpTbbN9jj.Y1nHXkpDS', NULL, 1, 1075, NULL, 5, 0, '2026-02-05', '2026-02-05 11:56:20', '2026-02-27 07:57:22', '', ''),
(18, 'FERDISAPUTRO', 'e41231827@student.polije.ac.id', NULL, '$2y$10$n4EjCS1UEvNzE1nO8hMFpeg42GbudFg.2fGi63eyeiZJqCVjFcZLS', NULL, 1, 1093, NULL, 35, 1, '2026-04-24', '2026-04-24 14:35:19', '2026-06-19 08:58:28', 'KOkbDW22iJtOy1oaSJUCb6RxnSa09wUpiGADd+H/L7k=', 'd9caadc5-edf5-4e48-9501-accb216a04d0'),
(19, 'FERDIAN SAPUTRO', 'test1@gmail.com', NULL, '$2y$10$pK8Wx9nblYz74T0741spEe6/a7Ei62ldzUKvw27zeWmVsS7AD2Oo.', NULL, 1, 1092, NULL, 4, 1, '2026-06-19', '2026-06-18 16:34:08', '2026-06-19 12:44:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_log`
--

CREATE TABLE `user_log` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `workhour_detail`
--

CREATE TABLE `workhour_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `workhour_id` bigint UNSIGNED NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_in` time NOT NULL,
  `work_out` time NOT NULL,
  `break_duration` int NOT NULL DEFAULT '0',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `workhour_detail`
--

INSERT INTO `workhour_detail` (`id`, `workhour_id`, `day`, `work_in`, `work_out`, `break_duration`, `notes`, `created_at`, `updated_at`) VALUES
(6, 2, 'Monday', '07:00:00', '15:45:00', 45, NULL, '2026-03-26 02:26:20', '2026-03-26 02:26:20'),
(7, 2, 'Tuesday', '07:00:00', '15:45:00', 45, NULL, '2026-03-26 02:26:20', '2026-03-26 02:26:20'),
(8, 2, 'Wednesday', '07:00:00', '15:45:00', 45, NULL, '2026-03-26 02:26:20', '2026-03-26 02:26:20'),
(9, 2, 'Thursday', '07:00:00', '15:45:00', 45, NULL, '2026-03-26 02:26:20', '2026-03-26 02:26:20'),
(10, 2, 'Friday', '07:00:00', '16:00:00', 60, NULL, '2026-03-26 02:26:20', '2026-03-26 02:26:20'),
(16, 13, 'Monday', '07:45:00', '16:30:00', 45, NULL, '2026-03-31 08:08:44', '2026-03-31 08:08:44'),
(17, 13, 'Tuesday', '07:45:00', '16:30:00', 45, NULL, '2026-03-31 08:08:44', '2026-03-31 08:08:44'),
(18, 13, 'Wednesday', '07:45:00', '16:30:00', 45, NULL, '2026-03-31 08:08:45', '2026-03-31 08:08:45'),
(19, 13, 'Thursday', '07:45:00', '16:30:00', 45, NULL, '2026-03-31 08:08:45', '2026-03-31 08:08:45'),
(20, 13, 'Friday', '07:45:00', '16:45:00', 60, NULL, '2026-03-31 08:08:45', '2026-03-31 08:08:45'),
(21, 14, 'Monday', '07:00:00', '15:30:00', 30, NULL, '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(22, 14, 'Tuesday', '07:00:00', '15:30:00', 30, NULL, '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(23, 14, 'Wednesday', '07:00:00', '15:30:00', 30, NULL, '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(24, 14, 'Thursday', '07:00:00', '15:30:00', 30, NULL, '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(25, 14, 'Friday', '07:00:00', '15:45:00', 45, NULL, '2026-03-31 08:11:32', '2026-03-31 08:11:32'),
(41, 17, 'Monday', '07:00:00', '15:45:00', 45, NULL, '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(42, 17, 'Tuesday', '07:00:00', '15:45:00', 45, NULL, '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(43, 17, 'Wednesday', '07:00:00', '15:45:00', 45, NULL, '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(44, 17, 'Thursday', '07:00:00', '15:45:00', 45, NULL, '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(45, 17, 'Friday', '07:00:00', '16:00:00', 60, NULL, '2026-03-31 08:25:16', '2026-03-31 08:25:16'),
(51, 1, 'Monday', '07:45:00', '16:30:00', 45, NULL, '2026-04-20 01:15:16', '2026-04-20 01:15:16'),
(52, 1, 'Tuesday', '07:45:00', '16:30:00', 45, NULL, '2026-04-20 01:15:16', '2026-04-20 01:15:16'),
(53, 1, 'Wednesday', '07:45:00', '16:30:00', 45, NULL, '2026-04-20 01:15:16', '2026-04-20 01:15:16'),
(54, 1, 'Thursday', '07:45:00', '16:30:00', 45, NULL, '2026-04-20 01:15:16', '2026-04-20 01:15:16'),
(55, 1, 'Friday', '07:45:00', '16:45:00', 60, NULL, '2026-04-20 01:15:16', '2026-04-20 01:15:16'),
(56, 15, 'Monday', '07:00:00', '15:45:00', 45, NULL, '2026-05-05 08:29:56', '2026-05-05 08:29:56'),
(57, 15, 'Tuesday', '21:00:00', '05:45:00', 45, NULL, '2026-05-05 08:29:56', '2026-05-05 08:29:56'),
(58, 15, 'Wednesday', '07:00:00', '15:45:00', 45, NULL, '2026-05-05 08:29:56', '2026-05-05 08:29:56'),
(59, 15, 'Thursday', '07:00:00', '15:45:00', 45, NULL, '2026-05-05 08:29:56', '2026-05-05 08:29:56'),
(60, 15, 'Friday', '07:00:00', '16:00:00', 60, NULL, '2026-05-05 08:29:56', '2026-05-05 08:29:56'),
(61, 18, 'Monday', '21:00:00', '05:45:00', 45, NULL, '2026-05-05 08:38:45', '2026-05-05 08:38:45'),
(62, 18, 'Tuesday', '21:00:00', '05:45:00', 45, NULL, '2026-05-05 08:38:45', '2026-05-05 08:38:45'),
(63, 18, 'Wednesday', '21:00:00', '05:45:00', 45, NULL, '2026-05-05 08:38:45', '2026-05-05 08:38:45'),
(64, 18, 'Thursday', '21:00:00', '05:45:00', 45, NULL, '2026-05-05 08:38:45', '2026-05-05 08:38:45'),
(65, 18, 'Friday', '21:00:00', '06:00:00', 60, NULL, '2026-05-05 08:38:45', '2026-05-05 08:38:45'),
(66, 4, 'Monday', '07:00:00', '15:45:00', 45, NULL, '2026-06-19 09:26:09', '2026-06-19 09:26:09'),
(67, 4, 'Tuesday', '07:00:00', '15:45:00', 45, NULL, '2026-06-19 09:26:09', '2026-06-19 09:26:09'),
(68, 4, 'Wednesday', '07:00:00', '15:45:00', 45, NULL, '2026-06-19 09:26:09', '2026-06-19 09:26:09'),
(69, 4, 'Thursday', '07:00:00', '15:45:00', 45, NULL, '2026-06-19 09:26:09', '2026-06-19 09:26:09'),
(70, 4, 'Friday', '07:00:00', '16:00:00', 60, NULL, '2026-06-19 09:26:09', '2026-06-19 09:26:09');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abouts_version_unique` (`version`),
  ADD KEY `abouts_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `asset_disposals`
--
ALTER TABLE `asset_disposals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_disposals_transaction_number_unique` (`transaction_number`),
  ADD KEY `asset_disposals_requester_id_foreign` (`requester_id`);

--
-- Indeks untuk tabel `asset_disposal_items`
--
ALTER TABLE `asset_disposal_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_disposal_items_asset_disposal_id_foreign` (`asset_disposal_id`),
  ADD KEY `asset_disposal_items_it_asset_id_foreign` (`it_asset_id`);

--
-- Indeks untuk tabel `asset_disposal_logs`
--
ALTER TABLE `asset_disposal_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_disposal_logs_asset_disposal_id_foreign` (`asset_disposal_id`),
  ADD KEY `asset_disposal_logs_disposal_approval_path_id_foreign` (`disposal_approval_path_id`);

--
-- Indeks untuk tabel `asset_histories`
--
ALTER TABLE `asset_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_histories_it_asset_id_foreign` (`it_asset_id`),
  ADD KEY `asset_histories_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `asset_maintenance_items`
--
ALTER TABLE `asset_maintenance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_maintenance_items_asset_maintenance_id_foreign` (`asset_maintenance_id`),
  ADD KEY `asset_maintenance_items_it_asset_id_foreign` (`it_asset_id`);

--
-- Indeks untuk tabel `asset_types`
--
ALTER TABLE `asset_types`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_employee_id_date_index` (`employee_id`,`date`),
  ADD KEY `attendances_date_index` (`date`),
  ADD KEY `attendances_group_id_index` (`group_id`);

--
-- Indeks untuk tabel `attendance_calendars`
--
ALTER TABLE `attendance_calendars`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `attendance_permits`
--
ALTER TABLE `attendance_permits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_permits_approval_token_unique` (`approval_token`),
  ADD KEY `attendance_permits_employee_id_index` (`employee_id`);

--
-- Indeks untuk tabel `booking_record`
--
ALTER TABLE `booking_record`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `business_cancellations`
--
ALTER TABLE `business_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_cancellations_business_trip_id_foreign` (`business_trip_id`);

--
-- Indeks untuk tabel `business_cancellation_approvals`
--
ALTER TABLE `business_cancellation_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_cancellation_approvals_approval_token_unique` (`approval_token`),
  ADD KEY `business_cancellation_approvals_cancellation_id_foreign` (`cancellation_id`),
  ADD KEY `business_cancellation_approvals_approver_id_foreign` (`approver_id`);

--
-- Indeks untuk tabel `business_cancellation_items`
--
ALTER TABLE `business_cancellation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_cancellation_items_cancellation_id_foreign` (`cancellation_id`);

--
-- Indeks untuk tabel `business_cancellation_logs`
--
ALTER TABLE `business_cancellation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_cancellation_logs_business_cancellation_id_foreign` (`business_cancellation_id`),
  ADD KEY `business_cancellation_logs_approval_path_id_foreign` (`approval_path_id`);

--
-- Indeks untuk tabel `business_reports`
--
ALTER TABLE `business_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_reports_business_trip_id_foreign` (`business_trip_id`),
  ADD KEY `business_reports_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `business_report_approvals`
--
ALTER TABLE `business_report_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_report_approvals_approval_token_unique` (`approval_token`),
  ADD KEY `business_report_approvals_business_report_id_foreign` (`business_report_id`),
  ADD KEY `business_report_approvals_approver_id_foreign` (`approver_id`);

--
-- Indeks untuk tabel `business_report_attachments`
--
ALTER TABLE `business_report_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_report_attachments_business_report_item_id_foreign` (`business_report_item_id`);

--
-- Indeks untuk tabel `business_report_items`
--
ALTER TABLE `business_report_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_report_items_business_report_id_foreign` (`business_report_id`);

--
-- Indeks untuk tabel `business_report_logs`
--
ALTER TABLE `business_report_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_report_logs_business_report_id_foreign` (`business_report_id`),
  ADD KEY `business_report_logs_approval_path_id_foreign` (`approval_path_id`);

--
-- Indeks untuk tabel `business_trips`
--
ALTER TABLE `business_trips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_trips_no_document_unique` (`no_document`),
  ADD KEY `business_trips_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `business_trip_allowances`
--
ALTER TABLE `business_trip_allowances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `allowance_unique` (`level_id`,`category`,`trip_type`,`minimum_hours`);

--
-- Indeks untuk tabel `business_trip_approvals`
--
ALTER TABLE `business_trip_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_trip_approvals_approval_token_unique` (`approval_token`),
  ADD KEY `business_trip_approvals_business_trip_id_foreign` (`business_trip_id`),
  ADD KEY `business_trip_approvals_approver_id_foreign` (`approver_id`);

--
-- Indeks untuk tabel `business_trip_costs`
--
ALTER TABLE `business_trip_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_trip_costs_business_trip_id_foreign` (`business_trip_id`);

--
-- Indeks untuk tabel `business_trip_hotels`
--
ALTER TABLE `business_trip_hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_trip_hotels_business_trip_id_foreign` (`business_trip_id`);

--
-- Indeks untuk tabel `business_trip_logs`
--
ALTER TABLE `business_trip_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_trip_logs_business_trip_id_foreign` (`business_trip_id`),
  ADD KEY `business_trip_logs_approval_path_id_foreign` (`approval_path_id`);

--
-- Indeks untuk tabel `business_trip_transportations`
--
ALTER TABLE `business_trip_transportations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_trip_transportations_business_trip_id_foreign` (`business_trip_id`);

--
-- Indeks untuk tabel `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `candidate_education`
--
ALTER TABLE `candidate_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_education_candidate_id_index` (`candidate_id`);

--
-- Indeks untuk tabel `candidate_experience`
--
ALTER TABLE `candidate_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_experience_candidate_id_index` (`candidate_id`);

--
-- Indeks untuk tabel `claim_approvals`
--
ALTER TABLE `claim_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claim_approvals_approval_token_unique` (`approval_token`),
  ADD KEY `claim_approvals_claim_overtime_id_foreign` (`claim_overtime_id`),
  ADD KEY `claim_approvals_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `claim_overtimes`
--
ALTER TABLE `claim_overtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_overtimes_employee_id_index` (`employee_id`),
  ADD KEY `claim_overtimes_employee_attendance_id_index` (`employee_attendance_id`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `disposal_approval_paths`
--
ALTER TABLE `disposal_approval_paths`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disposal_approval_paths_employee_id_foreign` (`employee_id`),
  ADD KEY `disposal_approval_paths_asset_disposal_id_foreign` (`asset_disposal_id`);

--
-- Indeks untuk tabel `doctor_account`
--
ALTER TABLE `doctor_account`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `drug_keluar`
--
ALTER TABLE `drug_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `drug_masuk`
--
ALTER TABLE `drug_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `earlyout_orlates`
--
ALTER TABLE `earlyout_orlates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `earlyout_orlates_approval_token_unique` (`approval_token`),
  ADD KEY `earlyout_orlates_employee_id_index` (`employee_id`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employees_area` (`area_id`),
  ADD KEY `fk_employees_dept` (`department_id`) USING BTREE;

--
-- Indeks untuk tabel `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_attendances_business_trip_id_foreign` (`business_trip_id`),
  ADD KEY `employee_attendances_holiday_id_foreign` (`holiday_id`),
  ADD KEY `employee_attendances_employee_id_index` (`employee_id`),
  ADD KEY `employee_attendances_date_index` (`date`),
  ADD KEY `employee_attendances_group_id_index` (`group_id`),
  ADD KEY `employee_attendances_master_workhour_id_index` (`master_workhour_id`);

--
-- Indeks untuk tabel `employee_attendance_details`
--
ALTER TABLE `employee_attendance_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_attendance_details_employee_attendance_id_foreign` (`employee_attendance_id`);

--
-- Indeks untuk tabel `employee_milestones`
--
ALTER TABLE `employee_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_milestones_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `employee_requisition`
--
ALTER TABLE `employee_requisition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_requisition_no_pengajuan_unique` (`no_pengajuan`);

--
-- Indeks untuk tabel `employee_requisition_educations`
--
ALTER TABLE `employee_requisition_educations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_requisition_educations_name_unique` (`name`);

--
-- Indeks untuk tabel `employee_requisition_genders`
--
ALTER TABLE `employee_requisition_genders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_requisition_genders_requisition_id_foreign` (`requisition_id`);

--
-- Indeks untuk tabel `employee_requisition_has_educations`
--
ALTER TABLE `employee_requisition_has_educations`
  ADD PRIMARY KEY (`requisition_id`,`education_id`),
  ADD KEY `employee_requisition_has_educations_education_id_foreign` (`education_id`);

--
-- Indeks untuk tabel `employee_requisition_has_recruitment_sources`
--
ALTER TABLE `employee_requisition_has_recruitment_sources`
  ADD PRIMARY KEY (`requisition_id`,`source_id`),
  ADD KEY `req_source_source_fk` (`source_id`);

--
-- Indeks untuk tabel `employee_requisition_hiring_steps`
--
ALTER TABLE `employee_requisition_hiring_steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `req_step_order_unique` (`requisition_id`,`step_order`),
  ADD KEY `employee_requisition_hiring_steps_master_hiring_id_foreign` (`master_hiring_id`);

--
-- Indeks untuk tabel `employee_requisition_recruitment_sources`
--
ALTER TABLE `employee_requisition_recruitment_sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_requisition_recruitment_sources_name_unique` (`name`);

--
-- Indeks untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evaluations_release_id_unique` (`release_id`);

--
-- Indeks untuk tabel `evaluation_attachments`
--
ALTER TABLE `evaluation_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `evaluation_has_attachments`
--
ALTER TABLE `evaluation_has_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluation_has_attachments_evaluation_id_foreign` (`evaluation_id`),
  ADD KEY `evaluation_has_attachments_attachment_id_foreign` (`attachment_id`);

--
-- Indeks untuk tabel `evaluation_histories`
--
ALTER TABLE `evaluation_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluation_histories_evaluation_id_foreign` (`evaluation_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `group_employees`
--
ALTER TABLE `group_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_employees_employee_id_foreign` (`employee_id`),
  ADD KEY `group_employees_group_id_foreign` (`group_id`);

--
-- Indeks untuk tabel `group_employee_workhours`
--
ALTER TABLE `group_employee_workhours`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `group_workhours`
--
ALTER TABLE `group_workhours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_workhours_workhour_id_foreign` (`workhour_id`),
  ADD KEY `group_workhours_group_id_foreign` (`group_id`);

--
-- Indeks untuk tabel `hiring_step_has_employees`
--
ALTER TABLE `hiring_step_has_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hshe_unique_assignment` (`requisition_hiring_step_id`,`employee_id`),
  ADD KEY `hiring_step_has_employees_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `internal_rules`
--
ALTER TABLE `internal_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `itsm_priorities`
--
ALTER TABLE `itsm_priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `it_assets`
--
ALTER TABLE `it_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_assets_asset_code_unique` (`asset_code`),
  ADD KEY `it_assets_asset_type_id_foreign` (`asset_type_id`),
  ADD KEY `it_assets_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `job_posting`
--
ALTER TABLE `job_posting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_posting_publish_id_unique` (`publish_id`),
  ADD UNIQUE KEY `job_posting_publish_code_unique` (`publish_code`);

--
-- Indeks untuk tabel `knowledge_bases`
--
ALTER TABLE `knowledge_bases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_bases_author_id_foreign` (`author_id`);

--
-- Indeks untuk tabel `knowledge_base_media`
--
ALTER TABLE `knowledge_base_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_base_media_knowledge_base_id_foreign` (`knowledge_base_id`);

--
-- Indeks untuk tabel `knowledge_base_users`
--
ALTER TABLE `knowledge_base_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_base_users_knowledge_base_id_foreign` (`knowledge_base_id`),
  ADD KEY `knowledge_base_users_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `late_histories`
--
ALTER TABLE `late_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `late_histories_employee_attendance_id_foreign` (`employee_attendance_id`);

--
-- Indeks untuk tabel `leave_approvals`
--
ALTER TABLE `leave_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_approvals_approval_token_unique` (`approval_token`),
  ADD KEY `leave_approvals_leave_request_id_foreign` (`leave_request_id`),
  ADD KEY `leave_approvals_approver_id_foreign` (`approver_id`);

--
-- Indeks untuk tabel `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_balances_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leave_balances_employee_id_leave_type_id_index` (`employee_id`,`leave_type_id`);

--
-- Indeks untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leave_requests_employee_id_index` (`employee_id`);

--
-- Indeks untuk tabel `leave_settings`
--
ALTER TABLE `leave_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_catatan_training`
--
ALTER TABLE `log_catatan_training`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenances_it_asset_id_foreign` (`it_asset_id`),
  ADD KEY `maintenances_owner_id_foreign` (`owner_id`);

--
-- Indeks untuk tabel `master_appraisal`
--
ALTER TABLE `master_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_appraisal_position_id_foreign` (`position_id`),
  ADD KEY `master_appraisal_department_id_foreign` (`department_id`),
  ADD KEY `master_appraisal_section_id_foreign` (`section_id`);

--
-- Indeks untuk tabel `master_building`
--
ALTER TABLE `master_building`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_contract`
--
ALTER TABLE `master_contract`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_drug`
--
ALTER TABLE `master_drug`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_hiring`
--
ALTER TABLE `master_hiring`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_kota`
--
ALTER TABLE `master_kota`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_lab`
--
ALTER TABLE `master_lab`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_leave`
--
ALTER TABLE `master_leave`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_level`
--
ALTER TABLE `master_level`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_line_approval`
--
ALTER TABLE `master_line_approval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_line_approval_department_id_foreign` (`department_id`),
  ADD KEY `master_line_approval_area_id_foreign` (`area_id`),
  ADD KEY `master_line_approval_building_id_foreign` (`building_id`),
  ADD KEY `master_line_approval_position_id_foreign` (`position_id`),
  ADD KEY `master_line_approval_section_id_foreign` (`section_id`),
  ADD KEY `master_line_approval_approve_1_foreign` (`approve_1`),
  ADD KEY `master_line_approval_approve_2_foreign` (`approve_2`),
  ADD KEY `master_line_approval_approve_3_foreign` (`approve_3`),
  ADD KEY `master_line_approval_approve_4_foreign` (`approve_4`),
  ADD KEY `master_line_approval_approve_5_foreign` (`approve_5`),
  ADD KEY `master_line_approval_approve_6_foreign` (`approve_6`),
  ADD KEY `master_line_approval_approve_7_foreign` (`approve_7`),
  ADD KEY `master_line_approval_approve_8_foreign` (`approve_8`),
  ADD KEY `master_line_approval_drafter_foreign` (`drafter`);

--
-- Indeks untuk tabel `master_line_approval_employees`
--
ALTER TABLE `master_line_approval_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_line_approval_employees_line_approval_id_foreign` (`line_approval_id`),
  ADD KEY `master_line_approval_employees_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `master_position`
--
ALTER TABLE `master_position`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_positioning`
--
ALTER TABLE `master_positioning`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_provinsi`
--
ALTER TABLE `master_provinsi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_room`
--
ALTER TABLE `master_room`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_section`
--
ALTER TABLE `master_section`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_work_hour`
--
ALTER TABLE `master_work_hour`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `medical`
--
ALTER TABLE `medical`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `medical_vendor`
--
ALTER TABLE `medical_vendor`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `news_event`
--
ALTER TABLE `news_event`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `overtime_approvals`
--
ALTER TABLE `overtime_approvals`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `pkb`
--
ALTER TABLE `pkb`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `prestock_drug`
--
ALTER TABLE `prestock_drug`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `priority_metrics`
--
ALTER TABLE `priority_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `qr_code_fkt`
--
ALTER TABLE `qr_code_fkt`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `qr_code_fpkt`
--
ALTER TABLE `qr_code_fpkt`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `risk_registers`
--
ALTER TABLE `risk_registers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `risk_registers_risk_id_unique` (`risk_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `security_account`
--
ALTER TABLE `security_account`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `security_guest`
--
ALTER TABLE `security_guest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `security_guest_id_employee_fk` (`id_employee`);

--
-- Indeks untuk tabel `selection_process`
--
ALTER TABLE `selection_process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `selection_process_requisition_id_foreign` (`requisition_id`),
  ADD KEY `sel_req_step_fk` (`requisition_hiring_step_id`);

--
-- Indeks untuk tabel `selection_process_assessments`
--
ALTER TABLE `selection_process_assessments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assess_unique` (`sel_process_candidate_id`,`employee_id`),
  ADD KEY `selection_process_assessments_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `selection_process_candidates`
--
ALTER TABLE `selection_process_candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sel_cand_unique` (`candidate_id`,`selection_process_id`),
  ADD KEY `sel_cand_proc_fk` (`selection_process_id`);

--
-- Indeks untuk tabel `selection_process_employees`
--
ALTER TABLE `selection_process_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sel_emp_unique` (`selection_process_id`,`employee_id`),
  ADD KEY `selection_process_employees_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `service_catalogs`
--
ALTER TABLE `service_catalogs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `service_changes`
--
ALTER TABLE `service_changes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_changes_change_no_unique` (`change_no`),
  ADD KEY `service_changes_service_ticket_id_foreign` (`service_ticket_id`),
  ADD KEY `service_changes_approver_id_foreign` (`approver_id`),
  ADD KEY `service_changes_proposer_id_foreign` (`proposer_id`);

--
-- Indeks untuk tabel `service_status_histories`
--
ALTER TABLE `service_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_status_histories_service_ticket_id_foreign` (`service_ticket_id`);

--
-- Indeks untuk tabel `service_tickets`
--
ALTER TABLE `service_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_tickets_no_ticket_unique` (`no_ticket`),
  ADD KEY `service_tickets_submitter_id_foreign` (`submitter_id`),
  ADD KEY `service_tickets_report_for_id_foreign` (`report_for_id`),
  ADD KEY `service_tickets_risk_register_id_foreign` (`risk_register_id`),
  ADD KEY `service_tickets_itsm_priority_id_foreign` (`itsm_priority_id`),
  ADD KEY `service_tickets_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `service_tickets_dept_head_id_foreign` (`dept_head_id`),
  ADD KEY `service_tickets_it_handler_id_foreign` (`it_handler_id`);

--
-- Indeks untuk tabel `service_ticket_assets`
--
ALTER TABLE `service_ticket_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_ticket_assets_service_ticket_id_foreign` (`service_ticket_id`),
  ADD KEY `service_ticket_assets_employee_id_foreign` (`employee_id`),
  ADD KEY `service_ticket_assets_it_asset_id_foreign` (`it_asset_id`);

--
-- Indeks untuk tabel `service_ticket_ccs`
--
ALTER TABLE `service_ticket_ccs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_ticket_ccs_service_ticket_id_foreign` (`service_ticket_id`),
  ADD KEY `service_ticket_ccs_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `service_ticket_media`
--
ALTER TABLE `service_ticket_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_ticket_media_service_ticket_message_id_foreign` (`service_ticket_message_id`);

--
-- Indeks untuk tabel `service_ticket_messages`
--
ALTER TABLE `service_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_ticket_messages_service_ticket_id_foreign` (`service_ticket_id`),
  ADD KEY `service_ticket_messages_sender_id_foreign` (`sender_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `template_medical`
--
ALTER TABLE `template_medical`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `temp_calendar`
--
ALTER TABLE `temp_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_evaluasi`
--
ALTER TABLE `training_evaluasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_fkt`
--
ALTER TABLE `training_fkt`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_fpkt`
--
ALTER TABLE `training_fpkt`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_periode`
--
ALTER TABLE `training_periode`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_record`
--
ALTER TABLE `training_record`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `training_status`
--
ALTER TABLE `training_status`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE;

--
-- Indeks untuk tabel `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `workhour_detail`
--
ALTER TABLE `workhour_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workhour_detail_workhour_id_foreign` (`workhour_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `areas`
--
ALTER TABLE `areas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `asset_disposals`
--
ALTER TABLE `asset_disposals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asset_disposal_items`
--
ALTER TABLE `asset_disposal_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asset_disposal_logs`
--
ALTER TABLE `asset_disposal_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asset_histories`
--
ALTER TABLE `asset_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `asset_maintenance_items`
--
ALTER TABLE `asset_maintenance_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `asset_types`
--
ALTER TABLE `asset_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `attendance_calendars`
--
ALTER TABLE `attendance_calendars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `attendance_permits`
--
ALTER TABLE `attendance_permits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `booking_record`
--
ALTER TABLE `booking_record`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `business_cancellations`
--
ALTER TABLE `business_cancellations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `business_cancellation_approvals`
--
ALTER TABLE `business_cancellation_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `business_cancellation_items`
--
ALTER TABLE `business_cancellation_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `business_cancellation_logs`
--
ALTER TABLE `business_cancellation_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `business_reports`
--
ALTER TABLE `business_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `business_report_approvals`
--
ALTER TABLE `business_report_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `business_report_attachments`
--
ALTER TABLE `business_report_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `business_report_items`
--
ALTER TABLE `business_report_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `business_report_logs`
--
ALTER TABLE `business_report_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `business_trips`
--
ALTER TABLE `business_trips`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `business_trip_allowances`
--
ALTER TABLE `business_trip_allowances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `business_trip_approvals`
--
ALTER TABLE `business_trip_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `business_trip_costs`
--
ALTER TABLE `business_trip_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `business_trip_hotels`
--
ALTER TABLE `business_trip_hotels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `business_trip_logs`
--
ALTER TABLE `business_trip_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `business_trip_transportations`
--
ALTER TABLE `business_trip_transportations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `calendar`
--
ALTER TABLE `calendar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT untuk tabel `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `candidate_education`
--
ALTER TABLE `candidate_education`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `candidate_experience`
--
ALTER TABLE `candidate_experience`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `claim_approvals`
--
ALTER TABLE `claim_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `claim_overtimes`
--
ALTER TABLE `claim_overtimes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `disposal_approval_paths`
--
ALTER TABLE `disposal_approval_paths`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `doctor_account`
--
ALTER TABLE `doctor_account`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `drug_keluar`
--
ALTER TABLE `drug_keluar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `drug_masuk`
--
ALTER TABLE `drug_masuk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `earlyout_orlates`
--
ALTER TABLE `earlyout_orlates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employee_attendances`
--
ALTER TABLE `employee_attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employee_attendance_details`
--
ALTER TABLE `employee_attendance_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employee_milestones`
--
ALTER TABLE `employee_milestones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `employee_requisition`
--
ALTER TABLE `employee_requisition`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `employee_requisition_educations`
--
ALTER TABLE `employee_requisition_educations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `employee_requisition_genders`
--
ALTER TABLE `employee_requisition_genders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `employee_requisition_hiring_steps`
--
ALTER TABLE `employee_requisition_hiring_steps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `employee_requisition_recruitment_sources`
--
ALTER TABLE `employee_requisition_recruitment_sources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT untuk tabel `evaluation_attachments`
--
ALTER TABLE `evaluation_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `evaluation_has_attachments`
--
ALTER TABLE `evaluation_has_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `evaluation_histories`
--
ALTER TABLE `evaluation_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `group_employees`
--
ALTER TABLE `group_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `group_employee_workhours`
--
ALTER TABLE `group_employee_workhours`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `group_workhours`
--
ALTER TABLE `group_workhours`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `hiring_step_has_employees`
--
ALTER TABLE `hiring_step_has_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `internal_rules`
--
ALTER TABLE `internal_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `itsm_priorities`
--
ALTER TABLE `itsm_priorities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `it_assets`
--
ALTER TABLE `it_assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT untuk tabel `job_posting`
--
ALTER TABLE `job_posting`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `knowledge_bases`
--
ALTER TABLE `knowledge_bases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;

--
-- AUTO_INCREMENT untuk tabel `knowledge_base_media`
--
ALTER TABLE `knowledge_base_media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `knowledge_base_users`
--
ALTER TABLE `knowledge_base_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `late_histories`
--
ALTER TABLE `late_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT untuk tabel `leave_approvals`
--
ALTER TABLE `leave_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=496;

--
-- AUTO_INCREMENT untuk tabel `leave_settings`
--
ALTER TABLE `leave_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `log_catatan_training`
--
ALTER TABLE `log_catatan_training`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=384;

--
-- AUTO_INCREMENT untuk tabel `master_appraisal`
--
ALTER TABLE `master_appraisal`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `master_building`
--
ALTER TABLE `master_building`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `master_contract`
--
ALTER TABLE `master_contract`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `master_drug`
--
ALTER TABLE `master_drug`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `master_hiring`
--
ALTER TABLE `master_hiring`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `master_kota`
--
ALTER TABLE `master_kota`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=515;

--
-- AUTO_INCREMENT untuk tabel `master_lab`
--
ALTER TABLE `master_lab`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_leave`
--
ALTER TABLE `master_leave`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `master_level`
--
ALTER TABLE `master_level`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `master_line_approval`
--
ALTER TABLE `master_line_approval`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_line_approval_employees`
--
ALTER TABLE `master_line_approval_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_position`
--
ALTER TABLE `master_position`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT untuk tabel `master_positioning`
--
ALTER TABLE `master_positioning`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `master_provinsi`
--
ALTER TABLE `master_provinsi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `master_room`
--
ALTER TABLE `master_room`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `master_section`
--
ALTER TABLE `master_section`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `master_work_hour`
--
ALTER TABLE `master_work_hour`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `medical`
--
ALTER TABLE `medical`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `medical_vendor`
--
ALTER TABLE `medical_vendor`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT untuk tabel `news_event`
--
ALTER TABLE `news_event`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `overtime_approvals`
--
ALTER TABLE `overtime_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `patient`
--
ALTER TABLE `patient`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pkb`
--
ALTER TABLE `pkb`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prestock_drug`
--
ALTER TABLE `prestock_drug`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `priority_metrics`
--
ALTER TABLE `priority_metrics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `qr_code_fkt`
--
ALTER TABLE `qr_code_fkt`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `qr_code_fpkt`
--
ALTER TABLE `qr_code_fpkt`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `risk_registers`
--
ALTER TABLE `risk_registers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `security_account`
--
ALTER TABLE `security_account`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `security_guest`
--
ALTER TABLE `security_guest`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `selection_process`
--
ALTER TABLE `selection_process`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `selection_process_assessments`
--
ALTER TABLE `selection_process_assessments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `selection_process_candidates`
--
ALTER TABLE `selection_process_candidates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `selection_process_employees`
--
ALTER TABLE `selection_process_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_catalogs`
--
ALTER TABLE `service_catalogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_changes`
--
ALTER TABLE `service_changes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_status_histories`
--
ALTER TABLE `service_status_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_tickets`
--
ALTER TABLE `service_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_ticket_assets`
--
ALTER TABLE `service_ticket_assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_ticket_ccs`
--
ALTER TABLE `service_ticket_ccs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_ticket_media`
--
ALTER TABLE `service_ticket_media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service_ticket_messages`
--
ALTER TABLE `service_ticket_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `template_medical`
--
ALTER TABLE `template_medical`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `temp_calendar`
--
ALTER TABLE `temp_calendar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_evaluasi`
--
ALTER TABLE `training_evaluasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_fkt`
--
ALTER TABLE `training_fkt`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_fpkt`
--
ALTER TABLE `training_fpkt`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_periode`
--
ALTER TABLE `training_periode`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_record`
--
ALTER TABLE `training_record`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `training_status`
--
ALTER TABLE `training_status`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `user_log`
--
ALTER TABLE `user_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `workhour_detail`
--
ALTER TABLE `workhour_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `abouts`
--
ALTER TABLE `abouts`
  ADD CONSTRAINT `abouts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `asset_disposals`
--
ALTER TABLE `asset_disposals`
  ADD CONSTRAINT `asset_disposals_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `asset_disposal_items`
--
ALTER TABLE `asset_disposal_items`
  ADD CONSTRAINT `asset_disposal_items_asset_disposal_id_foreign` FOREIGN KEY (`asset_disposal_id`) REFERENCES `asset_disposals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_disposal_items_it_asset_id_foreign` FOREIGN KEY (`it_asset_id`) REFERENCES `it_assets` (`id`);

--
-- Ketidakleluasaan untuk tabel `asset_disposal_logs`
--
ALTER TABLE `asset_disposal_logs`
  ADD CONSTRAINT `asset_disposal_logs_asset_disposal_id_foreign` FOREIGN KEY (`asset_disposal_id`) REFERENCES `asset_disposals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_disposal_logs_disposal_approval_path_id_foreign` FOREIGN KEY (`disposal_approval_path_id`) REFERENCES `disposal_approval_paths` (`id`);

--
-- Ketidakleluasaan untuk tabel `asset_histories`
--
ALTER TABLE `asset_histories`
  ADD CONSTRAINT `asset_histories_it_asset_id_foreign` FOREIGN KEY (`it_asset_id`) REFERENCES `it_assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `asset_maintenance_items`
--
ALTER TABLE `asset_maintenance_items`
  ADD CONSTRAINT `asset_maintenance_items_asset_maintenance_id_foreign` FOREIGN KEY (`asset_maintenance_id`) REFERENCES `asset_maintenances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_maintenance_items_it_asset_id_foreign` FOREIGN KEY (`it_asset_id`) REFERENCES `it_assets` (`id`);

--
-- Ketidakleluasaan untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
