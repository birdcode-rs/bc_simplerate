#
# Table structure for table 'tx_bcsimplerate_domain_model_rate'
#
CREATE TABLE tx_bcsimplerate_domain_model_rate (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	rate varchar(255) DEFAULT '' NOT NULL,
	recordid int(11) DEFAULT '0' NOT NULL,
	tablename varchar(255) DEFAULT '' NOT NULL,
	note varchar(255) DEFAULT '' NOT NULL,
	feuser int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	recordlanguage int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY recordid_tablename (tablename(32),recordid)
);
 
