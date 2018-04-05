create table wx_user(
id int unsigned primary key auto_increment comment '主键',
nickname varchar(50) default null comment '昵称',
openId varchar(200) not null comment 'openId',
createTime int unsigned default 0 not null comment '创建时间',
city varchar(20) default '' not null comment '所在城市'
)engine=innodb charset=utf8 comment='微信用户表';

create unique index oid on wx_user (openId);