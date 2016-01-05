#!	/bin/bash

#########################################################################
#
#	Qihoo project source deploy tool
#	Writen by: bingchen <cb@qihoo.net>
#	http://task.corp.qihoo.net/browse/JYGROUP-184
#
#########################################################################

###########################################################################
#	beta 发布与 release 发布的不同有:
#	1.	目标机器不同
#	2.	从 PROJECT_HOME 而不是从 SVN 中获取源码

ENV_BETA="1";

#
this_file=`pwd`"/"$0
this_dir=`dirname $this_file`
. $this_dir/deploy.minor.release.sh
