#!	/bin/bash

#########################################################################
#
#	Qihoo project source deploy tool
#	Writen by: bingchen <cb@qihoo.net>
#	http://task.corp.qihoo.net/browse/JYGROUP-184
#
#########################################################################


###########################################################################
#	配置项

#	项目名称
#project="TWIDDER"
PROJECT_HOME="/home/s/www/$USER/t/zt-api/service"

# 线上集群列表
# first machine is the regression one.
online_clusters="101.198.158.87 101.198.158.80 101.198.152.218 101.198.152.213 101.198.152.214"

# 测试机器
beta_clusters="101.198.158.88";

#	目标机器的目录
dst="/home/s/www/zt-api/service";

#   目标安装路径
install_dst="/home/s/www/zt-api/service"

#	SVN 地址
git="git@10.100.14.251:os-zhuti/api.git"

#	同步所使用的用户
ssh_user="qiku"

#	文件黑名单
blacklist='(.*\.tmp$)|(.*\.log$)|(.*\.svn.*)|\.git$|(tools\/.*)|(tests/.*|local.config.php|.env$|composer.json|readme.md|Makefile|artisan|phpunit.xml)'

# 编译发布目录
deploy_dir='vendor'


###########################################################################
#	公共库

#	print colored text
#	$1 = message
#	$2 = color

#	格式化输出
export black='\e[0m'
export boldblack='\e[1;0m'
export red='\e[31m'
export boldred='\e[1;31m'
export green='\e[32m'
export boldgreen='\e[1;32m'
export yellow='\e[33m'
export boldyellow='\e[1;33m'
export blue='\e[34m'
export boldblue='\e[1;34m'
export magenta='\e[35m'
export boldmagenta='\e[1;35m'
export cyan='\e[36m'
export boldcyan='\e[1;36m'
export white='\e[37m'
export boldwhite='\e[1;37m'

cecho()
{
    message=$1
    color=${2:-$black}

    /bin/echo -e "$color"
    /bin/echo -e "$message"
    tput sgr0			# Reset to normal.
    #/bin/echo -e "$black"
    return
}

cread()
{
    color=${4:-$black}

    /bin/echo -e "$color"
    read $1 "$2" $3
    tput sgr0			# Reset to normal.
    /bin/echo -e "$black"
    return
}

#	确认用户的输入
deploy_confirm()
{
    while [ 1 = 1 ]
    do
        cread -p "$1 [y/n]: " CONTINUE $c_notify
        if [ "y" = "$CONTINUE" ]; then
          return 1;
        fi

        if [ "n" = "$CONTINUE" ]; then
          return 0;
        fi
    done

    return 0;
}


###########################################################################
#	Start

export LC_ALL="zh_CN.UTF-8"
#export LC_ALL="zh_CN.GB2312"

#PROJECT_HOME=`echo "echo \$""${project}_HOME" | sh`

#	确定根目录
if [ -z "$PROJECT_HOME" ]; then
    echo "先置当前用户工作根目录的环境变量：$PROJECT_HOME"
    exit
fi

prj_nam=`basename $dst`

#
SSH="sudo -u $ssh_user ssh -c blowfish"
SCP="sudo -u $ssh_user scp -c blowfish"

#	提示颜色
c_notify=$boldcyan
c_error=$boldred
