#! /usr/local/bin/bash
#	帮助
if [ $# -lt 1 ] || [ "-h" = "$1" ] || [ "--help" = "$1" ]
then
	echo "用法: $0 HOST";
	echo "HOST : 填写要上线的机器"
	exit 0;
fi

#	配置项

#   include lib
this_file=`pwd`"/"$0
this_dir=`dirname $this_file`
. $this_dir/deploy.sh

DEPLOY_DIR="$HOME/deploy"
rm -rf $DEPLOY_DIR && mkdir -p $DEPLOY_DIR

#	release 从 svn 中 export trunk
PROJECT_HOME="$DEPLOY_DIR/$prj_nam"
cecho "=== export chunk from svn ===" $c_notify
svn export $svn $PROJECT_HOME > /dev/null 2>&1
svn_version=`svn --xml info $svn | grep 'revision' | head -1 | awk -F '"' '{ print $2 }'`	#   获取当前 SVN 的版本"

PROJECT_HOME_LEN=`echo "$PROJECT_HOME/" | wc -m | bc`

cecho "\n=== 文件打包 === \n" $c_notify
time=`date "+%Y%m%d%H%M%S"`
src_tgz="$HOME/patch.${svn_version}.${USER}.${time}.tgz"
tar cvfz $src_tgz -C $PROJECT_HOME . > /dev/null 2>&1
echo "$src_tgz"
if [ ! -s "$src_tgz" ]; then
    cecho "错误：文件打包失败" $c_error
    exit 1
fi

hosts=$*
for host in ${hosts}
do
dst_src_tgz=`basename $src_tgz`
cecho "\n=== ${host} 开始上传 ===\n" $c_notify
	$SCP $src_tgz $host:~/$dst_src_tgz > /dev/null 2>&1

    $SSH $host "test -s ~/$dst_src_tgz"
    if [ 0 -ne $? ]; then
        cecho "\t错误：文件上传失败" $c_error
        exit 1
    fi
    cecho "\n=== ${host} 上传成功 ===\n" $c_notify

    $SSH $host "test -s $dst || test -s $install_dst"
    if [ 0 -eq $? ] ; then
        cecho "\t 远程文件已经存在,请使用上线工具上线" $c_error
        exit 1
    fi
    
    cecho "\n\t--- 部署文件 ---\n" $c_notify
	$SSH $host "env LC_CTYPE=zh_CN.GB2312 mkdir -p $install_dst && tar xvfz ~/$dst_src_tgz -C $install_dst" 2>&1

    if [ 0 != $? ]
	then
        cecho "\t错误：部署文件失败" $c_error
    	deploy_confirm "	继续部署？"
        if [ 1 != $? ]; then
	        exit 1;
        fi
	fi
   
    #建立link
    if [ "" = "${dst##*/}" ]
    then
        linkdst=${dst%/*}
    else
        linkdst=$dst
    fi

    $SSH $host "ln -s $install_dst $linkdst"
    $SSH $host "cd $linkdst;/bin/sh project/autoload_builder.sh"

    if [ 0 = $? ] 
    then
        cecho "成功安装,请验证" $c_notify
    fi
done
# clean 
rm -rf $src_tgz

if [ ! -z "$DEPLOY_DIR" ]
then
	rm -rf $DEPLOY_DIR
fi

cecho "\n=== 上线完毕 ===\n" $c_notify
