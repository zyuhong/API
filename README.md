# Welcome

## Current Version
versionCode > 28  支持计费
protocolCode = 3  预览图修改为小图
kernerlCode = 5   支持短信联系人主题   

## Old version

versionCode <= 28 不支持计费
protocolCode = 2 
kernerlCode = 3  CoolUI6.0普通主题

##锁屏内核版本
初始版本默认为1，新增内核字段从2开始,动态锁屏资源为3，与主题共享锁屏资源为2

目录
lib：基础库
public：公共函数
config：业务配置
task：业务代码
    CoolShow 	业务模块，主要业务代码实现
    Exorder 	支付订单模块
    Records 	统计记录模块
    androidWallpaper 	安卓壁纸模块
    charge 	计费结果同步数据模块
    label 	分类列表
    protocol 	协议类
    statis 	旧的MYSQL统计模块（可以废弃）