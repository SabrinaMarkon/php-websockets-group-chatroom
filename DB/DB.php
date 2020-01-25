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

create table chatroom (
id integer unsigned not null primary key auto_increment,
username varchar(255) not null,
msg text not null,
created_on datetime not null
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

CREATE TABLE `members` (
  `id` int(10) UNSIGNED NOT NULL primary key auto_increment,
  `username` varchar(255) NOT NULL unique,
  `password` varchar(255) NOT NULL,
  `accounttype` varchar(255) NOT NULL DEFAULT 'Member',
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `signupdate` datetime NOT NULL,
  `signupip` varchar(255) NOT NULL,
  `verified` varchar(4) NOT NULL DEFAULT 'no',
  `verifieddate` datetime,
  `verifiedcode` varchar(50),
  `lastlogin` datetime,
  `login_status` tinyint(1) NOT NULL DEFAULT '0',
  `resourceId` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `pages` (
  `id` int(10) unsigned not null auto_increment,
  `name` varchar(255) not null,
  `htmlcode` longtext not null,
  `slug` varchar(255) not null,
  `core` varchar(4) not null default 'no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

insert into adminsettings (adminuser, adminpass, adminname, adminemail, sitename, domain) values ('adminuser', 'adminpass', 'YOUR NAME', 'YOUR ADMIN EMAIL', 'YOUR SITE NAME','http://YOURDOMAIN.COM');

INSERT INTO `adminnotes` (`id`, `name`, `htmlcode`) values (1, 'Admin Notes', '');

INSERT INTO pages (name, htmlcode, slug, core) values ('Home Page', '', '', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Login Page', '', 'login', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Terms and Conditions', '', 'terms', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Main Page', '', 'main', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Profile Page', '', 'profile', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Members Area Chatroom Page', '', 'chatroom', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Registration Page', '', 'register', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('Logout Page', '', 'logout', 'yes');
INSERT INTO pages (name, htmlcode, slug, core) values ('About Us Page', '', 'aboutus', 'yes');

