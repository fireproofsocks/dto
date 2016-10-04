<?php

namespace DtoTest\UseCases;

use DtoTest\TestCase;

class NestedDataTest extends TestCase
{
    public function testCannotSetPropertiesOnUndefinedLocationsInChild()
    {
        $post = new NestedDataTestBlogPostDto();
     
        // this is the problem: if the template is empty, ambiguity is assumed
        $post->not_defined = true;
        $this->assertFalse(array_key_exists('not_defined',$post->toArray()));
        $post->flags->not_defined = true;
        $this->assertFalse(array_key_exists('not_defined',$post->flags->toArray()));
    }
}

class NestedDataTestBlogPostDto extends \Dto\Dto
{
    protected $template = [
        'flags' => [
            'anything' => 'The array must have something, otherwise it is considered ambiguous, even if ambiguous is set to false',
        ]
    ];
    
    protected $meta = [
        //'title' => ['type' => 'string'],
        'flags' => [
            'type' => 'hash',
            'values' => ['type' => 'boolean'],
            'ambiguous' => false
        ]
    ];
}