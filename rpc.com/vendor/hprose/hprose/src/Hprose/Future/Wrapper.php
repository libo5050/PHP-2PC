<?php
/**********************************************************\
|                                                          |
|                          hprose                          |
|                                                          |
| Official WebSite: http://www.hprose.com/                 |
|                   http://www.hprose.org/                 |
|                                                          |
\**********************************************************/

/**********************************************************\
 *                                                        *
 * Hprose/Future/Wrapper.php                              *
 *                                                        *
 * Future Wrapper for php 5.3+                            *
 *                                                        *
 * LastModified: Jul 25, 2016                             *
 * Author: Ma Bingyao <andot@hprose.com>                  *
 *                                                        *
\**********************************************************/

namespace Hprose\Future;

use ReflectionMethod;

class Wrapper {
    protected $obj;
    public function __construct($obj) {
        $this->obj = $obj;
    }
    public function __call($name, array $arguments) {
        $method = array($this->obj, $name);
        return all($arguments)->then(function($args) use ($method, $name) {
            if (class_exists("\\Generator")) {
                $m = new ReflectionMethod($this->obj, $name);
                if ($m->isGenerator()) {
                    array_splice($args, 0, 0, array($method));
                    return call_user_func_array('\\Hprose\\Future\\co', $args);
                }
            }
            return call_user_func_array($method, $args);
        });
    }
    public function __get($name) {
        return $this->obj->$name;
    }
    public function __set($name, $value) {
        $this->obj->$name = $value;
    }
    public function __isset($name) {
        return isset($this->obj->$name);
    }
    public function __unset($name) {
        unset($this->obj->$name);
    }
}
