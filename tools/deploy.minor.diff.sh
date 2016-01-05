#!	/usr/local/bin/bash


#########################################################################
#
#	Qihoo project source deploy tool: 线上目录比较工具
#	Writen by: bingchen <cb@qihoo.net>
#	http://task.corp.qihoo.net/browse/JYGROUP-184
#
#########################################################################


#	帮助
if [ $# -lt 1 ] || [ "-h" = "$1" ] || [ "--help" = "$1" ]
then
	echo "用法: $0 ONLINE_HOST";
	echo "ONLINE_HOST : 对比当前 SVN 中的版本与 ONLINE_HOST 的不同"
	exit 0;
fi


###########################################################################
#	配置项

#   include lib
this_file=`pwd`"/"$0
this_dir=`dirname $this_file`
. $this_dir/deploy.sh


###########################################################################
#	Start

#	从 svn 中 export trunk
cecho "=== export chunk from svn ===" $c_notify
DEPLOY_DIR="$HOME/deploy"
PROJECT_HOME="$DEPLOY_DIR/$prj_nam"
rm -rf $DEPLOY_DIR && mkdir -p $DEPLOY_DIR
svn export $svn $PROJECT_HOME > /dev/null 2>&1

#   将线上版本拖下来 
host="$1"
online_src="/home/$ssh_user/${USER}.$prj_nam.tgz"
$SSH $host tar cvLfz $online_src $dst > /dev/null 2>&1
$SCP $host:$online_src $online_src > /dev/null 2>&1
local_online="$DEPLOY_DIR/online"
rm -rf $local_online && mkdir -p $local_online
tar xz -f $online_src -C $local_online

prefix=`echo "$PROJECT_HOME" | awk '{ gsub("/","\\\/"); print $0"\\\/"; }'`
dir_diff=`diff -rbB --brief $PROJECT_HOME ${local_online}${dst} | grep -v "Only in ${local_online}${dst}" | awk '{ if("Files"==$1) { print "!\t"$2; }; if("Only"==$1) { print "+\t"substr($3,1,length($3)-1)"/"$4 } }' | sed "s/$prefix//"`

#	比较用户输入的文件
while [ 1 = 1 ]
do
    echo -e "$dir_diff"
	cread -p "输入比较文件的路径（路径参考以上输出），n退出；左帧SVN: " file $c_notify

	if [ "n" = "$file" ]; then
		break;
	fi

	if [ "" = "$file" ]; then
		continue;
	fi

	local_file="$PROJECT_HOME/$file"
	online_file="${local_online}${dst}/$file"

	if [ ! -s "$local_file" ]; then
		cecho "没有找到文件，确认文件路径是否正确：$file" $c_error
		continue;
	fi

    vimdiff $local_file $online_file
done

#	清理垃圾
rm -rf $DEPLOY_DIR
