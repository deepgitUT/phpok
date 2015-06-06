ALTER TABLE  `qinggan_res_cate` ADD  `filetypes` VARCHAR( 255 ) NOT NULL COMMENT  '��������',
ADD  `gdtypes` VARCHAR( 255 ) NOT NULL COMMENT  '֧�ֵ�GD���������GD������Ӣ��ID�ֿ�',
ADD  `gdall` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1֧��ȫ��GD����0��֧��ָ����GD����';



ALTER TABLE  `qinggan_res_cate` ADD  `typeinfo` VARCHAR( 200 ) NOT NULL COMMENT  '����˵��' AFTER  `filetypes`;

ALTER TABLE `qinggan_res_ext` DROP `x1`, DROP `y1`, DROP `x2`, DROP `y2`, DROP `w`, DROP `h`;


ALTER TABLE  `qinggan_res` ADD  `admin_id` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '����ԱID';


CREATE TABLE IF NOT EXISTS `qinggan_list_cate` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID',
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID',
  `site_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'վ��ID',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '��ĿID',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ģ��ID',
  PRIMARY KEY (`id`,`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='����󶨵ķ���';

-- ���������µķ�����Ϣ
INSERT INTO `qinggan_list_cate`(id,cate_id,site_id,project_id,module_id) SELECT id,cate_id,site_id,project_id,module_id FROM `qinggan_list` WHERE cate_id>0 AND module_id>0;


ALTER TABLE  `qinggan_project` ADD  `is_userid` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '�Ƿ�󶨻�Ա' AFTER  `is_biz` ,
ADD  `is_tpl_content` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '�Ƿ��Զ�������ģ��' AFTER  `is_userid`;
