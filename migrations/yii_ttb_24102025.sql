-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 24, 2025 lúc 12:15 PM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `yii_ttb`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `al_aluminum_materials`
--

DROP TABLE IF EXISTS `al_aluminum_materials`;
CREATE TABLE IF NOT EXISTS `al_aluminum_materials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'Mã vật liệu nhôm',
  `name` varchar(255) NOT NULL COMMENT 'Tên vật liệu nhôm',
  `profile_type` varchar(100) NOT NULL COMMENT 'Loại profile (khung bao, cánh, nẹp...)',
  `length` decimal(10,2) NOT NULL COMMENT 'Chiều dài tiêu chuẩn (mm)',
  `stock_length` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng chiều dài tồn kho (mm)',
  `stock_quantity` int DEFAULT '0' COMMENT 'Số lượng thanh nhôm tồn kho',
  `note` varchar(255) DEFAULT NULL COMMENT 'Ghi chú thêm',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `al_cut_groups`
--

DROP TABLE IF EXISTS `al_cut_groups`;
CREATE TABLE IF NOT EXISTS `al_cut_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL COMMENT 'Mã đơn hàng (liên kết al_orders)',
  `material_id` int NOT NULL COMMENT 'Mã vật liệu nhôm (liên kết al_aluminum_materials)',
  `profile_name` varchar(255) NOT NULL COMMENT 'Tên profile (VD: khung bao, cánh, nẹp...)',
  `cut_length` decimal(10,2) NOT NULL COMMENT 'Chiều dài cần cắt (mm)',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Số lượng cần cắt',
  `waste_length` decimal(10,2) DEFAULT '0.00' COMMENT 'Chiều dài nhôm vụn còn dư (mm)',
  `total_used_length` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng chiều dài đã sử dụng (mm)',
  `note` text COMMENT 'Ghi chú thêm',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_al_cut_groups_order` (`order_id`),
  KEY `fk_al_cut_groups_material` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `al_orders`
--

DROP TABLE IF EXISTS `al_orders`;
CREATE TABLE IF NOT EXISTS `al_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL COMMENT 'Mã đơn hàng',
  `customer_name` varchar(255) NOT NULL COMMENT 'Tên khách hàng',
  `description` text COMMENT 'Mô tả / Ghi chú',
  `status` varchar(50) DEFAULT 'pending' COMMENT 'Trạng thái đơn hàng',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `al_reuse_log`
--

DROP TABLE IF EXISTS `al_reuse_log`;
CREATE TABLE IF NOT EXISTS `al_reuse_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `scrap_id` int NOT NULL COMMENT 'Mã nhôm vụn được tái sử dụng (liên kết al_scrap_aluminum)',
  `used_in_cut_group_id` int NOT NULL COMMENT 'Nhóm cắt đã dùng lại nhôm vụn này (liên kết al_cut_groups)',
  `used_length` decimal(10,2) NOT NULL COMMENT 'Chiều dài đã tái sử dụng (mm)',
  `note` varchar(255) DEFAULT NULL COMMENT 'Ghi chú',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_al_reuse_log_scrap` (`scrap_id`),
  KEY `fk_al_reuse_log_cut_group` (`used_in_cut_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `al_scrap_aluminum`
--

DROP TABLE IF EXISTS `al_scrap_aluminum`;
CREATE TABLE IF NOT EXISTS `al_scrap_aluminum` (
  `id` int NOT NULL AUTO_INCREMENT,
  `material_id` int NOT NULL COMMENT 'Mã vật liệu nhôm (liên kết al_aluminum_materials)',
  `cut_group_id` int DEFAULT NULL COMMENT 'Mã nhóm cắt sinh ra vụn (liên kết al_cut_groups)',
  `length` decimal(10,2) NOT NULL COMMENT 'Chiều dài nhôm vụn (mm)',
  `weight` decimal(10,2) DEFAULT '0.00' COMMENT 'Khối lượng ước tính (kg)',
  `is_reused` tinyint(1) DEFAULT '0' COMMENT 'Đã tái sử dụng chưa (0: chưa, 1: rồi)',
  `note` varchar(255) DEFAULT NULL COMMENT 'Ghi chú',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_al_scrap_aluminum_material` (`material_id`),
  KEY `fk_al_scrap_aluminum_cut_group` (`cut_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('Admin', 102, NULL),
('nhanvien', 102, NULL),
('Quản lý', 108, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  `group_code` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  KEY `fk_auth_item_group_code` (`group_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`, `group_code`) VALUES
('/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/categories/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/bulkdelete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/create', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/delete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/index', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/update', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/categories/default/view', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/*', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/customers/default/*', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/customers/default/bulkdelete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/default/create', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/default/delete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/default/index', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/default/update', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/customers/default/view', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/debug/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/db-explain', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/download-mail', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/index', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/toolbar', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/default/view', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/user/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/user/reset-identity', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/debug/user/set-identity', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/action', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/diff', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/index', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/preview', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gii/default/view', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/gridview/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/gridview/export/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/gridview/export/download', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/gridview/grid-edited-row/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/gridview/grid-edited-row/back', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/home/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/home/default/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/home/default/index', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/invoice/*', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/*', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/bulkdelete', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/create', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/delete', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/index', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/update', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/invoice/default/view', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/products/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/bulkdelete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/create', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/delete', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/index', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/update', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/default/view', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/*', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/create', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/edit', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/save', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/save1', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/products/product-price/update', 3, NULL, NULL, NULL, 1760149204, 1760149204, NULL),
('/product_prices_unit/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/product_prices_unit/default/*', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/bulkdelete', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/create', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/delete', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/index', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/update', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/product_prices_unit/default/view', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/site/*', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/about', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/captcha', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/check-session', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/site/contact', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/error', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/index', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/login', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/site/logout', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/*', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/*', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/bulkdelete', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/create', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/delete', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/index', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/update', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/units/default/view', 3, NULL, NULL, NULL, 1760149203, 1760149203, NULL),
('/user-management/*', 3, NULL, NULL, NULL, 1759030802, 1759030802, NULL),
('/user_management/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/bulkdelete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/create', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/delete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/update', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/default/view', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/permission-route/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/permission-route/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/permission-route/refresh-routes', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission/permission-route/save-routes', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/bulkdelete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/create', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/delete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/update', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/default/view', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/group-add-permission/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/permission_group/group-add-permission/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/bulkdelete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/create', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/delete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/update', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/default/view', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/role-permission/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/role-permission/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/role/role-permission/save-permissions', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/session_manager/*', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/*', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/bulkdelete', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/create', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/delete', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/index', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/logout-device', 3, NULL, NULL, NULL, 1760690818, 1760690818, NULL),
('/user_management/session_manager/default/revoke', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/revoke-all', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/update', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/session_manager/default/view', 3, NULL, NULL, NULL, 1760624602, 1760624602, NULL),
('/user_management/user/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/captcha', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/change-own-password', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/confirm-email', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/confirm-email-receive', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/confirm-registration-email', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/login', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/logout', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/password-recovery', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/password-recovery-receive', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/auth/registration', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/bulkdelete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/change-password', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/create', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/delete', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/save-change-password', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/update', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/default/view', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/user-permission/*', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/user-permission/ajax-get-by-role', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/user-permission/index', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('/user_management/user/user-permission/save-roles', 3, NULL, NULL, NULL, 1760607485, 1760607485, NULL),
('Admin', 1, 'Admin', NULL, NULL, 1760606263, 1760606263, NULL),
('assignRolesToUsers', 2, 'Assign roles to users', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('bindUserToIp', 2, 'Bind user to IP', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('changeOwnPassword', 2, 'Change own password', NULL, NULL, 1759030802, 1759030802, 'userCommonPermissions'),
('changeUserPassword', 2, 'Change user password', NULL, NULL, 1760510362, 1760510362, 'gCauHinhHeThong'),
('commonPermission', 2, 'Common permission', NULL, NULL, 1760365722, 1760365722, 'userCommonPermissions'),
('createUsers', 2, 'Create users', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('DanhMucDonViTinh', 2, 'Danh mục đơn vị tính', NULL, NULL, 1760339632, 1760339632, 'gBaoCaoThongKe'),
('DanhMucKhachHang', 2, 'Danh mục khách hàng', NULL, NULL, 1760339599, 1760339599, 'gBaoCaoThongKe'),
('deleteUsers', 2, 'Delete users', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('DongGoi', 2, 'Đóng gói', NULL, NULL, 1760339687, 1760339687, 'userManagement'),
('editUserEmail', 2, 'Edit user email', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('editUsers', 2, 'Edit users', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('nhanvien', 1, 'Nhân viên', NULL, NULL, 1760150194, 1760150194, NULL),
('NhomQuyen', 2, 'Nhóm quyền', NULL, NULL, 1760339839, 1760339839, 'userCommonPermissions'),
('pBaoGiaSanPham', 2, 'Báo giá sản phẩm', NULL, NULL, 1760361119, 1760361119, 'gBaoCaoThongKe'),
('PhanQuyen', 2, 'Phân quyền', NULL, NULL, 1760339821, 1760339821, 'userCommonPermissions'),
('pTrangChu', 2, 'Trang chủ', NULL, NULL, 1760624561, 1760624561, 'userManagement'),
('Quản lý', 1, 'Quản lý', NULL, NULL, 1760261558, 1760261558, NULL),
('QuanLyNguoiDung', 2, 'Quản lý người dùng', NULL, NULL, 1760339728, 1760339728, 'userCommonPermissions'),
('SanPham', 2, 'Sản phẩm', NULL, NULL, 1760339669, 1760339669, 'userManagement'),
('ttbDanhMucSanPham', 2, 'Danh mục sản phẩm', NULL, NULL, 1760339545, 1760339545, 'userManagement'),
('VaiTro', 2, 'Vai trò', NULL, NULL, 1760339772, 1760339772, 'userCommonPermissions'),
('viewRegistrationIp', 2, 'View registration IP', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('viewUserEmail', 2, 'View user email', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('viewUserRoles', 2, 'View user roles', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('viewUsers', 2, 'View users', NULL, NULL, 1759030802, 1759030802, 'userManagement'),
('viewVisitLog', 2, 'View visit log', NULL, NULL, 1759030802, 1759030802, 'userManagement');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('pTrangChu', '/*'),
('assignRolesToUsers', '/categories/*'),
('changeOwnPassword', '/categories/*'),
('viewUsers', '/categories/*'),
('assignRolesToUsers', '/categories/default/*'),
('changeOwnPassword', '/categories/default/*'),
('viewUsers', '/categories/default/*'),
('assignRolesToUsers', '/categories/default/bulkdelete'),
('changeOwnPassword', '/categories/default/bulkdelete'),
('assignRolesToUsers', '/categories/default/create'),
('changeOwnPassword', '/categories/default/create'),
('assignRolesToUsers', '/categories/default/delete'),
('changeOwnPassword', '/categories/default/delete'),
('assignRolesToUsers', '/categories/default/index'),
('changeOwnPassword', '/categories/default/index'),
('assignRolesToUsers', '/categories/default/update'),
('changeOwnPassword', '/categories/default/update'),
('assignRolesToUsers', '/categories/default/view'),
('changeOwnPassword', '/categories/default/view'),
('changeOwnPassword', '/customers/*'),
('viewUsers', '/customers/*'),
('changeOwnPassword', '/customers/default/*'),
('DanhMucKhachHang', '/customers/default/*'),
('viewUsers', '/customers/default/*'),
('changeOwnPassword', '/customers/default/bulkdelete'),
('DanhMucKhachHang', '/customers/default/bulkdelete'),
('changeOwnPassword', '/customers/default/create'),
('DanhMucKhachHang', '/customers/default/create'),
('changeOwnPassword', '/customers/default/delete'),
('DanhMucKhachHang', '/customers/default/delete'),
('changeOwnPassword', '/customers/default/index'),
('DanhMucKhachHang', '/customers/default/index'),
('changeOwnPassword', '/customers/default/update'),
('DanhMucKhachHang', '/customers/default/update'),
('changeOwnPassword', '/customers/default/view'),
('DanhMucKhachHang', '/customers/default/view'),
('changeOwnPassword', '/home/*'),
('pBaoGiaSanPham', '/home/*'),
('pTrangChu', '/home/*'),
('changeOwnPassword', '/home/default/*'),
('pBaoGiaSanPham', '/home/default/*'),
('pTrangChu', '/home/default/*'),
('changeOwnPassword', '/home/default/index'),
('pBaoGiaSanPham', '/home/default/index'),
('pTrangChu', '/home/default/index'),
('pBaoGiaSanPham', '/products/*'),
('ttbDanhMucSanPham', '/products/*'),
('ttbDanhMucSanPham', '/products/default/*'),
('ttbDanhMucSanPham', '/products/default/bulkdelete'),
('ttbDanhMucSanPham', '/products/default/create'),
('ttbDanhMucSanPham', '/products/default/delete'),
('changeOwnPassword', '/products/default/index'),
('ttbDanhMucSanPham', '/products/default/index'),
('ttbDanhMucSanPham', '/products/default/update'),
('changeOwnPassword', '/products/default/view'),
('ttbDanhMucSanPham', '/products/default/view'),
('changeOwnPassword', '/products/product-price/*'),
('pBaoGiaSanPham', '/products/product-price/*'),
('ttbDanhMucSanPham', '/products/product-price/*'),
('pBaoGiaSanPham', '/products/product-price/create'),
('ttbDanhMucSanPham', '/products/product-price/create'),
('pBaoGiaSanPham', '/products/product-price/edit'),
('ttbDanhMucSanPham', '/products/product-price/edit'),
('pBaoGiaSanPham', '/products/product-price/save'),
('ttbDanhMucSanPham', '/products/product-price/save'),
('pBaoGiaSanPham', '/products/product-price/save1'),
('ttbDanhMucSanPham', '/products/product-price/save1'),
('pBaoGiaSanPham', '/products/product-price/update'),
('ttbDanhMucSanPham', '/products/product-price/update'),
('pBaoGiaSanPham', '/product_prices_unit/*'),
('pBaoGiaSanPham', '/product_prices_unit/default/*'),
('pBaoGiaSanPham', '/product_prices_unit/default/bulkdelete'),
('pBaoGiaSanPham', '/product_prices_unit/default/create'),
('pBaoGiaSanPham', '/product_prices_unit/default/delete'),
('pBaoGiaSanPham', '/product_prices_unit/default/index'),
('pBaoGiaSanPham', '/product_prices_unit/default/update'),
('pBaoGiaSanPham', '/product_prices_unit/default/view'),
('changeOwnPassword', '/units/*'),
('DanhMucDonViTinh', '/units/*'),
('changeOwnPassword', '/units/default/*'),
('DanhMucDonViTinh', '/units/default/*'),
('DanhMucDonViTinh', '/units/default/bulkdelete'),
('DanhMucDonViTinh', '/units/default/create'),
('DanhMucDonViTinh', '/units/default/delete'),
('DanhMucDonViTinh', '/units/default/index'),
('DanhMucDonViTinh', '/units/default/update'),
('DanhMucDonViTinh', '/units/default/view'),
('Admin', 'changeOwnPassword'),
('nhanvien', 'changeOwnPassword'),
('Admin', 'changeUserPassword'),
('Admin', 'DanhMucDonViTinh'),
('nhanvien', 'DanhMucDonViTinh'),
('Admin', 'DanhMucKhachHang'),
('nhanvien', 'DanhMucKhachHang'),
('Admin', 'DongGoi'),
('Admin', 'NhomQuyen'),
('Admin', 'pBaoGiaSanPham'),
('nhanvien', 'pBaoGiaSanPham'),
('Quản lý', 'pBaoGiaSanPham'),
('Admin', 'PhanQuyen'),
('nhanvien', 'pTrangChu'),
('Admin', 'QuanLyNguoiDung'),
('Admin', 'SanPham'),
('nhanvien', 'SanPham'),
('Admin', 'ttbDanhMucSanPham'),
('nhanvien', 'ttbDanhMucSanPham'),
('Admin', 'VaiTro'),
('editUserEmail', 'viewUserEmail'),
('createUsers', 'viewUsers'),
('deleteUsers', 'viewUsers'),
('editUsers', 'viewUsers');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auth_item_group`
--

DROP TABLE IF EXISTS `auth_item_group`;
CREATE TABLE IF NOT EXISTS `auth_item_group` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `auth_item_group`
--

INSERT INTO `auth_item_group` (`code`, `name`, `created_at`, `updated_at`) VALUES
('gBaoCaoThongKe', 'Báo cáo - thống kê', 1760361637, 1760361637),
('gBaoGia', 'Báo giá', 1760361335, 1760361335),
('gCauHinhHeThong', 'Cấu hình hệ thống', 1760361580, 1760361580),
('gQuanLyBanHang', 'Quản lý bán hàng', 1760361606, 1760361606),
('gQuanLyKhachHang', 'Quản lý khách hàng', 1760361673, 1760361673),
('userCommonPermissions', 'Quyền chung của người dùng', 1760352964, 1760352964),
('userManagement', 'Quản lý người dùng', 1760346351, 1760346351);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Điện thoại rfgvrfv', 'Danh mục các loại điện thoại', '2025-09-29 15:30:59', '2025-10-10 04:42:42'),
(2, 'Laptop', 'Danh mục các loại laptop', '2025-09-29 15:30:59', '2025-09-29 15:30:59'),
(3, 'Máy tính bảng', 'Danh mục các loại tablet', '2025-09-29 15:30:59', '2025-09-29 15:30:59'),
(4, 'Phụ kiện', 'Danh mục các loại phụ kiện', '2025-09-29 15:30:59', '2025-09-29 15:30:59'),
(38, 'Danh mục 1', 'Mô tả cho danh mục 1', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(39, 'Danh mục 2', 'Mô tả cho danh mục 2', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(40, 'Danh mục 3', 'Mô tả cho danh mục 3', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(41, 'Danh mục 4', 'Mô tả cho danh mục 4', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(42, 'Danh mục 5', 'Mô tả cho danh mục 5', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(43, 'Danh mục 6', 'Mô tả cho danh mục 6', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(44, 'Danh mục 7', 'Mô tả cho danh mục 7', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(45, 'Danh mục 8', 'Mô tả cho danh mục 8', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(46, 'Danh mục 9', 'Mô tả cho danh mục 9', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(47, 'Danh mục 10', 'Mô tả cho danh mục 10', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(48, 'Danh mục 11', 'Mô tả cho danh mục 11', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(49, 'Danh mục 12', 'Mô tả cho danh mục 12', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(50, 'Danh mục 13', 'Mô tả cho danh mục 13', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(51, 'Danh mục 14', 'Mô tả cho danh mục 14', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(52, 'Danh mục 15', 'Mô tả cho danh mục 15', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(53, 'Danh mục 16', 'Mô tả cho danh mục 16', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(54, 'Danh mục 17', 'Mô tả cho danh mục 17', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(55, 'Danh mục 18', 'Mô tả cho danh mục 18', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(56, 'Danh mục 19', 'Mô tả cho danh mục 19', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(57, 'Danh mục 20', 'Mô tả cho danh mục 20', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(58, 'Danh mục 21', 'Mô tả cho danh mục 21', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(59, 'Danh mục 22', 'Mô tả cho danh mục 22', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(60, 'Danh mục 23', 'Mô tả cho danh mục 23', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(61, 'Danh mục 24', 'Mô tả cho danh mục 24', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(62, 'Danh mục 25', 'Mô tả cho danh mục 25', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(63, 'Danh mục 26', 'Mô tả cho danh mục 26', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(64, 'Danh mục 27', 'Mô tả cho danh mục 27', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(65, 'Danh mục 28', 'Mô tả cho danh mục 28', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(66, 'Danh mục 29', 'Mô tả cho danh mục 29', '2025-10-21 14:25:53', '2025-10-21 14:25:53'),
(67, 'Danh mục 30', 'Mô tả cho danh mục 30', '2025-10-21 14:25:53', '2025-10-21 14:25:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `note`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn A', 'vana@example.com', '0909123456', '123 Trần Hưng Đạo, Hà Nội', NULL, '2025-10-03 08:25:58', '2025-10-03 08:25:58'),
(2, 'Trần Thị B', 'thib@example.com', '0912345678', '456 Lê Lợi, TP.HCM', NULL, '2025-10-03 08:25:58', '2025-10-03 08:25:58'),
(3, 'Lê Văn C', 'vanc@example.com', '0987654321', '789 Nguyễn Huệ, Đà Nẵng', NULL, '2025-10-03 08:25:58', '2025-10-03 08:25:58'),
(13, 'Khách hàng 1', 'khach1@example.com', '0900011234', 'Số 1 Đường ABC, Phường 1, TP Trà Vinh', 'Ghi chú khách hàng 1', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(14, 'Khách hàng 2', 'khach2@example.com', '0900021234', 'Số 2 Đường ABC, Phường 2, TP Trà Vinh', 'Ghi chú khách hàng 2', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(15, 'Khách hàng 3', 'khach3@example.com', '0900031234', 'Số 3 Đường ABC, Phường 3, TP Trà Vinh', 'Ghi chú khách hàng 3', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(16, 'Khách hàng 4', 'khach4@example.com', '0900041234', 'Số 4 Đường ABC, Phường 4, TP Trà Vinh', 'Ghi chú khách hàng 4', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(17, 'Khách hàng 5', 'khach5@example.com', '0900051234', 'Số 5 Đường ABC, Phường 5, TP Trà Vinh', 'Ghi chú khách hàng 5', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(18, 'Khách hàng 6', 'khach6@example.com', '0900061234', 'Số 6 Đường ABC, Phường 6, TP Trà Vinh', 'Ghi chú khách hàng 6', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(19, 'Khách hàng 7', 'khach7@example.com', '0900071234', 'Số 7 Đường ABC, Phường 7, TP Trà Vinh', 'Ghi chú khách hàng 7', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(20, 'Khách hàng 8', 'khach8@example.com', '0900081234', 'Số 8 Đường ABC, Phường 8, TP Trà Vinh', 'Ghi chú khách hàng 8', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(21, 'Khách hàng 9', 'khach9@example.com', '0900091234', 'Số 9 Đường ABC, Phường 9, TP Trà Vinh', 'Ghi chú khách hàng 9', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(22, 'Khách hàng 10', 'khach10@example.com', '0900101234', 'Số 10 Đường ABC, Phường 0, TP Trà Vinh', 'Ghi chú khách hàng 10', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(23, 'Khách hàng 11', 'khach11@example.com', '0900111234', 'Số 11 Đường ABC, Phường 1, TP Trà Vinh', 'Ghi chú khách hàng 11', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(24, 'Khách hàng 12', 'khach12@example.com', '0900121234', 'Số 12 Đường ABC, Phường 2, TP Trà Vinh', 'Ghi chú khách hàng 12', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(25, 'Khách hàng 13', 'khach13@example.com', '0900131234', 'Số 13 Đường ABC, Phường 3, TP Trà Vinh', 'Ghi chú khách hàng 13', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(26, 'Khách hàng 14', 'khach14@example.com', '0900141234', 'Số 14 Đường ABC, Phường 4, TP Trà Vinh', 'Ghi chú khách hàng 14', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(27, 'Khách hàng 15', 'khach15@example.com', '0900151234', 'Số 15 Đường ABC, Phường 5, TP Trà Vinh', 'Ghi chú khách hàng 15', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(28, 'Khách hàng 16', 'khach16@example.com', '0900161234', 'Số 16 Đường ABC, Phường 6, TP Trà Vinh', 'Ghi chú khách hàng 16', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(29, 'Khách hàng 17', 'khach17@example.com', '0900171234', 'Số 17 Đường ABC, Phường 7, TP Trà Vinh', 'Ghi chú khách hàng 17', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(30, 'Khách hàng 18', 'khach18@example.com', '0900181234', 'Số 18 Đường ABC, Phường 8, TP Trà Vinh', 'Ghi chú khách hàng 18', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(31, 'Khách hàng 19', 'khach19@example.com', '0900191234', 'Số 19 Đường ABC, Phường 9, TP Trà Vinh', 'Ghi chú khách hàng 19', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(32, 'Khách hàng 20', 'khach20@example.com', '0900201234', 'Số 20 Đường ABC, Phường 0, TP Trà Vinh', 'Ghi chú khách hàng 20', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(33, 'Khách hàng 21', 'khach21@example.com', '0900211234', 'Số 21 Đường ABC, Phường 1, TP Trà Vinh', 'Ghi chú khách hàng 21', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(34, 'Khách hàng 22', 'khach22@example.com', '0900221234', 'Số 22 Đường ABC, Phường 2, TP Trà Vinh', 'Ghi chú khách hàng 22', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(35, 'Khách hàng 23', 'khach23@example.com', '0900231234', 'Số 23 Đường ABC, Phường 3, TP Trà Vinh', 'Ghi chú khách hàng 23', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(36, 'Khách hàng 24', 'khach24@example.com', '0900241234', 'Số 24 Đường ABC, Phường 4, TP Trà Vinh', 'Ghi chú khách hàng 24', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(37, 'Khách hàng 25', 'khach25@example.com', '0900251234', 'Số 25 Đường ABC, Phường 5, TP Trà Vinh', 'Ghi chú khách hàng 25', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(38, 'Khách hàng 26', 'khach26@example.com', '0900261234', 'Số 26 Đường ABC, Phường 6, TP Trà Vinh', 'Ghi chú khách hàng 26', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(39, 'Khách hàng 27', 'khach27@example.com', '0900271234', 'Số 27 Đường ABC, Phường 7, TP Trà Vinh', 'Ghi chú khách hàng 27', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(40, 'Khách hàng 28', 'khach28@example.com', '0900281234', 'Số 28 Đường ABC, Phường 8, TP Trà Vinh', 'Ghi chú khách hàng 28', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(41, 'Khách hàng 29', 'khach29@example.com', '0900291234', 'Số 29 Đường ABC, Phường 9, TP Trà Vinh', 'Ghi chú khách hàng 29', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(42, 'Khách hàng 30', 'khach30@example.com', '0900301234', 'Số 30 Đường ABC, Phường 0, TP Trà Vinh', 'Ghi chú khách hàng 30', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(43, 'Khách hàng 31', 'khach31@example.com', '0900311234', 'Số 31 Đường ABC, Phường 1, TP Trà Vinh', 'Ghi chú khách hàng 31', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(44, 'Khách hàng 32', 'khach32@example.com', '0900321234', 'Số 32 Đường ABC, Phường 2, TP Trà Vinh', 'Ghi chú khách hàng 32', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(45, 'Khách hàng 33', 'khach33@example.com', '0900331234', 'Số 33 Đường ABC, Phường 3, TP Trà Vinh', 'Ghi chú khách hàng 33', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(46, 'Khách hàng 34', 'khach34@example.com', '0900341234', 'Số 34 Đường ABC, Phường 4, TP Trà Vinh', 'Ghi chú khách hàng 34', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(47, 'Khách hàng 35', 'khach35@example.com', '0900351234', 'Số 35 Đường ABC, Phường 5, TP Trà Vinh', 'Ghi chú khách hàng 35', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(48, 'Khách hàng 36', 'khach36@example.com', '0900361234', 'Số 36 Đường ABC, Phường 6, TP Trà Vinh', 'Ghi chú khách hàng 36', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(49, 'Khách hàng 37', 'khach37@example.com', '0900371234', 'Số 37 Đường ABC, Phường 7, TP Trà Vinh', 'Ghi chú khách hàng 37', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(50, 'Khách hàng 38', 'khach38@example.com', '0900381234', 'Số 38 Đường ABC, Phường 8, TP Trà Vinh', 'Ghi chú khách hàng 38', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(51, 'Khách hàng 39', 'khach39@example.com', '0900391234', 'Số 39 Đường ABC, Phường 9, TP Trà Vinh', 'Ghi chú khách hàng 39', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(52, 'Khách hàng 40', 'khach40@example.com', '0900401234', 'Số 40 Đường ABC, Phường 0, TP Trà Vinh', 'Ghi chú khách hàng 40', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(53, 'Khách hàng 41', 'khach41@example.com', '0900411234', 'Số 41 Đường ABC, Phường 1, TP Trà Vinh', 'Ghi chú khách hàng 41', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(54, 'Khách hàng 42', 'khach42@example.com', '0900421234', 'Số 42 Đường ABC, Phường 2, TP Trà Vinh', 'Ghi chú khách hàng 42', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(55, 'Khách hàng 43', 'khach43@example.com', '0900431234', 'Số 43 Đường ABC, Phường 3, TP Trà Vinh', 'Ghi chú khách hàng 43', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(56, 'Khách hàng 44', 'khach44@example.com', '0900441234', 'Số 44 Đường ABC, Phường 4, TP Trà Vinh', 'Ghi chú khách hàng 44', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(57, 'Khách hàng 45', 'khach45@example.com', '0900451234', 'Số 45 Đường ABC, Phường 5, TP Trà Vinh', 'Ghi chú khách hàng 45', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(58, 'Khách hàng 46', 'khach46@example.com', '0900461234', 'Số 46 Đường ABC, Phường 6, TP Trà Vinh', 'Ghi chú khách hàng 46', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(59, 'Khách hàng 47', 'khach47@example.com', '0900471234', 'Số 47 Đường ABC, Phường 7, TP Trà Vinh', 'Ghi chú khách hàng 47', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(60, 'Khách hàng 48', 'khach48@example.com', '0900481234', 'Số 48 Đường ABC, Phường 8, TP Trà Vinh', 'Ghi chú khách hàng 48', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(61, 'Khách hàng 49', 'khach49@example.com', '0900491234', 'Số 49 Đường ABC, Phường 9, TP Trà Vinh', 'Ghi chú khách hàng 49', '2025-10-21 08:49:08', '2025-10-21 08:49:08'),
(62, 'Khách hàng 50', 'khach50@example.com', '0900501234', 'Số 50 Đường ABC, Phường 0, TP Trà Vinh', 'Ghi chú khách hàng 50', '2025-10-21 08:49:08', '2025-10-21 08:49:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL COMMENT 'Số hóa đơn duy nhất',
  `customer_id` int NOT NULL COMMENT 'Khách hàng liên kết',
  `issue_date` date NOT NULL COMMENT 'Ngày lập hóa đơn',
  `due_date` date DEFAULT NULL COMMENT 'Ngày đến hạn thanh toán',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Tổng tiền trước thuế và giảm giá',
  `discount_total` decimal(12,2) DEFAULT '0.00' COMMENT 'Tổng số tiền giảm giá',
  `tax_total` decimal(12,2) DEFAULT '0.00' COMMENT 'Tổng số tiền thuế',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Tổng tiền sau thuế và giảm giá',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Trạng thái hóa đơn (nháp, chưa thanh toán, đã thanh toán, đã hủy)',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Phương thức thanh toán (tiền mặt, chuyển khoản...)',
  `notes` text COMMENT 'Ghi chú hóa đơn',
  `created_by` int DEFAULT NULL COMMENT 'Người tạo hóa đơn',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `idx-invoice-customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `invoice`
--

INSERT INTO `invoice` (`id`, `invoice_number`, `customer_id`, `issue_date`, `due_date`, `subtotal`, `discount_total`, `tax_total`, `total_amount`, `status`, `payment_method`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(12, 'H1', 1, '2025-10-19', '2025-10-19', 0.00, 0.00, 0.00, 0.00, 'Nháp', 'Chuyển khoản', '', NULL, '2025-10-19 07:06:26', '2025-10-19 07:06:26'),
(14, 'H2', 2, '2025-10-19', '2025-10-19', 213000.00, 0.00, 0.00, 213000.00, 'Nháp', 'Chuyển khoản', '', NULL, '2025-10-19 11:56:31', '2025-10-20 08:12:59'),
(31, 'H3', 3, '2025-10-19', '2025-10-19', 50000.00, 0.00, 0.00, 50000.00, 'Nháp', 'Tiền mặt', '', NULL, '2025-10-19 14:46:48', '2025-10-19 16:17:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice_detail`
--

DROP TABLE IF EXISTS `invoice_detail`;
CREATE TABLE IF NOT EXISTS `invoice_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL COMMENT 'Khóa ngoại đến hóa đơn',
  `product_price_unit_id` int NOT NULL COMMENT 'Khóa ngoại đến bảng product_prices_unit',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Số lượng sản phẩm',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Giá lưu',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Thành tiền',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Ghi chú',
  PRIMARY KEY (`id`),
  KEY `fk_invoice_detail_invoice` (`invoice_id`),
  KEY `fk_invoice_detail_price_unit` (`product_price_unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `invoice_detail`
--

INSERT INTO `invoice_detail` (`id`, `invoice_id`, `product_price_unit_id`, `quantity`, `unit_price`, `total`, `notes`) VALUES
(1, 12, 7, 1, 35000.00, 35000.00, ''),
(2, 12, 8, 3, 40000.00, 120000.00, ''),
(3, 14, 7, 1, 35000.00, 35000.00, ''),
(4, 14, 8, 4, 40000.00, 160000.00, ''),
(5, 14, 3, 1, 18000.00, 18000.00, ''),
(11, 31, 10, 1, 50000.00, 50000.00, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1759030789),
('m140608_173539_create_user_table', 1759030800),
('m140611_133903_init_rbac', 1759030800),
('m140808_073114_create_auth_item_group_table', 1759030801),
('m140809_072112_insert_superadmin_to_user', 1759030802),
('m140809_073114_insert_common_permisison_to_auth_item', 1759030802),
('m141023_141535_create_user_visit_log', 1759030802),
('m141116_115804_add_bind_to_ip_and_registration_ip_to_user', 1759030802),
('m141121_194858_split_browser_and_os_column', 1759030802),
('m141201_220516_add_email_and_email_confirmed_to_user', 1759030802),
('m141207_001649_create_basic_user_permissions', 1759030802),
('m250928_061112_create_table_categories', 1759041200),
('m250928_061157_create_table_products', 1759041200),
('m250929_152728_create_table_categories', 1759159859),
('m250929_152746_create_table_products', 1759159859),
('m251003_082508_create_table_customers', 1759479958),
('m251003_083627_create_table_units', 1759480772),
('m251016_123415_create_table_user_sessions', 1760618238),
('m251003_092534_create_table_product_prices_unit', 1759484821),
('m251017_075807_create_table_invoice', 1760690184),
('m251017_083653_create_table_invoice_detail', 1760831424),
('m251024_095106_al_create_table_al_scrap_aluminum', 1761307757),
('m251024_083056_al_create_table_al_orders', 1761307756),
('m251024_092903_al_create_table_al_aluminum_materials', 1761307757),
('m251024_094946_al_create_table_al_cut_groups', 1761307757),
('m251024_095218_al_create_table_al_reuse_log', 1761307758);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Product name',
  `price` decimal(10,0) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk-products-category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `datetime`, `created_at`, `updated_at`) VALUES
(1, 1, 'Smartphone', 5000000, '2025-09-30 19:16:01', '2025-09-29 15:30:59', '2025-09-30 12:16:05'),
(2, 1, 'Laptop', 12000, '2025-09-18 19:18:18', '2025-09-29 15:30:59', '2025-09-30 12:18:29'),
(3, 2, 'T-Shirt', 20000, '2025-01-01 00:00:00', '2025-09-29 15:30:59', '2025-09-30 10:49:49'),
(4, 2, 'Jeans', 4500000, '2025-09-30 18:00:21', '2025-09-29 15:30:59', '2025-09-30 11:00:24'),
(5, 3, 'Novel', 15000, '2025-09-30 19:11:12', '2025-09-29 15:30:59', '2025-09-30 12:11:15'),
(6, 3, 'Dictionary', 3000000, '2025-09-30 19:12:39', '2025-09-29 15:30:59', '2025-09-30 12:12:41'),
(7, 1, 'SON GFD', 100000, '2025-09-30 19:52:37', '2025-09-30 07:37:38', '2025-09-30 12:52:40'),
(8, 1, 'A6', 10000000, '2025-09-30 20:04:40', '2025-09-30 07:41:55', '2025-09-30 13:04:42'),
(101, 2, 'Sản phẩm 1', 10150, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(102, 3, 'Sản phẩm 2', 10300, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(103, 4, 'Sản phẩm 3', 10450, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(104, 1, 'Sản phẩm 5', 10750, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(105, 2, 'Sản phẩm 6', 10900, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(106, 3, 'Sản phẩm 7', 11050, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06'),
(107, 4, 'Sản phẩm 8', 11200, '2025-10-21 10:00:00', '2025-10-22 02:41:06', '2025-10-22 02:41:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_prices_unit`
--

DROP TABLE IF EXISTS `product_prices_unit`;
CREATE TABLE IF NOT EXISTS `product_prices_unit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk-product_rices_unit-product_id` (`product_id`),
  KEY `fk-product_rices_unit-unit_id` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `product_prices_unit`
--

INSERT INTO `product_prices_unit` (`id`, `product_id`, `unit_id`, `price`, `datetime`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 20000.00, '2025-10-09 08:00:00', '2025-10-03 16:47:01', '2025-10-09 10:25:16'),
(2, 1, 2, 15000.00, '2025-10-02 09:15:00', '2025-10-03 16:47:01', '2025-10-09 10:26:00'),
(3, 2, 1, 18000.00, '2025-10-03 10:30:00', '2025-10-03 16:47:01', '2025-10-09 14:52:47'),
(4, 2, 3, 20000.00, '2025-10-04 11:45:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(5, 3, 2, 25000.25, '2025-10-05 13:00:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(6, 3, 4, 30000.00, '2025-10-06 14:15:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(7, 1, 3, 35000.00, '2025-10-07 15:30:00', '2025-10-03 16:47:01', '2025-10-09 16:17:02'),
(8, 2, 4, 40000.00, '2025-10-08 16:45:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(9, 3, 1, 45000.50, '2025-10-09 18:00:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(10, 1, 2, 50000.00, '2025-10-10 19:15:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(11, 2, 3, 55000.25, '2025-10-11 20:30:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(12, 3, 4, 60000.00, '2025-10-12 21:45:00', '2025-10-03 16:47:01', '2025-10-03 16:47:01'),
(17, 1, 1, 10000.00, '2025-10-06 14:35:19', '2025-10-06 14:35:51', '2025-10-06 14:35:51'),
(18, 2, 2, 19000.00, '2025-10-08 14:35:00', '2025-10-06 14:36:15', '2025-10-06 14:39:12'),
(19, 8, 2, 50000000.00, '2025-10-10 18:54:00', '2025-10-09 15:11:22', '2025-10-17 14:11:56'),
(20, 6, 5, 3000000.00, '2025-10-09 15:13:23', '2025-10-09 15:13:36', '2025-10-09 15:13:36'),
(21, 4, 5, 500000.00, '2025-10-09 15:13:56', '2025-10-09 15:14:17', '2025-10-09 15:14:17'),
(22, 5, 5, 1500000.00, '2025-10-09 15:14:37', '2025-10-09 15:14:58', '2025-10-09 15:14:58'),
(23, 8, 4, 10000000.00, '2025-10-09 15:15:00', '2025-10-09 15:15:50', '2025-10-10 08:36:58'),
(51, 7, 4, 600000.00, '2025-10-10 12:00:00', '2025-10-10 16:01:55', '2025-10-10 16:01:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `units`
--

INSERT INTO `units` (`id`, `code`, `name`, `note`, `created_at`, `updated_at`) VALUES
(1, 'VIEN', 'Viên', NULL, '2025-10-03 15:39:32', '2025-10-03 15:39:32'),
(2, 'HOP', 'Hộp', NULL, '2025-10-03 15:39:32', '2025-10-03 15:39:32'),
(3, 'M2', 'Mét vuông', NULL, '2025-10-03 15:39:32', '2025-10-03 15:39:32'),
(4, 'THUNG', 'Thùng', NULL, '2025-10-03 15:39:32', '2025-10-03 15:39:32'),
(5, 'CA', 'Cái', NULL, '2025-10-03 16:03:39', '2025-10-09 15:12:46'),
(6, 'DV001', 'Cái', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(7, 'DV002', 'Chiếc', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(8, 'DV003', 'Bộ', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(9, 'DV004', 'Hộp', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(10, 'DV005', 'Thùng', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(11, 'DV006', 'Chai', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(12, 'DV007', 'Lít', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(13, 'DV008', 'Kg', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(14, 'DV009', 'Gam', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(15, 'DV010', 'Mét', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(16, 'DV011', 'Cm', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(17, 'DV012', 'Mm', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(18, 'DV013', 'Tấm', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(19, 'DV014', 'Cuộn', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(20, 'DV015', 'Gói', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(21, 'DV016', 'Túi', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(22, 'DV017', 'Lọ', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(23, 'DV018', 'Viên', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(24, 'DV019', 'Vỉ', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(25, 'DV020', 'Tập', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(26, 'DV021', 'Cuốn', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(27, 'DV022', 'Cây', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(28, 'DV023', 'Bao', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(29, 'DV024', 'Thỏi', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(30, 'DV025', 'Ống', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(31, 'DV026', 'Miếng', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(32, 'DV027', 'Bình', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(33, 'DV028', 'Giỏ', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(34, 'DV029', 'Ly', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12'),
(35, 'DV030', 'Đôi', 'Đơn vị tính phổ biến', '2025-10-21 16:45:12', '2025-10-21 16:45:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `superadmin` smallint DEFAULT '0',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `registration_ip` varchar(15) DEFAULT NULL,
  `bind_to_ip` varchar(255) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_confirmed` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `confirmation_token`, `status`, `superadmin`, `created_at`, `updated_at`, `registration_ip`, `bind_to_ip`, `email`, `email_confirmed`) VALUES
(100, 'superadmin', 'aa3H9wJBKEwPNU0UOaNCChLCUezz6LOb', '$2y$13$cb1lc.DkAo9z8BlmAsWEvugr2a09XZwVLpgWgJ6DKDJthAFCdnbpy', NULL, 1, 1, 1759030802, 1760198653, NULL, NULL, NULL, 0),
(102, 'ttbang', 'YY3ryGEUYj79SLikxl8Lmjvr5zAy-Ggh', '$2y$13$GPmnYp8S8s6lJHDEcOAZmuFSIzDf.zGiN/6pZ1CR6aEbRapVkYDR.', NULL, 1, NULL, 10, 1760631143, NULL, '', '', 0),
(108, 'ttbang1', 'ulosovvoWnIFoZzpGYFEykMLJz_tt2oA', '$2y$13$RXXn.byAfAwm2tKQnbIfNeVG5.HVk0aXBgBwhSPeeY9BUTWGfsEcy', NULL, 1, 0, 1760170252, 1760625097, '127.0.0.1', '', '', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `revoked_by_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx-user_sessions-user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `device_name`, `login_time`, `last_activity`, `logout_time`, `revoked_by_admin`) VALUES
(3, 108, 'i9lj6relthjgpn8lh99s8vvpje', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows / Firefox', '2025-10-16 21:39:59', NULL, '2025-10-16 21:49:18', 0),
(4, 102, '9ctvc7358uakr096gl8271jf9d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows / Firefox', '2025-10-16 22:44:11', '2025-10-16 22:44:11', '2025-10-16 22:58:08', 1),
(5, 100, 'pc2pciufht6k4fkr2d5aqdlvuu', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows / Firefox', '2025-10-16 22:58:20', '2025-10-16 22:58:20', '2025-10-16 22:58:46', 1),
(6, 100, 'uurtkuv9ilavai3ajpt0o7fod4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows / Firefox', '2025-10-16 23:01:15', '2025-10-16 23:01:15', '2025-10-16 23:01:46', 1),
(20, 100, 'uskpopk1918midaaq58khec1gj', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-17 10:32:23', '2025-10-17 10:32:23', NULL, 0),
(36, 108, 'ml12lej2oighlm7jkslggi74g8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-17 13:36:50', '2025-10-17 13:36:50', '2025-10-17 13:48:57', 1),
(37, 108, '04mo76afdhbjfkh4gsc6bka1a7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-17 13:55:04', '2025-10-17 13:55:04', '2025-10-17 13:55:56', 1),
(38, 102, '7hlh7vlo44snbja25aqlrh342j', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-17 14:01:07', '2025-10-17 14:01:07', '2025-10-17 14:01:22', 1),
(39, 108, '8i4farvaieq2u5bbdmfs587f1b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-17 14:18:13', '2025-10-17 14:18:13', '2025-10-17 14:18:58', 1),
(40, 108, '7eelsm382v7pdus6t43u93077b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-17 14:19:31', '2025-10-17 14:19:31', NULL, 0),
(41, 100, 'kg4h6sslr60g562bfti4lamorb', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-18 08:23:37', '2025-10-18 08:23:37', NULL, 0),
(42, 100, '9vveg2l3khpiv2feotpculu0lm', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-19 06:42:33', '2025-10-19 06:42:33', NULL, 0),
(43, 100, 'otv4liurii0lb2r2v83643f4bv', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-19 22:35:53', '2025-10-19 22:35:53', NULL, 0),
(44, 100, 'v12t40n5gvsms2vmu3avjemod8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-20 07:30:17', '2025-10-20 07:30:17', NULL, 0),
(45, 100, 'httauccp671d1i8kg0buljfqp6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-20 07:30:18', '2025-10-20 07:30:18', NULL, 0),
(46, 100, 'gn34af0vut56s7ced55qk1i13h', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-21 07:20:27', '2025-10-21 07:20:27', NULL, 0),
(47, 100, 'u9fi8fnffeu1h40fmgs7v5t7ah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-21 21:21:16', '2025-10-21 21:21:16', NULL, 0),
(48, 100, 'bodaik52u65bm6i7296ivkav7v', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-22 07:48:49', '2025-10-22 07:48:49', NULL, 0),
(49, 100, 'v22tijrc0bqmtp8b81u4gkjrjs', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Windows / Chrome', '2025-10-24 13:23:34', '2025-10-24 13:23:34', NULL, 0),
(50, 102, 't6em7nqa3424254nrcl4v2tbo8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-24 13:35:13', '2025-10-24 13:35:13', '2025-10-24 13:35:26', 0),
(51, 108, 'hmp9q26b5otkrhom5r0l08un34', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-24 13:35:31', '2025-10-24 13:35:31', '2025-10-24 13:52:02', 0),
(52, 102, 'qcubvjpbbmk8rtojii6u0c3rgk', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'Windows / Firefox', '2025-10-24 13:52:07', '2025-10-24 13:52:07', NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_visit_log`
--

DROP TABLE IF EXISTS `user_visit_log`;
CREATE TABLE IF NOT EXISTS `user_visit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `language` char(2) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  `visit_time` int NOT NULL,
  `browser` varchar(30) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb3;

--
-- Đang đổ dữ liệu cho bảng `user_visit_log`
--

INSERT INTO `user_visit_log` (`id`, `token`, `ip`, `language`, `user_agent`, `user_id`, `visit_time`, `browser`, `os`) VALUES
(2, '68d93729469a7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759065897, 'Chrome', 'Windows'),
(3, '68d9409426e77', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759068308, 'Chrome', 'Windows'),
(4, '68d9411e12449', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759068446, 'Firefox', 'Windows'),
(5, '68d9412b3002b', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759068459, 'Firefox', 'Windows'),
(6, '68d94df39bbe6', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759071731, 'Chrome', 'Windows'),
(7, '68d9d1139acad', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759105299, 'Chrome', 'Windows'),
(8, '68da031646813', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759118102, 'Firefox', 'Windows'),
(9, '68da3a555482f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759132245, 'Chrome', 'Windows'),
(10, '68da3beac748e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759132650, 'Chrome', 'Windows'),
(11, '68da3f442e9d1', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759133508, 'Firefox', 'Windows'),
(12, '68da4068ec9c3', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759133800, 'Firefox', 'Windows'),
(13, '68da518662280', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759138182, 'Chrome', 'Windows'),
(14, '68da51a00529a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759138208, 'Chrome', 'Windows'),
(15, '68da531110f86', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759138577, 'Chrome', 'Windows'),
(16, '68da5481a2850', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759138945, 'Chrome', 'Windows'),
(17, '68da549ddba88', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759138973, 'Chrome', 'Windows'),
(18, '68da9de456b15', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759157732, 'Chrome', 'Windows'),
(19, '68daa4ffe3cd7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759159551, 'Chrome', 'Windows'),
(20, '68db22667aecf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759191654, 'Chrome', 'Windows'),
(21, '68db4827c9b4c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759201319, 'Firefox', 'Windows'),
(22, '68db4a881c07b', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759201928, 'Firefox', 'Windows'),
(23, '68db4b91b9b8f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759202193, 'Chrome', 'Windows'),
(24, '68db9721144ea', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759221537, 'Firefox', 'Windows'),
(25, '68db99664471b', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759222118, 'Firefox', 'Windows'),
(26, '68dbd28bd4235', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759236747, 'Chrome', 'Windows'),
(27, '68dbef5524456', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759244117, 'Firefox', 'Windows'),
(28, '68dbefa250807', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759244194, 'Firefox', 'Windows'),
(29, '68dbefca32856', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759244234, 'Firefox', 'Windows'),
(30, '68dbf3505dc94', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759245136, 'Firefox', 'Windows'),
(31, '68dbf5c533fdf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759245765, 'Firefox', 'Windows'),
(32, '68dbf6b38ad85', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759246003, 'Firefox', 'Windows'),
(33, '68dc6fd3984bf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759277011, 'Chrome', 'Windows'),
(34, '68dc71c19a7f2', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759277505, 'Chrome', 'Windows'),
(35, '68dce9f5867a1', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759308277, 'Firefox', 'Windows'),
(36, '68dcea046d801', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759308292, 'Firefox', 'Windows'),
(37, '68dcf13d5d678', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759310141, 'Chrome', 'Windows'),
(38, '68dcf1b59601e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759310261, 'Firefox', 'Windows'),
(39, '68dcf3fcd69db', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759310844, 'Chrome', 'Windows'),
(40, '68dcf40e6f632', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759310862, 'Chrome', 'Windows'),
(41, '68dcf4f84769e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759311096, 'Chrome', 'Windows'),
(42, '68dcf5bcdb666', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759311292, 'Chrome', 'Windows'),
(43, '68dee1c3e169f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759437251, 'Chrome', 'Windows'),
(44, '68df2857501d5', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759455319, 'Chrome', 'Windows'),
(45, '68df28df5a6d2', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759455455, 'Chrome', 'Windows'),
(46, '68df40f388677', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759461619, 'Chrome', 'Windows'),
(47, '68df41800c69a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759461760, 'Firefox', 'Windows'),
(48, '68df41f078129', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759461872, 'Chrome', 'Windows'),
(49, '68df426c75a5d', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759461996, 'Chrome', 'Windows'),
(50, '68df42b83ccb1', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759462072, 'Chrome', 'Windows'),
(51, '68df442d043cf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759462445, 'Chrome', 'Windows'),
(52, '68df449718168', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759462551, 'Chrome', 'Windows'),
(53, '68df470313820', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759463171, 'Chrome', 'Windows'),
(54, '68df4711becf0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759463185, 'Firefox', 'Windows'),
(55, '68df6ecc0dcf0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759473356, 'Chrome', 'Windows'),
(56, '68e0716dadae1', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759539565, 'Chrome', 'Windows'),
(57, '68e29ae55a284', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759681253, 'Firefox', 'Windows'),
(58, '68e29aef3d936', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759681263, 'Firefox', 'Windows'),
(59, '68e29b41b2d30', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759681345, 'Firefox', 'Windows'),
(60, '68e29c6c57bcf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759681644, 'Chrome', 'Windows'),
(61, '68e2b9bb952f7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759689147, 'Chrome', 'Windows'),
(62, '68e30cfc48d57', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759710460, 'Chrome', 'Windows'),
(63, '68e31c91084ec', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759714449, 'Chrome', 'Windows'),
(64, '68e35e89dfa8e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759731337, 'Chrome', 'Windows'),
(65, '68e35e9f7a6cb', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 100, 1759731359, 'Chrome', 'Windows'),
(66, '68e672c3709a7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1759933123, 'Chrome', 'Windows'),
(67, '68e672ce05202', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1759933134, 'Chrome', 'Windows'),
(68, '68e70237b9531', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1759969847, 'Chrome', 'Windows'),
(69, '68e76d0d98cb3', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1759997197, 'Firefox', 'Windows'),
(70, '68e7a132e9db3', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760010546, 'Chrome', 'Windows'),
(71, '68e853caddc4c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760056266, 'Chrome', 'Windows'),
(72, '68e8e5de592a8', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760093662, 'Chrome', 'Windows'),
(73, '68e91fae024ec', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760108462, 'Chrome', 'Windows'),
(74, '68e923b392c23', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 102, 1760109491, 'Chrome', 'Windows'),
(75, '68e923d4c4efa', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760109524, 'Chrome', 'Windows'),
(76, '68e9aa60a3e97', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760143968, 'Chrome', 'Windows'),
(77, '68e9c73688982', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 102, 1760151350, 'Chrome', 'Windows'),
(78, '68e9c7415b225', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760151361, 'Chrome', 'Windows'),
(79, '68e9ca267b7fd', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 102, 1760152102, 'Chrome', 'Windows'),
(80, '68e9ca56acb9c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760152150, 'Chrome', 'Windows'),
(81, '68e9cab34f0b5', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760152243, 'Firefox', 'Windows'),
(82, '68e9cac7736eb', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760152263, 'Firefox', 'Windows'),
(83, '68e9cdcd4c58c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760153037, 'Firefox', 'Windows'),
(84, '68ea114969b3a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760170313, 'Firefox', 'Windows'),
(85, '68ea201493715', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760174100, 'Chrome', 'Windows'),
(86, '68ea203f40f99', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760174143, 'Chrome', 'Windows'),
(87, '68ea20aa1e34c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760174250, 'Chrome', 'Windows'),
(88, '68ea20b555f2e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760174261, 'Chrome', 'Windows'),
(89, '68ea29e51dce4', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760176613, 'Chrome', 'Windows'),
(90, '68ea7804a32c7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760196612, 'Chrome', 'Windows'),
(91, '68ea7da962643', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760198057, 'Firefox', 'Windows'),
(92, '68ea7dbee0ff0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760198078, 'Firefox', 'Windows'),
(93, '68ea7deec1901', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760198126, 'Firefox', 'Windows'),
(94, '68ea80417b60c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760198721, 'Firefox', 'Windows'),
(95, '68ea81ba69d11', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760199098, 'Chrome', 'Windows'),
(96, '68eb27f299ed9', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760241650, 'Chrome', 'Windows'),
(97, '68ec711944765', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760325913, 'Chrome', 'Windows'),
(98, '68ecd5c591716', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760351685, 'Chrome', 'Windows'),
(99, '68ed96ae29228', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760401070, 'Chrome', 'Windows'),
(100, '68eda0d265c6c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760403666, 'Firefox', 'Windows'),
(101, '68edaad877602', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760406232, 'Firefox', 'Windows'),
(102, '68ee5c1cb8cf5', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760451612, 'Chrome', 'Windows'),
(103, '68eee7c026866', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760487360, 'Chrome', 'Windows'),
(104, '68ef3ecdb67a7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760509645, 'Chrome', 'Windows'),
(105, '68f03b50be87c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760574288, 'Chrome', 'Windows'),
(106, '68f0e72dc037f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760618285, 'Chrome', 'Windows'),
(107, '68f0fd6ccd499', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760623980, 'Firefox', 'Windows'),
(108, '68f0fe459e1b2', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760624197, 'Firefox', 'Windows'),
(109, '68f101de4492e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760625118, 'Firefox', 'Windows'),
(110, '68f103bf49322', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760625599, 'Firefox', 'Windows'),
(111, '68f111e1d1d7c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760629217, 'Firefox', 'Windows'),
(112, '68f112cb2b48e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760629451, 'Firefox', 'Windows'),
(113, '68f1161c5801c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760630300, 'Firefox', 'Windows'),
(114, '68f116cbbdcaf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 100, 1760630475, 'Firefox', 'Windows'),
(115, '68f11998d16f5', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 102, 1760631192, 'Firefox', 'Windows'),
(116, '68f121aba95b0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 108, 1760633259, 'Firefox', 'Windows'),
(117, '68f18b1d98b8f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760660253, 'Chrome', 'Windows'),
(118, '68f18c0115a0e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760660481, 'Firefox', 'Windows'),
(119, '68f18c0e39452', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760660494, 'Firefox', 'Windows'),
(120, '68f18ef6f0607', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760661238, 'Firefox', 'Windows'),
(121, '68f1999b4f6a6', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760663963, 'Firefox', 'Windows'),
(122, '68f199b8239e0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760663992, 'Firefox', 'Windows'),
(123, '68f19b3282404', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760664370, 'Firefox', 'Windows'),
(124, '68f19be921e8c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760664553, 'Firefox', 'Windows'),
(125, '68f19d10c6531', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760664848, 'Firefox', 'Windows'),
(126, '68f1a912c47d9', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760667922, 'Firefox', 'Windows'),
(127, '68f1a933a1f03', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760667955, 'Firefox', 'Windows'),
(128, '68f1a96784398', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668007, 'Firefox', 'Windows'),
(129, '68f1a99a873ae', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668058, 'Firefox', 'Windows'),
(130, '68f1aa287e2a3', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668200, 'Firefox', 'Windows'),
(131, '68f1aae1bc55f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668385, 'Firefox', 'Windows'),
(132, '68f1ac76cedd6', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668790, 'Firefox', 'Windows'),
(133, '68f1ac97a5925', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668823, 'Firefox', 'Windows'),
(134, '68f1acc179031', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668865, 'Firefox', 'Windows'),
(135, '68f1ace4de69a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760668900, 'Firefox', 'Windows'),
(136, '68f1ae463300c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760669254, 'Firefox', 'Windows'),
(137, '68f1ae71da145', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760669297, 'Firefox', 'Windows'),
(138, '68f1af842244d', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760669572, 'Firefox', 'Windows'),
(139, '68f1b07cd5052', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760669820, 'Firefox', 'Windows'),
(140, '68f1b0bb8299a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 102, 1760669883, 'Firefox', 'Windows'),
(141, '68f1b215935ca', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760670229, 'Firefox', 'Windows'),
(142, '68f1b24118df1', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760670273, 'Firefox', 'Windows'),
(143, '68f1b2d4b2dee', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760670420, 'Firefox', 'Windows'),
(144, '68f1b4db3bb06', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760670939, 'Firefox', 'Windows'),
(145, '68f1b5c6aac4a', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760671174, 'Firefox', 'Windows'),
(146, '68f1b88cd62ac', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760671884, 'Firefox', 'Windows'),
(147, '68f1b8c71c9b7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760671943, 'Chrome', 'Windows'),
(148, '68f1b97f0269b', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760672127, 'Firefox', 'Windows'),
(149, '68f1b9fd1e219', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760672253, 'Firefox', 'Windows'),
(150, '68f1ba26079e3', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760672294, 'Firefox', 'Windows'),
(151, '68f1bae6edf7e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760672486, 'Firefox', 'Windows'),
(152, '68f1bbdcb3a13', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760672732, 'Firefox', 'Windows'),
(153, '68f1bd1d1a7b8', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673053, 'Firefox', 'Windows'),
(154, '68f1be2a89024', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673322, 'Firefox', 'Windows'),
(155, '68f1bf49a6720', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673609, 'Firefox', 'Windows'),
(156, '68f1bf6562a76', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673637, 'Firefox', 'Windows'),
(157, '68f1bf81eddac', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673665, 'Firefox', 'Windows'),
(158, '68f1bfc4a6d72', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760673732, 'Firefox', 'Windows'),
(159, '68f1c0f103c53', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760674033, 'Firefox', 'Windows'),
(160, '68f1cad1da578', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760676561, 'Firefox', 'Windows'),
(161, '68f1cbec3fa3f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760676844, 'Firefox', 'Windows'),
(162, '68f1ce62362e8', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760677474, 'Firefox', 'Windows'),
(163, '68f1e4021e098', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760683010, 'Firefox', 'Windows'),
(164, '68f1e8489300e', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760684104, 'Firefox', 'Windows'),
(165, '68f1e9b387613', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 102, 1760684467, 'Firefox', 'Windows'),
(166, '68f1edb57261c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760685493, 'Firefox', 'Windows'),
(167, '68f1ee03598d7', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1760685571, 'Firefox', 'Windows'),
(168, '68f2ec1906ecd', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760750617, 'Chrome', 'Windows'),
(169, '68f425e96bf3b', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760830953, 'Chrome', 'Windows'),
(170, '68f505595f5bf', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 100, 1760888153, 'Firefox', 'Windows'),
(171, '68f58299a3bf0', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760920217, 'Chrome', 'Windows'),
(172, '68f5829a7265d', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1760920218, 'Chrome', 'Windows'),
(173, '68f6d1cb4fd59', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1761006027, 'Chrome', 'Windows'),
(174, '68f796dc9c66c', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1761056476, 'Chrome', 'Windows'),
(175, '68f829f177e35', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1761094129, 'Chrome', 'Windows'),
(176, '68fb1b663b287', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 100, 1761287014, 'Chrome', 'Windows'),
(177, '68fb1e215de55', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 102, 1761287713, 'Firefox', 'Windows'),
(178, '68fb1e3345bf2', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 108, 1761287731, 'Firefox', 'Windows'),
(179, '68fb22176f74f', '127.0.0.1', 'vi', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 102, 1761288727, 'Firefox', 'Windows');

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `al_cut_groups`
--
ALTER TABLE `al_cut_groups`
  ADD CONSTRAINT `fk_al_cut_groups_material` FOREIGN KEY (`material_id`) REFERENCES `al_aluminum_materials` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_al_cut_groups_order` FOREIGN KEY (`order_id`) REFERENCES `al_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `al_reuse_log`
--
ALTER TABLE `al_reuse_log`
  ADD CONSTRAINT `fk_al_reuse_log_cut_group` FOREIGN KEY (`used_in_cut_group_id`) REFERENCES `al_cut_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_al_reuse_log_scrap` FOREIGN KEY (`scrap_id`) REFERENCES `al_scrap_aluminum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `al_scrap_aluminum`
--
ALTER TABLE `al_scrap_aluminum`
  ADD CONSTRAINT `fk_al_scrap_aluminum_cut_group` FOREIGN KEY (`cut_group_id`) REFERENCES `al_cut_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_al_scrap_aluminum_material` FOREIGN KEY (`material_id`) REFERENCES `al_aluminum_materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_assignment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auth_item_group_code` FOREIGN KEY (`group_code`) REFERENCES `auth_item_group` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk-invoice-customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `invoice_detail`
--
ALTER TABLE `invoice_detail`
  ADD CONSTRAINT `fk_invoice_detail_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoice_detail_price_unit` FOREIGN KEY (`product_price_unit_id`) REFERENCES `product_prices_unit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk-products-category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_prices_unit`
--
ALTER TABLE `product_prices_unit`
  ADD CONSTRAINT `fk-product_rices_unit-product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-product_rices_unit-unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_visit_log`
--
ALTER TABLE `user_visit_log`
  ADD CONSTRAINT `user_visit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
