ALTER TABLE  `qinggan_res_cate` ADD  `filetypes` VARCHAR( 255 ) NOT NULL COMMENT  '��������',
ADD  `gdtypes` VARCHAR( 255 ) NOT NULL COMMENT  '֧�ֵ�GD���������GD������Ӣ��ID�ֿ�',
ADD  `gdall` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1֧��ȫ��GD����0��֧��ָ����GD����';



ALTER TABLE  `qinggan_res_cate` ADD  `typeinfo` VARCHAR( 200 ) NOT NULL COMMENT  '����˵��' AFTER  `filetypes`;

ALTER TABLE `qinggan_res_ext` DROP `x1`, DROP `y1`, DROP `x2`, DROP `y2`, DROP `w`, DROP `h`;


ALTER TABLE  `qinggan_res` ADD  `admin_id` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '����ԱID';