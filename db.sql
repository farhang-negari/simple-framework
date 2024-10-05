
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for urls
-- ----------------------------
DROP TABLE IF EXISTS `urls`;
CREATE TABLE `urls`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `short_code` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `url` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`, `short_code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of urls
-- ----------------------------
INSERT INTO `urls` VALUES (1, 'e2e3e', 'https://www.google.com');
INSERT INTO `urls` VALUES (2, '52347h', 'https://test.com');
INSERT INTO `urls` VALUES (4, 'nLvvG', 'https://wee.google.com');
INSERT INTO `urls` VALUES (5, 'MRf0v', 'https://wee.googledsf.com');
INSERT INTO `urls` VALUES (10, 'gzBba', 'https://wee.googlsdfsdfgdssdfsdedsf.com');
INSERT INTO `urls` VALUES (11, 'oBU6K', 'https://wee.googlsdsdffsdfgdssdfsdedsf.com');
INSERT INTO `urls` VALUES (12, 'u4X3b', 'https://wee.googlsdsdsdfffsdfgdssdfsdedsf.com');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `full_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `active` bit(1) NULL DEFAULT b'1',
  PRIMARY KEY (`id`, `username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'farhang', '$2y$10$Rkh9/uvLAXahGnUGxTcqVuyH8Bw7y.9aH2dq320wo.5tQjwUsz8W2', 'farhang', b'1');

SET FOREIGN_KEY_CHECKS = 1;
