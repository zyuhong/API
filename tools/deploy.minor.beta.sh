#!	/bin/bash

#########################################################################
#
#	Qihoo project source deploy tool
#	Writen by: bingchen <cb@qihoo.net>
#	http://task.corp.qihoo.net/browse/JYGROUP-184
#
#########################################################################

###########################################################################
#	beta ������ release �����Ĳ�ͬ��:
#	1.	Ŀ�������ͬ
#	2.	�� PROJECT_HOME �����Ǵ� SVN �л�ȡԴ��

ENV_BETA="1";

#
this_file=`pwd`"/"$0
this_dir=`dirname $this_file`
. $this_dir/deploy.minor.release.sh
