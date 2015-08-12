ALTER TABLE  `qinggan_site` ADD  `html_content_type` VARCHAR( 255 ) NOT NULL DEFAULT  'empty',
ADD  `html_root_dir` VARCHAR( 255 ) NOT NULL DEFAULT  'html/';


CREATE TABLE  `qinggan_user_relation` (
`uid` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '�û�ID',
`introducer` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '������ID',
`dateline` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '����ʱ��'
) ENGINE = MYISAM COMMENT =  '��Ա���ܹ�ϵͼ';


CREATE TABLE IF NOT EXISTS `qinggan_wealth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '�Ƹ�ID',
  `site_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'վ��ID',
  `title` varchar(100) NOT NULL COMMENT '�Ʋ�����',
  `identifier` varchar(100) NOT NULL COMMENT '��ʶ������Ӣ���ַ�',
  `unit` varchar(100) NOT NULL COMMENT '��λ����',
  `dnum` tinyint(1) NOT NULL DEFAULT '0' COMMENT '������λС����Ϊ0��ʾֻȡ����',
  `ifpay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�֧�ֳ�ֵ',
  `ratio` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '�һ���������1Ԫ���Զһ����٣�Ϊ0��֧�ֳ�ֵ��Ϊ1��ʾ1��1����֧��С��',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0��ʹ��1ʹ��',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '����0-255��ԽСԽ��ǰ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�Ƹ�����' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `qinggan_wealth_info` (
  `wid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID���ԱID�����ID����ĿID',
  `lasttime` int(11) NOT NULL DEFAULT '0' COMMENT '���һ�θ���ʱ��',
  `val` float unsigned NOT NULL DEFAULT '0' COMMENT '��С�Ƹ�Ϊ0�������Ǹ������',
  PRIMARY KEY (`wid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�Ƹ�����';


CREATE TABLE IF NOT EXISTS `qinggan_wealth_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `wid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�Ƹ�ID',
  `goal_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Ŀ���ԱID',
  `mid` varchar(100) NOT NULL COMMENT '����ID����',
  `val` int(11) NOT NULL DEFAULT '0' COMMENT '�������ű�ʾ���ӣ������ű�ʾ��ȥ',
  `note` varchar(255) NOT NULL COMMENT '����ժҪ',
  `appid` enum('admin','www','api') NOT NULL DEFAULT 'www' COMMENT '�����ĸ��ӿ�',
  `dateline` int(11) NOT NULL DEFAULT '0' COMMENT 'д��ʱ��',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '��ԱID��Ϊ0�ǻ�Ա',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ԱID��Ϊ0�ǹ���Ա',
  `ctrlid` varchar(100) NOT NULL COMMENT '������ID',
  `funcid` varchar(100) NOT NULL COMMENT '����ID',
  `url` varchar(255) NOT NULL COMMENT 'ִ�е�URL',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�Ƹ���ȡ��������־' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `qinggan_wealth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `wid` int(10) unsigned NOT NULL COMMENT '�Ʋ�ID',
  `action` varchar(255) NOT NULL COMMENT '��������',
  `repeat` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0��֧���ظ�1֧�ֶ��',
  `mintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�ڶ��������ظ�����Ч�ģ�Ϊ0��ʾ����ʱ',
  `val` varchar(255) NOT NULL DEFAULT '0' COMMENT 'ֵ����ֵ��ʾ��������0��ʾ�ӣ�֧�ּ�����price*2',
  `goal` enum('user','introducer') NOT NULL DEFAULT 'user' COMMENT 'Ŀ������user��Աintroducer������',
  `efunc` varchar(255) NOT NULL COMMENT '�Զ���ִ�еĺ���',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '����',
  `linkid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�����⣬0������1����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�Ƹ����ɹ���' AUTO_INCREMENT=1 ;

ALTER TABLE  `qinggan_user` ADD  `invoice_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '��Ʊ����' AFTER  `integral` ,
ADD  `invoice_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '��Ʊ̧ͷ' AFTER  `invoice_type` ;

ALTER TABLE `qinggan_user`  ADD `post_date` int(10) unsigned NOT NULL default '0' COMMENT '���ε�½ʱ��' ;
ALTER TABLE `qinggan_user`  ADD `pdip` varchar(100) NOT NULL COMMENT '���ε�½IP' ;
ALTER TABLE `qinggan_user`  ADD `lasttime` int(10) unsigned NOT NULL default '0' COMMENT '�ϴε�½ʱ��' ;
ALTER TABLE `qinggan_user`  ADD `lastip` varchar(100) NOT NULL COMMENT '�ϴε�½IP' ;

ALTER TABLE  `qinggan_site_domain` ADD  `is_mobile` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '1������ǿ��Ϊ�ֻ���';

CREATE TABLE  `qinggan_order_invoice` (
`order_id` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '����ID��',
`type` VARCHAR( 100 ) NOT NULL COMMENT  '��Ʊ����',
`title` VARCHAR( 255 ) NOT NULL COMMENT  '��Ʊ̧ͷ',
`content` TEXT NOT NULL COMMENT  '��Ʊ����',
PRIMARY KEY (  `order_id` )
) ENGINE = MYISAM COMMENT =  '������Ʊ';

CREATE TABLE IF NOT EXISTS `qinggan_user_invoice` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID��',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '��ԱID',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�Ĭ��',
  `type` varchar(100) NOT NULL COMMENT '��Ʊ����',
  `title` varchar(255) NOT NULL COMMENT '��Ʊ̧ͷ',
  `content` text NOT NULL COMMENT '��Ʊ����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='��Ա��Ʊ��¼';


ALTER TABLE  `qinggan_project` ADD `cate_multiple` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0���൥ѡ1����֧�ֶ�ѡ';=======
ALTER TABLE  `qinggan_all` ADD  `status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0��ʹ��1����ʹ��' AFTER  `title` ;>>>>>>> .r1735
