-- ����ʱ�䣺2015��01��22�� 01ʱ38��
DROP TABLE IF EXISTS `qinggan_tag`;
CREATE TABLE IF NOT EXISTS `qinggan_tag` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `site_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'վ��ID',
  `title` varchar(255) NOT NULL COMMENT '����',
  `url` varchar(255) NOT NULL COMMENT '�ؼ�����ַ',
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0ԭ���ڴ򿪣�1�´��ڴ�',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�������',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�ؼ��ֹ�����' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- ��Ľṹ `qinggan_tag_stat`
--

DROP TABLE IF EXISTS `qinggan_tag_stat`;
CREATE TABLE IF NOT EXISTS `qinggan_tag_stat` (
  `title_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����ID',
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TAG��ǩID',
  PRIMARY KEY (`title_id`,`tag_id`),
  KEY `title_id` (`title_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tag����ͳ��';

DROP TABLE IF EXISTS `qinggan_list_tag`;