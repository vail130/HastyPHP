############################################################
# Table definitions
############################################################

CREATE TABLE users
(
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email           VARCHAR(255) NOT NULL,
  type            VARCHAR(100) NOT NULL,
  status          VARCHAR(100) NOT NULL,
  hash            VARCHAR(255) NOT NULL,
  salt            VARCHAR(255) NOT NULL,
  first_name      VARCHAR(100) NOT NULL,
  last_name       VARCHAR(100) NOT NULL,
  organization    VARCHAR(100) NOT NULL,
  street_1        VARCHAR(100) NOT NULL,
  street_2        VARCHAR(100) NOT NULL,
  city            VARCHAR(100) NOT NULL,
  state           VARCHAR(100) NOT NULL,
  zip             VARCHAR(50) NOT NULL,
  phone           VARCHAR(50) NOT NULL,
  updated         INT UNSIGNED NOT NULL,
  created         INT UNSIGNED NOT NULL,
  PRIMARY KEY  (id)
);

CREATE TABLE userrequests
(
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED NOT NULL,
  type            VARCHAR(100) NOT NULL,
  code            VARCHAR(255) NOT NULL,
  request         VARCHAR(255) NOT NULL,
  status          VARCHAR(100) NOT NULL,
  updated         INT UNSIGNED NOT NULL,
  created         INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE emails
(
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED NOT NULL,
  subject         VARCHAR(255) NOT NULL,
  email           VARCHAR(255) NOT NULL,
  html            TEXT NOT NULL,
  text            TEXT NOT NULL,
  type            VARCHAR(100) NOT NULL,
  status          VARCHAR(100) NOT NULL,
  updated         INT UNSIGNED NOT NULL,
  created         INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE items
(
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title             VARCHAR(100) NOT NULL,
  updated           INT UNSIGNED NOT NULL,
  created           INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE itemtags
(
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id           INT UNSIGNED NOT NULL,
  tag               VARCHAR(100) NOT NULL,
  type              VARCHAR(100) NOT NULL,
  created           INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE itemimages
(
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id           INT UNSIGNED NOT NULL,
  name              VARCHAR(100) NOT NULL,
  ext               VARCHAR(5) NOT NULL,
  num               INT UNSIGNED NOT NULL,
  width             INT UNSIGNED NOT NULL,
  height            INT UNSIGNED NOT NULL,
  created           INT UNSIGNED NOT NULL,
  PRIMARY KEY (id)
);

