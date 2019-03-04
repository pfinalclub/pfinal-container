<?php
/**
 * Created by PhpStorm.
 * User: nancheng
 * Date: 2019/3/4
 * Time: 10:42 PM
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

namespace pf\container\build;

use Closure;
use Exception;
use ReflectionClass;

class Base implements \ArrayAccess
{

    //绑定实例
    public $bindings = [];
    //单例服务
    public $instances = [];

    public function bind($name, $closure, $force = false)
    {
        $this->bindings[$name] = compact('closure', $force);
    }

    public function single($name, $closure)
    {
        $this->bind($name, $closure, true);
    }

    public function instance($name, $object)
    {
        $this->instances[$name] = $object;
    }

    public function make($name, $force = false)
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        //获得实现提供者
        $closure = $this->getClosure($name);
        //获取实例
        $object = $this->build($closure);
        //单例绑定
        if (isset($this->bindings[$name]['force']) && $this->bindings[$name]['force'] || $force) {
            $this->instances[$name] = $object;
        }
        return $object;
    }

    private function getClosure($name)
    {
        return isset($this->bindings[$name]) ? $this->bindings[$name]['closure'] : $name;
    }

    public function callFunction($function)
    {
        $reflectionFunction = new \ReflectionFunction($function);
        $args = $this->getDependencies(
            $reflectionFunction->getParameters()
        );
        return $reflectionFunction->invokeArgs($args);
    }

    public function callMethod($class, $method)
    {
        //反射方法实例
        $reflectionMethod = new \ReflectionMethod($class, $method);
        //解析方法参数
        $args = $this->getDependencies($reflectionMethod->getParameters());
        //生成类并执行方法
        return $reflectionMethod->invokeArgs($this->build($class), $args);
    }

    public function getDependencies($parameters)
    {
        $dependencies = [];
        //参数列表
        foreach ($parameters as $parameter) {
            //获取参数类型
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                //是变量,有默认值则设置默认值
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                //是一个类,递归解析
                $dependencies[] = $this->build($dependency->name);
            }
        }
        return $dependencies;
    }

    public function build($className)
    {
        //匿名函数
        if ($className instanceof Closure) {
            //执行闭包函数
            return $className($this);
        }
        //获取类信息
        $reflector = new ReflectionClass($className);
        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("$className 不能实例化.");
        }
        //获取类的构造函数
        $constructor = $reflector->getConstructor();
        //若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $className;
        }
        //取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();
        //递归解析构造函数的参数
        $dependencies = $this->getDependencies($parameters);
        //创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    public function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        throw new Exception('参数无默认值');
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($key)
    {
        return $this->make($key);
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }
}