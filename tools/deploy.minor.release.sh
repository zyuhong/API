#!	/bin/bash

#########################################################################
#
#	Qihoo project source deploy tool
#
#########################################################################


###########################################################################
#	配置项

#   include lib
this_file=`pwd`"/"$0
this_dir=`dirname $this_file`
. $this_dir/deploy.sh

DEPLOY=0
GITFILE=0
# 指定发布的版本
VERSION="";
# 发布完成后的版本
DEPLOY_VERSION="";
#	读出所有的文件，并过滤黑名单"
files="";

# 发布信息存储
DEPLOY_INFO="/tmp/deploy_`pwd | md5sum | awk '{print $1}'`"

###########################################################################
#	帮助
COMMAND_LINE_OPTIONS_HELP='
Command line options:
    -g          git模式，默认为最后一次提交到仓库的文件, 之后会在/tmp/记录版本信息。
    -G commit   指定版本，diff最新和指定之前的文件。
    -d          deploy switch, deploy会执行make install
    -h          Print this help menu

Examples:
    git模式
        tools/deploy.minor.release.sh -g

    git模式指定版本
        tools/deploy.minor.release.sh -G d4a7b3a

    指定文件模式
        tools/deploy.minor.release.sh index.php server.php ...

    编译模式
        tools/deploy.minor.release.sh -d

'

if [ $# -lt 1 ] || [ "-h" = "$1" ] || [ "--help" = "$1" ]
then
    echo "$COMMAND_LINE_OPTIONS_HELP"
    exit $E_OPTERROR;
fi

while getopts "gdG:" opt
do
    case $opt in
        d ) DEPLOY=1
        cecho "=== DEPLOY mode, make install isn't neccessary ===" $c_notify;;
        g ) GITFILE=1
            VERSION=`cat $DEPLOY_INFO`
            if [ -z "$VERSION" ]
            then
                cecho "=== git mode, get files from last version ===" $c_notify
            else
                cecho "=== git mode, get files from version $VERSION ===" $c_notify
            fi
            ;;
        G ) GITFILE=1
            VERSION=$OPTARG
            cecho "=== git mode, get files from version $VERSION ===" $c_notify;;
        ? ) echo "error"
            exit 1;;
    esac
done

###########################################################################
#	Lets go

#   判别本机的操作系统类型，FreeBSD 与 Linux 有些命令不同
LOCAL_OS=`uname -s`

HOME="/home/qiku"
DEPLOY_DIR="$HOME/deploy_$USER"
rm -rf $DEPLOY_DIR && mkdir -p $DEPLOY_DIR

DEPLOY_VERSION=`git log | head -10 | grep 'commit' | head -1 | awk -F ' ' '{ print $2 }'`	#   获取当前 git 的版本"

if [ -z "$ENV_BETA" ]
then
    #	release 从 VCS 中 export trunk
    PROJECT_HOME="$DEPLOY_DIR/$prj_nam"
    cecho "=== export chunk from git ===" $c_notify
    git clone $git $PROJECT_HOME > /dev/null 2>&1
    cecho "=== git clone done ===" $c_notify

    cd $PROJECT_HOME
    #svn_version=`svn --xml info $svn | grep 'revision' | head -1 | awk -F '"' '{ print $2 }'`	#   获取当前 SVN 的版本"
    if [ -z "$VERSION" ]
    then
        version=$DEPLOY_VERSION
    else
        version=$VERSION
    fi

    cd - > /dev/null 2>&1
else
    #	beta 从 $PROJECT_HOME 中取得代码
    PROJECT_HOME=`pwd`
    version="beta";
fi

#version: target version, has beta
#VERSION: target vesion
#DEPLOY_VERSION: last git version
if [ -z "$VERSION" ]
then
    VERSION=$DEPLOY_VERSION
fi

if [ $GITFILE -ne 0 ]
then
    cecho "=== last git info, please check ===" $c_error
    cecho "`git log --stat=200 -n 1`"

    if [ $VERSION = $DEPLOY_VERSION ]
    then
        files=`git show $VERSION --stat=200 | grep -P '\|\s+\d+\s(\+|-)' | awk '{print $1}'`
    else
        files=`git diff $VERSION --stat=200 | grep -P '\|\s+\d+\s(\+|-)' | awk '{print $1}'`
    fi
fi


# init
if [ $DEPLOY = 1 ]
then
    cecho "=== make install ===" $c_notify
    make install
    cecho "=== make install done ===" $c_notify
fi

PROJECT_HOME_LEN=`echo "$PROJECT_HOME/" | wc -m | bc`

if [ "" = "$files" ]; then
    if [ $DEPLOY = 1 ]
    then
        files="$deploy_dir"
    fi

    while [ $# -ne 0 ]
    do
        if [ "-d" = "$1" ]; then
            shift
            continue
        fi

        if [[ "$LOCAL_OS" == *BSD ]]
        then
            file=`echo "/usr/bin/find -E $PROJECT_HOME/$1 -type f -not -regex '$blacklist' | cut -c '$PROJECT_HOME_LEN-1000' | xargs echo" | sh`
        else
            #     Linux
            file=`echo "/usr/bin/find $PROJECT_HOME/$1 -regextype posix-extended -type f -not -regex '$blacklist' | cut -c '$PROJECT_HOME_LEN-1000' | xargs echo" | sh`
        fi

        files="${files}${file} "

        shift
    done
fi

#
if [ 0 -ne `expr "$files" : ' *'` ]; then
    cecho "\n没有找到要上传的文件，请调整输入参数" $c_error
    exit 1;
fi

#	确认文件
cecho "\n=== 上传文件列表 === \n" $c_notify
no=0;

# 过滤后的文件列表
filterFiles=""
for file in $files
do
    filter=`echo $file | grep -P $blacklist`
    if [ -z $filter ]
    then
        no=`echo "$no + 1" | bc`
        echo "$no $file";
        filterFiles="${filterFiles} ${file}"
    fi
done

if [ -z "$filterFiles" ]
then
    cecho "\n=== 没有要上传的文件，发布中止 === \n" $c_notify
    exit
fi
echo ""
deploy_confirm "确认文件列表？"
if [ 1 != $? ]; then
    exit 1;
fi

#	源文件打包
cecho "\n=== 文件打包 === \n" $c_notify
time=`date "+%Y%m%d%H%M%S"`
src_tgz="$HOME/patch.${version}.${USER}.${time}.tgz"
tar cvfz $src_tgz -C $PROJECT_HOME $filterFiles
echo "$src_tgz"
if [ ! -s "$src_tgz" ]; then
    cecho "错误：文件打包失败" $c_error
    exit 1
fi

if [ -z "$ENV_BETA" ]
then
    hosts="$online_clusters"
else
    hosts="$beta_clusters"
fi

cecho "\n=== 开始部署 ===" $c_notify

#	同步"
host1="";
host1_src="";

for host in ${hosts}
do
    cecho "\n=== ${host} ===\n" $c_notify

    #	获取此主机的对应文件
    online_src="/home/$ssh_user/${USER}.$prj_nam.$host.tgz"
    $SSH $host tar cvhfz $online_src -C $dst $filterFiles > /dev/null 2>&1
    $SCP $host:$online_src $online_src > /dev/null 2>&1
    local_online="$DEPLOY_DIR/online/$host"
    rm -rf $local_online && mkdir -p $local_online
    tar xz -f $online_src -C $local_online

    #	记录基准主机
    if [ "" = "$host1_src" ]; then
        host1="$host"
        host1_src="$local_online"
    fi

    #	对比文件的 VCS 版本与线上版本
    cecho "\t--- 逐个文件比较差异 ---\n" $c_notify

    for file in $filterFiles
    do
        #	确定文件类型，只针对 text 类型
        type=`file $PROJECT_HOME/$file | grep "text"`
        if [ -z "$type" ]; then
            continue
        fi

        cecho "\t$file"
        diffs=`diff -Bb $PROJECT_HOME/$file $local_online/$file`

        #   如果没有不同就不要确认
        if [ -z "$diffs" ]; then
            continue
        fi

        #	如果与基准主机的版本一致，就自动提交
        if [ "$host" != "$host1" ]
        then
            tmp=`diff -Bb $host1_src/$file $local_online/$file`
            if [ -z "$tmp" ]; then
                continue
            fi
        fi

        #   进行 vimdiff
        sleep 1
        vimdiff $PROJECT_HOME/$file ${local_online}/$file

        deploy_confirm "	修改确认 $file ?"
        if [ 1 != $? ]; then
            exit 1;
        fi
    done

    #	上传源文件
    dst_src_tgz=`basename $src_tgz`
    $SCP $src_tgz $host:~/$dst_src_tgz > /dev/null 2>&1

    $SSH $host "test -s ~/$dst_src_tgz"
    if [ 0 -ne $? ]; then
        cecho "\t错误：文件上传失败" $c_error
        exit 1
    fi

    #	备份原始文件
    cecho "\n\t--- 备份原始文件 ---\n" $c_notify
    bak_src_tgz="~$ssh_user/bak.patch.${version}.${USER}.${time}.tgz"
    $SSH $host tar cvhfz ${bak_src_tgz} -C $dst $filterFiles > /dev/null 2>&1
    cecho "\t${bak_src_tgz}"

    $SSH $host "test -s $bak_src_tgz"
    if [ 0 -ne $? ]; then
        cecho "\t错误：远程主机原始文件备份失败" $c_error
        exit 1
    fi

    #	展开源文件
    cecho "\n\t--- 部署文件 ---\n" $c_notify
    $SSH $host "env LC_CTYPE=zh_CN.GB2312 tar xvfz ~/$dst_src_tgz -C $dst" 2>&1 | sed -e 's/^/	/'

    if [ 0 != $? ]
    then
        cecho "\t错误：部署文件失败" $c_error
        deploy_confirm "	继续部署？"
        if [ 1 != $? ]; then
            exit 1;
        fi
    fi

    #	失效EA,重新build autoload map // add by cc

    #   查看生产机器 state 确认服务正常

    #   提示验证部署效果
    verify="	--- 上线完毕，执行此命令恢复原始版本： $SSH $host tar xvfz $bak_src_tgz -C $dst ";

    if [ "$host" = "$host1" ]
    then
        echo ""
        deploy_confirm "$verify 请验证效果"
        if [ 1 != $? ]; then
            exit 1;
        fi
    else
        cecho "\n$verify \n" $c_notify
    fi
done

#	清理垃圾
rm -rf $src_tgz

if [ ! -z "$DEPLOY_DIR" ]
then
    rm -rf $DEPLOY_DIR
fi

cecho "\n=== 上线完毕 ===\n" $c_notify

# mark deploy info
echo $DEPLOY_VERSION > $DEPLOY_INFO
