<?php

class MyArrayObject extends ArrayObject {
    static $debugLevel = 2;
    private $storage = [
        'x' => 'y'
    ];
    public function __construct()
    {
        parent::setFlags(parent::ARRAY_AS_PROPS);
    }
    static public function sdprintf() {
        if (static::$debugLevel > 1) {
            call_user_func_array("printf", func_get_args());
        }
    }

    public function offsetGet($name) {
        self::sdprintf("%s(%s)\n", __FUNCTION__, implode(",", func_get_args()));
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }
    public function offsetSet($name, $value) {
        self::sdprintf("%s(%s)\n", __FUNCTION__, implode(",", func_get_args()));
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }
    public function offsetExists($name) {
        self::sdprintf("%s(%s)\n", __FUNCTION__, implode(",", func_get_args()));
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }
    public function offsetUnset($name) {
        self::sdprintf("%s(%s)\n", __FUNCTION__, implode(",", func_get_args()));
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }
}

//$mao = new MyArrayObject();
//$mao["name"] = "bob";
//$mao["friend"] = "jane";
//print_r((array)$mao);

/* Output:

offsetSet(name,bob)
offsetSet(friend,jane)
Array
(
    [name] => bob
    [friend] => jane
)
*/
//$mao = new MyArrayObject();
//$mao->name = "bob";
//$mao->friend = "jane";
//$mao[] = 'whaaat';
//print_r((array)$mao);
/*
offsetSet(name,bob)
offsetSet(friend,jane)
offsetSet(,whaaat)
Array
(
    [name] => bob
    [friend] => jane
    [0] => whaaat
)
*/

$mao = new MyArrayObject();
//$mao->hobbies = [];
//$mao->hobbies[] = 'x';
print_r((array)$mao);