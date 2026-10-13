-- Schema for Energy Monitor IoT.
-- Historical measurement records are intentionally not included.

CREATE TABLE IF NOT EXISTS `pzem_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `voltage` FLOAT DEFAULT NULL,
  `current` FLOAT DEFAULT NULL,
  `power` FLOAT DEFAULT NULL,
  `energy` FLOAT DEFAULT NULL,
  `frequency` FLOAT DEFAULT NULL,
  `pf` FLOAT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
