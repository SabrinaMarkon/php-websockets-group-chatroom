CREATE TABLE `adminsettings` (
id integer unsigned not null primary key auto_increment,
adminuser varchar(255) not null,
adminpass varchar(255) not null,
adminname varchar(255) not null,
adminemail varchar(255) not null,
sitename varchar(255) not null,
domain varchar(255) not null
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `adminnotes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL default '',
  `htmlcode` longtext NOT NULL,
  KEY `index` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE mail (
`id` int(10) unsigned not null auto_increment,
username varchar(255) not null default 'admin',
`subject` varchar(255) not null,
message longtext not null,
needtosend char(1) not null default '0',
sent datetime DEFAULT NULL,
clicks int(11) not null default '0',
save char(1) not null default '0',
PRIMARY KEY (`id`),
KEY mail_username_foreign (username)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table members (
id integer unsigned not null primary key auto_increment,
username varchar(255) not null unique,
password varchar(255) not null,
accounttype varchar(255) not null default 'Member',
firstname varchar(255) not null,
lastname varchar(255) not null,
email varchar(255) not null,
login_status tinyint(1) not null default '0',
signupdate datetime not null,
signupip varchar(255) not null,
verified varchar(4) not null default 'no',
verifieddate datetime not null,
lastlogin datetime not null
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `pages` (
  `id` int(10) unsigned not null auto_increment,
  `name` varchar(255) not null,
  `htmlcode` longtext not null,
  `slug` varchar(255) not null,
  `core` varchar(4) not null default 'no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

insert into adminsettings (adminuser, adminpass, adminname, adminemail, sitename, domain) values ('Admin', 'admin', 'YOUR NAME', 'YOUR ADMIN EMAIL', 'YOUR SITE NAME','http://YOURDOMAIN.COM');

INSERT INTO `adminnotes` (`id`, `name`, `htmlcode`) values (1, 'Admin Notes', '');

INSERT INTO pages (name, htmlcode, slug, core) values ('Home Page', '', '', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Login Page', '', 'login', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Terms and Conditions', '', 'terms', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Main Page', '', 'members', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Profile Page', '', 'profile', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Chatroom Page', '', 'chatroom', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Registration Page', '', 'register', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Logout Page', '', 'logout', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('About Us Page', '', 'aboutus', 'yes');

