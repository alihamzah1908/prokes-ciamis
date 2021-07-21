/*
 Navicat Premium Data Transfer

 Source Server         : localnew
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : localhost:3306
 Source Schema         : prokes

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 12/07/2021 11:33:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dokumen_individu
-- ----------------------------
DROP TABLE IF EXISTS `dokumen_individu`;
CREATE TABLE `dokumen_individu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `individu_id` int(11) NULL DEFAULT NULL,
  `kode_desa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kode_kecamatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tanggal_pantau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `lokasi_pantau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `deskripsi_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `deleted_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
