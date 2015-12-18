<?php

define('ROOT_PATH', dirname(__DIR__));

$idc = get_cfg_var('application_idc');

$configFile = __DIR__ . '/' . $idc . '.config.php';
$confIdc = '';
if (file_exists($configFile)) {
    $CONFIG = include($configFile);
    $confIdc = $idc;
} else {
    $CONFIG = include(__DIR__ . '/release.config.php');
    $confIdc = 'all';
}

if ($CONFIG) {
    foreach ($CONFIG as $key => $cfg) {
        AppConf::$specCfgs = array_merge(AppConf::$specCfgs, [
                $key => [
                    $confIdc => $cfg
                ]
            ]
        );
    }
}

# 本地配置覆盖
$localConfFile = __DIR__ . '/local.config.php';
if (file_exists($localConfFile)) {
    $localConfig = include($localConfFile);
    AppConf::$specCfgs = array_merge(AppConf::$specCfgs, $localConfig);
}
# 本地配置end

# loader
$loader = include ROOT_PATH . '/vendor/autoload.php';
$loader->addPsr4('Controller\\', ROOT_PATH . '/src/Application/Controller');
$loader->addPsr4('Model\\', ROOT_PATH . '/src/Application/Model');
$loader->addPsr4('Lib\\', ROOT_PATH . '/src/Lib');

class AppConf
{
    // 兜底的配置，推荐所有配置都有
    // 如果value是数组，则分机房配置
    // 例如：
    // 'mapso/tile/traffic' => arary('corp' => '10.138.240.240:10000', 'bjdt' => 'xxx')
    static $defaultCfgs = [
    ];

    // 强制指定的配置
    // 该项在配置开发环境等比较适用
    // value为非数组，则全环境指定
    // value为数组，必须以idc为index，全机房为 ['all' => value]
    // 例如:
    // 'mapso/map/version' => 11
    // 'mapso/map/version' => ['corp' => 12, 'bjdt' => 13]
    // 'mapso/map/version' => ['all' => [1,2]]
    // 不支持 'mapso/map/version' => [1,2]
    static $specCfgs = [
    ];

    /**
     * 获得qconf配置项
     * @params {String} $key qconf配置的key
     * @params {String} $specIdc 强制指定的idc，默认为空 @default ''
     * @params {Mix} $default 如值为空，则返回default值 @default null
     */
    public static function getCfg($key, $specIdc = '', $default = null)
    {
        global $idc;

        $defaultCfgs = self::$defaultCfgs;

        if (empty($specIdc)) {
            $specIdc = $idc;
        }

        $spec = self::getSpec($key, $specIdc);
        if (!is_null($spec)) {
            return $spec;
        }

        $value = null;
        $value = Qconf::getConf($key, $specIdc);

        if (is_null($value)) {
            $value = Qconf::getConf($key, 'bjdt');
        }

        if (is_null($value)) {
            if (!is_null($default)) {
                $value = $default;
            } elseif (isset($defaultCfgs[$key])) {
                $v = $defaultCfgs[$key];

                if (is_array($v) && isset($v[$specIdc])) {
                    $value = $v[$specIdc];
                } else {
                    $value = $v;
                }
            }
        }

        return $value;
    }

    /**
     * 获得服务配置
     * 服务和配置的概念不同，配置一般是值，服务是一组资源，一般为ip/vip
     * @params {String} $key qconf配置的key
     * @params {String} $specIdc 强制指定的idc，默认为空 @default ''
     * @params {Mix} $default 如值为空，则返回default值 @default null
     */
    public static function getHosts($key, $specIdc = '', $default = null)
    {
        global $idc;

        $defaultCfgs = self::$defaultCfgs;
        if (empty($specIdc)) {
            $specIdc = $idc;
        }

        $spec = self::getSpec($key, $specIdc);
        if (!is_null($spec)) {
            return $spec;
        }

        $hosts = [];
        $hosts = Qconf::getAllHost($key, $specIdc);

        if (is_null($hosts)) {
            $hosts = Qconf::getAllHost($key, 'bjdt');
        }

        if (is_null($hosts)) {
            if (!is_null($default)) {
                $hosts = $default;
            } elseif (isset($defaultCfgs[$key])) {
                $v = $defaultCfgs[$key];

                if (is_array($v) && isset($v[$specIdc])) {
                    $hosts = $v[$specIdc];
                } else {
                    $hosts = $v;
                }
            }
        }

        return $hosts;
    }

    /**
     * 获得指定配置
     * 如果没有指定配置，则返回null
     */
    public static function getSpec($key, $specIdc = '')
    {
        global $idc;

        $specCfgs = self::$specCfgs;
        if (empty($specIdc)) {
            $specIdc = $idc;
        }

        if (isset($specCfgs[$key])) {
            if (is_array($specCfgs[$key])) {
                if (!isset($specCfgs[$key][$specIdc])) {
                    $specIdc = 'all';
                }

                return isset($specCfgs[$key][$specIdc]) ? $specCfgs[$key][$specIdc] : null;
            } else {
                return $specCfgs[$key];
            }
        }

        return null;
    }
}
