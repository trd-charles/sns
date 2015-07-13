SET SESSION FOREIGN_KEY_CHECKS=0;


/* Create Tables */

CREATE TABLE sample
(
	id int NOT NULL AUTO_INCREMENT COMMENT 'サンプルID',
	value varchar(255) COMMENT '値',
	PRIMARY KEY (id)
) COMMENT = 'サンプル' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



