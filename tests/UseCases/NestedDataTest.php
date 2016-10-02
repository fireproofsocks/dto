<?php

namespace DtoTest\UseCases;

use DtoTest\TestCase;

class NestedDataTest extends TestCase
{
    public function testCannotSetPropertiesOnUndefinedLocationsInChild()
    {
        $post = new NestedDataTestBlogPostDto();
     
        $post->not_defined = true;
        $this->assertFalse(array_key_exists('not_defined',$post->toArray()));
        $post->flags->not_defined = true;
        $this->assertFalse(array_key_exists('not_defined',$post->flags->toArray()));
    }
}

class NestedDataTestBlogPostDto extends \Dto\Dto
{
    protected $template = [
        //'title' => '',
        'flags' => [
            'published' => true,
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