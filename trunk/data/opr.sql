CREATE TABLE `shuipf_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `g` varchar(20) NOT NULL COMMENT '项目',
  `m` varchar(20) NOT NULL COMMENT '模块',
  `a` varchar(20) NOT NULL COMMENT '方法',
  KEY `groupId` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




alter table `shuipf_access` add column `uid` int(15) not null auto_increment primary key comment '编号' first;

alter table `shuipf_access` add (`mid`  smallint(6) NOT NULL COMMENT ' 菜单编号');
-- alter table `shuipf_access` add (`uid` int(15)  unsigned NOT NULL auto_increment COMMENT '编号');
alter table `shuipf_access` add (`a_m` char(1)  default 'N' COMMENT '增加权限');
alter table `shuipf_access` add (`d_m` char(1)  default 'N' COMMENT '删除权限');
alter table `shuipf_access` add (`e_m` char(1)  default 'N' COMMENT '修改权限');
alter table `shuipf_access` add (`c_m` char(1)  default '1' COMMENT '查看权限，1自己，2所在部门，3所有');
alter table `shuipf_access` add (`h_m` char(1)  default 'N' COMMENT '审核权限');
alter table `shuipf_access` add (`zd_m` char(1)  default 'N' COMMENT '总删除权限');
alter table `shuipf_access` add (`z_m` char(1)  default 'N' COMMENT '支付权限');
alter table `shuipf_access` add (`o_m` char(1)  default 'N' COMMENT '导出权限');


 -- 自定义字段表
CREATE TABLE `shuipf_customsetting` (
  `id` smallint(6) unsigned NOT NULL auto_increment COMMENT '菜单编号',
  `parentid` smallint(6) unsigned NOT NULL default '0' COMMENT '父节点编号',
  `status` tinyint(1) unsigned NOT NULL default '1' COMMENT '状态 1为显示，0不显示',
  `name` varchar(50) NOT NULL default '' COMMENT '菜单名称',
  `sort` smallint(6) unsigned NOT NULL default '0' COMMENT '排序',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='自定义字段表';


 -- 客户表
CREATE TABLE `shuipf_custom` (
  `id` int(15) unsigned NOT NULL auto_increment COMMENT '编号',
  `name` varchar(50) NOT NULL default '' COMMENT '名称',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='客户表';


-- 用户表
CREATE TABLE `shuipf_user` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `nickname` varchar(50) NOT NULL COMMENT '昵称/姓名',
  `password` char(32) NOT NULL COMMENT '密码',
  `bind_account` varchar(50) NOT NULL COMMENT '绑定帐户',
  `last_login_time` int(11) unsigned DEFAULT '0' COMMENT '上次登录时间',
  `last_login_ip` varchar(40) DEFAULT NULL COMMENT '上次登录IP',
  `verify` varchar(32) DEFAULT NULL COMMENT '证验码',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `role_id` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '对应角色ID',
  `info` text NOT NULL COMMENT '信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台用户表';

alter table `shuipf_user` add (`pagesize` tinyint(2)  default '10' COMMENT '每页显示记录数');
alter table `shuipf_user` add (`height` tinyint(2)  default '30' COMMENT '行高');
alter table `shuipf_user` add (`fontname` varchar(128)  default 'GB2312_宋体' COMMENT '字体名称');
alter table `shuipf_user` add (`fontsize` tinyint(2)  default '12' COMMENT '字体大小');

-- 2013年5月18日 21:52:06
CREATE TABLE `zu_dcustomermsg` (
  `customer_id` int(15) unsigned NOT NULL auto_increment COMMENT '编号',
  `customer` varchar(60) default NULL COMMENT '客户名称',
  `province` varchar(60) default NULL COMMENT '省份',
  `city` varchar(60) default NULL COMMENT '城市',
  `area` varchar(60) default NULL COMMENT '区域',
  `address` text COMMENT '地址',
  `road` varchar(100) default NULL COMMENT '靠近路',
  `postno` varchar(10) default NULL COMMENT '邮编',
  `bank_name` text COMMENT '全称',
  `cardno` varchar(100) default NULL COMMENT '卡号',
  `oc_bank` varchar(200) default NULL COMMENT '开户行',
  `features` text COMMENT '特征',
  `note` text COMMENT '备注',
  `hzstate` varchar(20) default NULL,
  `jsstate` varchar(20) default NULL,
  `tzlevel` varchar(20) default NULL,
  `xylevel` varchar(20) default NULL,
  `kll` text,
  `opr_no` varchar(20) default NULL COMMENT '操作人',
  `opr_time` varchar(20) default NULL COMMENT '操作时间',
  `cus_edu` int(15) default '0' COMMENT '信用额度',
  `cus_group` int(15) default '1' COMMENT '分组',
  `cus_type` char(1) default 'N' COMMENT 'N为传统，Y为线上',
  PRIMARY KEY  (`customer_id`),
  KEY `indexcustomer` (`customer`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='客户资料表';

 -- 2013年5月20日 21:51:28
CREATE TABLE `zu_dcusperson` (
  `per_id` int(20) unsigned NOT NULL auto_increment COMMENT '客户联系人编号',
  `customer_id` int(20) NOT NULL default '0',
  `name` varchar(20) default NULL COMMENT '姓名',
  `sex` varchar(4) default NULL COMMENT '性别',
  `dept` varchar(60) default NULL COMMENT '部门',
  `duty` varchar(20) default NULL COMMENT '职位',
  `tell` varchar(30) default NULL COMMENT '电话',
  `fax` varchar(30) default NULL COMMENT '传真',
  `phone` varchar(30) default NULL COMMENT '电话',
  `birthday` varchar(30) default NULL COMMENT '生日',
  `qq` varchar(30) default NULL COMMENT 'QQ',
  `msn` varchar(100) default NULL COMMENT 'msn',
  `cus_group` varchar(60) default NULL COMMENT '组别',
  `cus_edu` int(15) default '0' COMMENT '信用额度',
  `sts` char(1) default 'Y' COMMENT '状态',
  PRIMARY KEY  (`per_id`),
  KEY `indexcustomerid` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='客户人员表';

 -- 2013年5月26日 13:13:00

 ALTER TABLE zu_customer RENAME TO zu_dcustomermsg;

 ALTER  TABLE zu_cusperson RENAME TO zu_dcusperson;

 -- 2013年5月26日 21:35:13

CREATE TABLE `zu_dwtfmsg` (
  `wtf_id` int(15) unsigned NOT NULL auto_increment COMMENT '序号',
  `wtf_name` varchar(60) default NULL COMMENT '委托方名称',
  `province` varchar(60) default NULL COMMENT '省份',
  `city` varchar(60) default NULL COMMENT '城市',
  `area` varchar(60) default NULL COMMENT '区域',
  `address` text COMMENT '地址',
  `road` varchar(100) default NULL COMMENT '范围',
  `postno` varchar(10) default NULL COMMENT '邮编',
  `type_code` varchar(20) default NULL COMMENT '类型',
  `jsstate` varchar(20) default NULL COMMENT '结算方式',
  `level` varchar(100) default NULL COMMENT '等级',
  `fr_no` varchar(100) default NULL COMMENT '企业法人营业执照',
  `jy_no` varchar(100) default NULL COMMENT '经营许可证号',
  `group_no` varchar(100) default NULL COMMENT '组织机构代码证号',
  `sw_no` varchar(100) default NULL COMMENT '税务登记证号',
  `lxsbx` varchar(100) default NULL COMMENT '旅行社责任保险',
  `zc_money` varchar(100) default NULL COMMENT '注册资金',
  `fr_name` varchar(100) default NULL COMMENT '法人代表姓名',
  `cardno` varchar(100) default NULL COMMENT '身份证号码',
  `bank_name` text COMMENT '户名',
  `bank_no` varchar(100) default NULL COMMENT '账号',
  `oc_bank` varchar(200) default NULL COMMENT '开户行',
  `features` text COMMENT '特色',
  `note` text COMMENT '备注',
  `opr_no` varchar(20) default NULL COMMENT '操作人',
  `opr_time` varchar(20) default NULL COMMENT '操作时间',
  `dis_sts` varchar(2) default 'Y' COMMENT '显示状态',
  `bili` varchar(10) default '1' COMMENT '比例',
  `wsort` int(4) default '1' COMMENT '排序',
  PRIMARY KEY  (`wtf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='委托方表';


-- 2013年5月28日 21:58:12
CREATE TABLE `zu_dwtfperson` (
  `per_id` int(20) unsigned NOT NULL auto_increment COMMENT '序号',
  `wtf_id` int(15) default NULL COMMENT '客户资料序号',
  `name` varchar(20) default NULL COMMENT '姓名',
  `sex` varchar(4) default NULL COMMENT '性别',
  `dept` varchar(60) default NULL COMMENT '部门',
  `duty` varchar(20) default NULL COMMENT '职务',
  `tell` varchar(30) default NULL COMMENT '电话',
  `fax` varchar(30) default NULL COMMENT '传真',
  `phone` varchar(30) default NULL COMMENT '手机',
  `birthday` varchar(30) default NULL COMMENT '生日',
  `qq` varchar(30) default NULL COMMENT 'QQ',
  `msn` varchar(100) default NULL COMMENT 'msn',
  `wtf_group` varchar(60) default NULL COMMENT '组别',
  `sts` char(1) default 'Y' COMMENT '状态',
  PRIMARY KEY  (`per_id`),
  KEY `indexwtfid` (`wtf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='委托方人员表';

-- 2013年5月29日 14:01:18
CREATE TABLE `zu_dwtfzhmsg` (
  `id` int(20) unsigned NOT NULL auto_increment COMMENT '序号',
  `wtf_id` int(15) default NULL COMMENT '供应商序号',
  `wname` varchar(255) default NULL COMMENT '户名',
  `wbank_name` varchar(255) default NULL COMMENT '开户行',
  `wbank_zh` varchar(255) default NULL COMMENT '帐号',
  `default_zh` tinyint(1) default NULL COMMENT '默认帐号',
  `sts` char(1) default 'N' COMMENT '状态',
  `opr_name` varchar(20) default NULL COMMENT '提交人',
  `opr_time` varchar(20) default NULL COMMENT '提交时间',
  PRIMARY KEY  (`id`),
  KEY `indexwtfid` (`wtf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='供应商帐号表';


-- 2013年6月12日 21:56:27

CREATE TABLE `zu_dnewlinecfg` (
  `lc_id` int(15) unsigned NOT NULL auto_increment COMMENT '序号',
  `line_code` varchar(50) default NULL COMMENT '线路代码',
  `line_name` varchar(50) default NULL COMMENT '线路名称',
  `jt_desc` varchar(200) default NULL COMMENT '交通描述',
  `adult_fl` varchar(50) default NULL COMMENT '成人返利',
  `child_fl` varchar(50) default NULL COMMENT '小孩返利',
  `op_riqi` varchar(50) default NULL COMMENT '开班日期',
  `op_wtf` varchar(50) default NULL COMMENT '委托方',
  `day_num` int(2) default NULL COMMENT '天数',
  `zhusu` text COMMENT '住宿',
  `yongcan` text COMMENT '用餐',
  `jingdian` text COMMENT '景点',
  `yongche` text COMMENT '用车',
  `daoyou` text COMMENT '导游',
  `wfjt` text COMMENT '往返交通',
  `qita` text COMMENT '其他',
  `buhxm` text COMMENT '不含项目',
  `zifei` text COMMENT '自费项目',
  `childap` text COMMENT '儿童安排',
  `shopap` text COMMENT '购物安排',
  `freexm` text COMMENT '赠送项目',
  `notexm` text COMMENT '注意事项',
  `notewx` text COMMENT '温馨提醒',
  `sort` int(2) default NULL COMMENT '排序',
  `jt_time` varchar(50) default NULL COMMENT '接团时间',
  `jt_place` varchar(50) default NULL COMMENT '接团地点',
  `jt_hci` varchar(50) default NULL COMMENT '所接航次',
  `st_time` varchar(50) default NULL COMMENT '送团时间',
  `st_place` varchar(50) default NULL COMMENT '送团地点',
  `st_hci` varchar(50) default NULL COMMENT '所送航次',
  `shlx` varchar(50) default NULL COMMENT '上海联系',
  `rxdh` varchar(50) default NULL COMMENT '热线电话',
  `twfax` varchar(50) default NULL COMMENT '图文传真',
  `cysj` varchar(50) default NULL COMMENT '常用手机',
  `qq` varchar(50) default NULL COMMENT 'QQ',
  `begin_date` varchar(20) default NULL COMMENT '开始日期',
  `end_date` varchar(20) default NULL COMMENT '结束日期',
  `wtf_id` int(15) default NULL COMMENT '委托方编号',
  `dept` varchar(20) default NULL COMMENT '委托方部门',
  `wtf_group` varchar(20) default NULL COMMENT '委托方组别',
  `wtf_name` varchar(20) default NULL COMMENT '委托方联系人',
  `wtf_tell` varchar(20) default NULL COMMENT '委托方电话',
  `wtf_phone` varchar(20) default NULL COMMENT '委托方手机',
  `wfc_name` varchar(10) default NULL COMMENT '往返程',
  `xm_name` varchar(20) default NULL COMMENT '项目',
  `hci` varchar(50) default NULL COMMENT '航次',
  `lb_name` varchar(50) default NULL COMMENT '类别',
  `pt_dw` varchar(10) default NULL COMMENT '到站',
  `wfc_name2` varchar(10) default NULL COMMENT '往返程2',
  `xm_name2` varchar(20) default NULL COMMENT '项目2',
  `hci2` varchar(50) default NULL COMMENT '航次2',
  `lb_name2` varchar(50) default NULL COMMENT '类别2',
  `pt_dw2` varchar(10) default NULL COMMENT '到站2',
  `wtf_fax` varchar(20) default NULL COMMENT '委托方传真',
  `wf_wtf` varchar(50) default NULL COMMENT '委托方',
  `wf_wtf2` varchar(50) default NULL COMMENT '委托方2',
  `fcday` int(2) default NULL COMMENT '返程票时间',
  `wf_price` varchar(10) default NULL COMMENT '单价1',
  `wf_price2` varchar(10) default NULL COMMENT '单价2',
  `jtday` int(2) default NULL COMMENT '接团时间',
  `od_start` varchar(50) default NULL COMMENT '始发1',
  `od_start2` varchar(50) default NULL COMMENT '始发2',
  `line_type` char(1) default 's' COMMENT 's为散客，t为团队',
  `hcjt_desc` varchar(200) default NULL COMMENT '回程交通描述',
  `op_no` varchar(15) default NULL COMMENT '操作人',
  `op_time` varchar(20) default NULL COMMENT '操作时间',
  `line_note` varchar(100) default NULL COMMENT '团队备注',
  `zongji` varchar(10) default NULL COMMENT '总计',
  `renshu` varchar(10) default NULL COMMENT '人数',
  `renjun` varchar(10) default NULL COMMENT '人均',
  `beizhu` text COMMENT '备注',
  `chk_sts` char(1) default 'N' COMMENT '审核',
  `renjunbz` varchar(200) default NULL COMMENT '人均备注',
  `tqp` varchar(10) default NULL COMMENT '全陪',
  PRIMARY KEY  (`lc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='新线路库表';