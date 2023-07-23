CREATE TABLE IF NOT EXISTS `b_awz_bxorm_hooks` (
    `ID` int(18) NOT NULL AUTO_INCREMENT,
    `NAME` varchar(64) NOT NULL,
    `TOKEN` varchar(32) NOT NULL,
    `ACTIVE` varchar(1) NOT NULL,
    `METHODS` longtext NOT NULL,
    PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `b_awz_bxorm_methods` (
    `ID` int(18) NOT NULL AUTO_INCREMENT,
    `NAME` varchar(64) NOT NULL,
    `CODE` varchar(32) NOT NULL,
    `ENTITY` varchar(256) NOT NULL,
    `ACTIVE` varchar(1) NOT NULL,
    `PARAMS` longtext NOT NULL,
    `MODULES` varchar(625) NOT NULL,
    PRIMARY KEY (`ID`)
);