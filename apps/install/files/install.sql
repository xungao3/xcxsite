CREATE TABLE IF NOT EXISTS `xg_db_table_admin` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `groupid` tinyint(4) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `auth` text NOT NULL,
  PRIMARY KEY (`userid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_app_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thid` int(11) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_comment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `infoid` int(10) UNSIGNED NOT NULL,
  `model` varchar(20) NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(2) NOT NULL,
  `ip` varchar(80) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_data_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `thid` int(11) NOT NULL,
  `sys` varchar(30) NOT NULL,
  `bid` int(11) NOT NULL,
  `infoid` int(11) NOT NULL,
  `cateid` int(11) NOT NULL,
  `page` int(11) UNSIGNED NOT NULL,
  `pagesize` int(11) UNSIGNED NOT NULL,
  `count` int(11) UNSIGNED NOT NULL,
  `md51` varchar(2) NOT NULL,
  `md52` varchar(30) NOT NULL,
  `file` varchar(100) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update` tinyint(4) NOT NULL,
  `delete` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_hooks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 \'，\'分割',
  `status` tinyint(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `title` varchar(30) NOT NULL,
  `link` varchar(200) NOT NULL,
  `login` tinyint(4) NOT NULL,
  `cateid` int(11) NOT NULL,
  `infoid` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_sitemap` (
  `cid` int(11) NOT NULL,
  `sys` varchar(20) NOT NULL,
  `alone` tinyint(4) NOT NULL,
  `join` tinyint(4) NOT NULL,
  `changefreq` varchar(10) NOT NULL,
  `priority` varchar(3) NOT NULL
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_submit` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `sys` varchar(20) NOT NULL,
  `model` varchar(20) NOT NULL,
  `site` varchar(20) NOT NULL,
  `cid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `level_1` int(11) NOT NULL,
  `level_2` int(11) NOT NULL,
  `level_3` int(11) NOT NULL,
  `level_4` int(11) NOT NULL,
  `level_5` int(11) NOT NULL,
  `level_6` int(11) NOT NULL,
  PRIMARY KEY (`sid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_sys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `path` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `developer` varchar(40) NOT NULL,
  `description` varchar(300) NOT NULL,
  `version` varchar(20) NOT NULL,
  `config` text NOT NULL,
  `database` varchar(500) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_topic` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `pic` varchar(120) NOT NULL COMMENT '封面图',
  `title` char(80) NOT NULL COMMENT '标题',
  `cid` int(10) UNSIGNED NOT NULL COMMENT '所属分类',
  `keywords` varchar(140) NOT NULL COMMENT '关键字',
  `description` char(140) NOT NULL COMMENT '描述',
  `formdata` mediumtext NOT NULL,
  `view` int(10) UNSIGNED NOT NULL COMMENT '浏览量',
  `createtime` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `status` tinyint(4) NOT NULL COMMENT '数据状态',
  `toppic` varchar(50) NOT NULL COMMENT '顶部图',
  `pos` tinyint(4) NOT NULL COMMENT '位置',
  `icon` varchar(50) NOT NULL COMMENT '图标',
  `ipic` varchar(50) NOT NULL COMMENT '首页标题图',
  `pctop` varchar(120) NOT NULL COMMENT '电脑版顶部图片',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_topic_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` varchar(15) NOT NULL,
  `length` varchar(8) NOT NULL,
  `form` varchar(15) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_topic_form` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `tid` int(11) NOT NULL,
  `pic` varchar(120) NOT NULL COMMENT '封面图',
  `time` int(11) NOT NULL,
  `title` char(80) NOT NULL COMMENT '标题',
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_vcode` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` varchar(100) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `vcode` varchar(6) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `exp` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '模型类型 1内容模型 2数据模型',
  `menu` tinyint(4) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys` tinyint(4) NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` varchar(15) NOT NULL,
  `length` int(11) NOT NULL,
  `form` varchar(15) NOT NULL,
  `catef` tinyint(4) NOT NULL,
  `contf` tinyint(4) NOT NULL,
  `lorder` int(11) NOT NULL,
  `forder` int(11) NOT NULL,
  `adminf` tinyint(4) NOT NULL,
  `jsonf` tinyint(4) NOT NULL,
  `func` varchar(30) NOT NULL,
  `mid` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_member_binding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app` varchar(15) NOT NULL,
  `userid` int(11) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `avatar` varchar(500) NOT NULL,
  `sex` varchar(1) NOT NULL,
  `addtime` int(11) NOT NULL,
  `updateitme` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_member_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `check` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_member_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` varchar(15) NOT NULL,
  `length` int(11) NOT NULL,
  `form` varchar(15) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `listf` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_member` (
  `userid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `apply_groupid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `login_times` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录次数',
  `reg_ip` varchar(15) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时间',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '会员状态',
  `reason` varchar(50) NOT NULL,
  PRIMARY KEY (`userid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_recom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) NOT NULL,
  `infoid` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `model` varchar(20) NOT NULL,
  `recom` varchar(20) NOT NULL,
  `data` text NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `group` varchar(15) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `option` varchar(255) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `tips` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `must` tinyint(1) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL,
  `input_fun` varchar(30) NOT NULL,
  `show_fun` varchar(30) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_times` (
  `times` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_app_theme` (
  `thid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`thid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_app_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `test_theme_id` int(11) NOT NULL,
  `open_ucenter` tinyint(4) NOT NULL,
  `open_search` tinyint(4) NOT NULL,
  `test_user` varchar(30) NOT NULL,
  `theme_color` varchar(30) NOT NULL,
  `title_color` varchar(10) NOT NULL,
  `site_name` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_app_page` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `thid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `show` varchar(80) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`pid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_cache_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `thid` int(11) NOT NULL,
  `sys` varchar(30) NOT NULL,
  `bid` int(11) NOT NULL,
  `infoid` int(11) NOT NULL,
  `cateid` int(11) NOT NULL,
  `page` int(11) UNSIGNED NOT NULL,
  `pagesize` int(11) UNSIGNED NOT NULL,
  `count` int(11) UNSIGNED NOT NULL,
  `md51` varchar(2) NOT NULL,
  `md52` varchar(30) NOT NULL,
  `file` varchar(100) NOT NULL,
  `updatetime` int(11) NOT NULL,
  `update` tinyint(4) NOT NULL,
  `delete` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_app_block` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `obid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `thid` int(11) NOT NULL,
  `block` varchar(20) NOT NULL,
  `pagename` varchar(30) NOT NULL,
  `data` text NOT NULL,
  `order` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_addons` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `type` varchar(20) NOT NULL,
  `description` text NOT NULL COMMENT '插件描述',
  `hooks` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `config` text NOT NULL COMMENT '配置',
  `developer` varchar(40) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '版本号',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '安装时间',
  `has_adminlist` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否有后台列表',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model` varchar(20) NOT NULL,
  `pid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `cache` tinyint(4) NOT NULL,
  `count` int(11) NOT NULL,
  `catetpl` varchar(50) NOT NULL,
  `conttpl` varchar(50) NOT NULL,
  `order` smallint(6) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_files` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `classid` int(10) UNSIGNED NOT NULL,
  `infoid` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `size` int(10) UNSIGNED NOT NULL,
  `isimg` tinyint(1) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  `updatetime` int(10) UNSIGNED NOT NULL,
  `md5` varchar(32) NOT NULL,
  `sha1` varchar(40) NOT NULL,
  `from` varchar(15) NOT NULL,
  `module` varchar(15) NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_html_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `sys` varchar(30) NOT NULL,
  `contid` int(11) NOT NULL,
  `cateid` int(11) NOT NULL,
  `file` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `update` tinyint(4) NOT NULL,
  `delete` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_admin_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(30) NOT NULL,
  `with` varchar(50) NOT NULL,
  `group` varchar(20) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `xg_db_table_region` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `level` tinyint(4) UNSIGNED NOT NULL,
  `pid` mediumint(8) UNSIGNED NOT NULL,
  `order` smallint(6) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `upid`(`pid`, `order`) USING BTREE
) ENGINE = MyISAM;