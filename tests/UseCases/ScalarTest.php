<?php
class ScalarTest extends DtoTest\TestCase
{
    // TODO: implement this functionality?  Or just punt it to the mutator functions?
    public function test()
    {
        $this->markTestSkipped('defer to mutators');
     
        // Scalar / String:
        // max_length
        // min_length
        // regex_match (if no match, no write)
        // regex_replace (filter incoming values)
        
        // Integer
        // max
        // min
        
        // Float
        // min
        // max
        // precision e.g. [6,2]
        
        // Array
        // min_size
        // max_size
    }
}