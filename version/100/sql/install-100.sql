CREATE TABLE IF NOT EXISTS `xplugin_endereco_jtl4_phs_cconfs` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(8),
    `value` TEXT,
    `last_change_at` timestamp NOT NULL DEFAULT NOW()
);

CREATE TABLE `xplugin_endereco_jtl4_phs_checked_numbers` (
    `number` varchar(126) NOT NULL,
    `format` varchar(16) NOT NULL,
    `country` varchar(2) NOT NULL,
    `status` text NOT NULL,
    `predictions` text NOT NULL,
    `last_change_at` timestamp NOT NULL DEFAULT NOW()
);
ALTER TABLE `xplugin_endereco_jtl4_phs_checked_numbers`
    ADD INDEX `number` (`number`),
    ADD INDEX `format` (`format`),
    ADD INDEX `country` (`country`),
    ADD UNIQUE `number_unq` (`number`);
