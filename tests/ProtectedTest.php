<?php
class ProtectedTest extends PHPUnit_Framework_Testcase
{
    
    

    
//    public function testGetMetaSubsetTrimsTheRootNodeDefinition()
//    {
//        $dto = new \Dto\Dto();
//        $reflection = new ReflectionClass(get_class($dto));
//        $method = $reflection->getMethod('getMetaSubset');
//        $method->setAccessible(true);
//
//        // Assume a normalized meta key format w leading dots
//        $meta = [
//            '.' => [
//                'type' => 'array',
//                'values' => [
//                    'type' => 'array'
//                ]
//            ]
//        ];
//
//        $trimmed = [
//            '.' => [
//                'type' => 'array',
//            ],
//        ];
//    print '--->'; print_r($method->invokeArgs($dto, [null, $meta])); exit;
//        $this->assertEquals($trimmed, $method->invokeArgs($dto, [null, $meta]));
//    }


    

    public function testFilter()
    {
        $dto = new TestFilterDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('filter');
        $method->setAccessible(true);

        // $dto->integer = '123a0'; // should come out as 123
        $value = $method->invokeArgs($dto, ['123a4', 'x']);
        $this->assertEquals(123, $value);

        $value = $method->invokeArgs($dto, ['456b7', 'y']);
        $this->assertEquals(456, $value);
    }

    
    public function testGetTypeMutatorFunctionName()
    {
        $dto = new TestFilterDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getTypeMutatorFunctionName');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['z']);
        
        $this->assertEquals('setTypeArray', $value);
    }
}

class TestFilterDto extends \Dto\Dto {
    protected $template = [
        'x' => null
    ];

    protected $meta = [
        'x' => [
            'type' => 'integer',
        ],
        'y' => [
            'type' => 'integer'
        ],
        'z' => [
            'type' => 'array',
            'values' => [
                'type' => 'integer'
            ]
        ]
    ];

    public static function toInt($value) {
        return intval($value);
    }

    public function setY($value) {
        return intval($value);
    }
}