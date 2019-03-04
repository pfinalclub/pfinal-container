<?php
/**
 * Created by PhpStorm.
 * User: nancheng
 * Date: 2019/3/4
 * Time: 10:41 PM
 * Email: Lampxiezi@163.com
 * Blog:  http://friday-go.cc/
 *
 *                      _ooOoo_
 *                     o8888888o
 *                     88" . "88
 *                     (| ^_^ |)
 *                     O\  =  /O
 *                  ____/`---'\____
 *                .'  \\|     |//  `.
 *               /  \\|||  :  |||//  \
 *              /  _||||| -:- |||||-  \
 *              |   | \\\  -  /// |   |
 *              | \_|  ''\---/''  |   |
 *              \  .-\__  `-`  ___/-. /
 *            ___`. .'  /--.--\  `. . ___
 *          ."" '<  `.___\_<|>_/___.'  >'"".
 *        | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *        \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  ========`-.____`-.___\_____/___.-`____.-'========
 *                       `=---='
 *  ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *         佛祖保佑       永无BUG     永不修改
 *
 */

namespace pf\container;
class Container
{
    protected static $link = null;
    //更改缓存驱动
    protected function driver()
    {
        self::$link = new Base();
        return $this;
    }
    public function __call($method, $params)
    {
        if (is_null(self::$link)) {
            $this->driver();
        }
        return call_user_func_array([self::$link, $method], $params);
    }
    public static function single()
    {
        static $link;
        if (is_null($link)) {
            $link = new static();
        }
        return $link;
    }
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::single(), $name], $arguments);
    }

}