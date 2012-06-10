############################################################
# Table definitions
############################################################

CREATE TABLE contacts
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	user_id				INT UNSIGNED NOT NULL,						#
	email				VARCHAR(255) NOT NULL,						#
	name				VARCHAR(100) NOT NULL,						#
	subject				VARCHAR(255) NOT NULL,						#
	text				TEXT NOT NULL,								#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY (module_id)
);

CREATE TABLE emails
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	user_id				INT UNSIGNED NOT NULL,						#
	subject				VARCHAR(255) NOT NULL,						#
	email				VARCHAR(255) NOT NULL,						#
	text				TEXT NOT NULL,								#
	status				VARCHAR(10) NOT NULL,						#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY (module_id)
);

CREATE TABLE requests
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	user_id				INT UNSIGNED NOT NULL,						#
	status				VARCHAR(20) NOT NULL,						#
	type				VARCHAR(10) NOT NULL,						#
	code				VARCHAR(50) NOT NULL,						#
	request				VARCHAR(100) NOT NULL,						#
	updated				INT UNSIGNED NOT NULL,						#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY	(module_id)
);

CREATE TABLE users
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	type				VARCHAR(20) NOT NULL,						# (administrator, user, pending, deleted, curator)
	name				VARCHAR(100) NOT NULL,						#
	password			VARCHAR(128) NOT NULL,						#
	salt				VARCHAR(128) NOT NULL,						#
	email				VARCHAR(255) NOT NULL,						#
	updated				INT UNSIGNED NOT NULL,						#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY	(module_id)
);

CREATE TABLE referredusers
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	referral_id			INT UNSIGNED NOT NULL,						#
	user_id				INT UNSIGNED NOT NULL,						#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY (module_id)
);

CREATE TABLE userreferralcodes
(
	module_id			INT UNSIGNED NOT NULL AUTO_INCREMENT,		#
	user_id				INT UNSIGNED NOT NULL,						#
	code				VARCHAR(100) NOT NULL,						#
	created				INT UNSIGNED NOT NULL,						#
	PRIMARY KEY (module_id)
);

