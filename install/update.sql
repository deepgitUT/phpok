ALTER TABLE `qinggan_adm`  ADD `category` LONGTEXT NOT NULL COMMENT '�ɲ����ķ���ID��ϵͳ����Ա��Ч' ;

-- ɾ����Ա����չ�ֶ�

ALTER TABLE `qinggan_user_group`
  DROP `read_popedom`,
  DROP `post_popedom`,
  DROP `reply_status`,
  DROP `post_status`;

ALTER TABLE `qinggan_user_group`  ADD `popedom` LONGTEXT NOT NULL COMMENT 'ǰ��Ȩ��' ;


ALTER TABLE `qinggan_module_fields`  ADD `is_front` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0ǰ�˲�����1ǰ�˿���' ;

ALTER TABLE `qinggan_adm`  ADD `vpass` VARCHAR(50) NOT NULL COMMENT '����������֤' ;

CREATE TABLE IF NOT EXISTS `qinggan_list_tag` (
  `id` int(10) unsigned NOT NULL COMMENT '����ID',
  `title_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID',
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TAG��ǩID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�����е�Tag������';