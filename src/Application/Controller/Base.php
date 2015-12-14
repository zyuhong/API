<?php

namespace Controller;

use Data\Type as DT;
class Base
{
    /**
     * @var string
     */
    public $action;

    /**
     * 回调函数
     * @var string
     */
    protected $cb = 'cb';

    /**
     * 模块信息
     * @var array
     */
    protected $module = [
        // 模块id 默认自定义1
        'mid' => 1,
        // 子模块来源
        'src' => 0
    ];

    // 模板
    protected $view = null;

    public function __construct()
    {
    }

    public function getControllerName()
    {
        return get_class($this);
    }

    public function getActionName()
    {
        return $this->action;
    }

    public function getView()
    {
        if (is_null($this->view)) {
            $this->initView();
        }

        return $this->view;
    }

    /**
     * 初始化模板
     */
    public function initView()
    {
        require(APP_PATH . 'lib/smarty/Smarty.class.php');
        $view = new \Smarty();
        $view->compile_dir     = RUNTIME_PATH . "/templates_cache/";
        $view->config_dir      = RUNTIME_PATH . "/configs/";
        $view->cache_dir       = RUNTIME_PATH . "/cache/";
        $view->template_dir    = APP_PATH . '/views/';
        $view->left_delimiter  = "{%";
        $view->right_delimiter = "%}";
        $view->force_compile   = $GLOBALS['idc'] == 'corp'; // 是否强制编译
        $view->_dir_perms = 0777;
        $this->viewRegister($view);

        $this->view = $view;
        return $this->view;
    }

    /**
     * 做一些初始化的绑定工作
     * 业务相关
     */
    public function viewRegister(&$view)
    {
        $view->registerClass("D", "\Data\Verify");
    }

    public function assign($key, $value = null, $nocache = false)
    {
        $view = $this->getView();
        $view->assign($key, $value, $nocache);
        return $view;
    }

    /**
     * 渲染模板
     * 如果没有传递template，则会根据类名和action名自动加载
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $view = $this->getView();

        if (is_null($template)) {
            $controller = $this->getControllerName();
            $pre = preg_replace('/^\\*', '', $controller);
            $pre = strtolower(str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $controller));
            $template = $view->template_dir . $pre . "/" . strtolower($this->getActionName()) . ".tpl";
        }

        $view->display($template, $cache_id, $compile_id, $parent);
    }

    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        $view = $this->getView();

        if (is_null($template)) {
            $controller = $this->getControllerName();
            $pre = preg_replace('/^\\*', '', $controller);
            $pre = strtolower(str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $controller));
            $template = $view->template_dir . $pre . "/" . strtolower($this->getActionName()) . ".tpl";
        }

        return $view->fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
    }

    /**
     * @param string $key 下标
     * @param string $type 类型 @default 'string',
     *        支持 'html', 'boolean', 'int', 'float', 'array', 'string', 'poi', 'ip', 'url', 'email', 'phone', 'function'
     * @param array $paras 更多配置参数
     *        支持
     *            'require' => 1, 此参数必须传递，否则抛出错误
     *            'empty' => 1, 允许未空，对于空，0的值，可以返回
     *            'default' => mix, 默认值
     * @param array|Closure $error, 错误提示
     *              'empty' => 'xxx不能为空';
     * @return mixed
     * @throws CParameterException
     */
    public function get($key, $type = 'string', $paras = [], $error = [])
    {
        $value = $this->_getQuery($_GET, $key, $type, $paras, $error);

        return $value;
    }

    /**
     * @param string $key
     * @param string $type
     * @param array $paras
     * @param array|Closure $error
     * @return mixed
     * @throws CParameterException
     */
    public function post($key, $type = 'string', $paras = [], $error = [])
    {
        $value = $this->_getQuery($_POST, $key, $type, $paras, $error);

        return $value;
    }

    /**
     * @param string $key
     * @param string $type
     * @param array $paras
     * @param array|Closure $error
     * @return mixed
     */
    public function req($key, $type = 'string', $paras = [], $error = [])
    {
        $value = $this->_getQuery($_REQUEST, $key, $type, $paras, $error);

        return $value;
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $type
     * @param array $paras
     * @param array $error
     * @return mixed
     * @throws CParameterException
     */
    public function _getQuery($data, $key, $type = 'string', $paras = [], $error = [])
    {
        try {
            return \Data\Verify::verify($data, $key, $type, $paras, $error);
        } catch (Data\Exception $e) {
            throw new CParameterException($e->getMessage());
        }
    }

    /**
     * 错误处理，一般来说，子类都会重写这个函数
     */
    public function error($e)
    {
        (new Error($e))->run();
        exit(0);
    }

    /**
     * 格式化的输出
     */
    public function jsonOutput($result, $cb = '', $encode = true)
    {
        if (empty($cb)) {
            $cb = $this->get($this->cb, 'jsonp');
        }

        if ($encode) {
            $result = json_encode($result);
        }

        // _dbg输出
        if ($this->get('_dbg', \Data\Type::INT)) {
            \Debug::time('end');
            Debug::export($this->get('_dbg', \Data\Type::INT));
            die();
        }

        if ($cb) {
            if ($result === 'NULL') {
                $result = 'null';
            }
            header('Content-Type: application/x-javascript');
            echo ' ' . $cb . '(' . $result . ')';
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo $result;
        }

        exit(0);
    }

    /**
     * 获得模块信息
     * 模块信息里面配置报警模块映射等
     */
    public function getModule()
    {
        return $this->module;
    }
}