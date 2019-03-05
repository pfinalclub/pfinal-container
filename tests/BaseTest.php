<?php

/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/3/5
 * Time: 15:01
 *
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
 *           佛祖保佑       永无BUG     永不修改
 *
 */

namespace tests;

use pf\container\Container;

class BaseTest extends \PHPUnit\Framework\TestCase
{
    public function testInstance()
    {
        Container::instance('app', new App());
        $this->assertEquals('success', Container::make('app')->show());
    }

    public function testSingle()
    {
        Container::single('app', function () {
            return new App();
        });
        $this->assertEquals('success', Container::make('app')->show());
    }
}