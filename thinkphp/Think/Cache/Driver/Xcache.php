<?php
// +----------------------------------------------------------------------
// | ThinkCache
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;
/**
 * Xcache缓存驱动
 * @author    liu21st <liu21st@gmail.com>
 */
class Xcache {

    protected $options  =   array(
        'prefix'        =>  '',
        'expire'        =>  0,
        'length'        =>  0,
    );

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options=array()) {
        if ( !function_exists('xcache_info') ) {
            throw new Exception('_NOT_SUPPERT_:Xcache');
        }
        if(!empty($options)) {
            $this->options      =   array_merge($this->options,$options);
        }
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        $name   =   $this->options['prefix'].$name;
        if (xcache_isset($name)) {
            return xcache_get($name);
        }
        return false;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolen
     */
    public function set($name, $value,$expire=null) {
        if(is_null($expire)) {
            $expire = $this->options['expire'] ;
        }
        $name   =   $this->options['prefix'].$name;
        if(xcache_set($name, $value, $expire)) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $queue  =   xcache_get('__info__');
                if(!$queue) {
                    $queue  =   array();
                }
                if(false===array_search($name, $queue))  array_push($queue,$name);
                if(count($queue) > $this->options['length']) {
                    // 出列
                    $key =  array_shift($queue);
                    // 删除缓存
                    xcache_unset($key);
                }
                xcache_set('__info__', $queue);
            }
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function rm($name) {
        return xcache_unset($this->options['prefix'].$name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolen
     */
    public function clear() {
        return ;
    }
}