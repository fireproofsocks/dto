<?php
class Z  {

    protected $data = [
        'firstname' => 'Bob',
        'mother' => [
            'firstname' => 'Debbie'
        ]

    ];

    protected $storage;

    public function __construct($array = [], $flags = 2)
    {

        $class = get_class($this);
        //print $class; exit;
        //parent::setFlags(parent::ARRAY_AS_PROPS);
        //$this->setFlags(ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
        //$this->setFlags(ArrayObject::STD_PROP_LIST);
        //Fatal error: Cannot re-assign $this
        //$this = $this->data;
        //foreach ($this->data as $key => $value) {
        $array = ($array) ? $array : $this->data;
        $this->storage = new ArrayObject();

//        foreach ($array as $key => $value) {
//            $this->offsetSet($key, is_array($value) ? new $class($value) : $value);
//        }
//
//        $this->setFlags($flags);

//        foreach ($this->data as $k => $v) {
//            //$this->__set($k, $v);
//            if (is_array($v)) {
//                // Fatal error: Allowed memory size of 134217728 bytes exhausted
//                //$this[$k] = new TestArrayObject($v);
//            }
//            else {
//                $this[$k] = $v;
//            }
//
//            //$this[$k] = new TestArrayObject($v);
//        }

    }


    // Only get used if ARRAY_AS_PROPS is NOT set
    // This does get called if STD_PROP_LIST is set
    // but then the value doesn't stick
    public function __set($name, $val) {
        printf("%s(%s)\n", __FUNCTION__, implode(", ", func_get_args()));
        $this[$name] = $val;
    }

    public function __get($name) {
        printf("%s(%s)\n", __FUNCTION__, implode(", ", func_get_args()));
        return $this[$name];
    }
}

$obj = new Z(['firstname' => 'bob', 'mother'=>['firstname'=>'Deb']]);
//$obj = new Z();

print_r((array) $obj);