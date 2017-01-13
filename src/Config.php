<?php

namespace Dto;

class Config implements ConfigInterface
{
    protected $config = [];

    protected $default = [
        'pre_validate' => true,
        'pre_validate_uniqueItems' => true,
        'exceptions_on_hydrate' => false,
        'exceptions_on_set' => false,
        'maxItems_behavior' => 'ignore', // shift
        'maxProperties_behavior' => 'ignore', // shift
        'toString_behavior' => 'json'
    ];

    protected $maxItems_behavior_allowed_values = ['shift', 'ignore', 'exception'];

    protected $maxProperties_behavior_allowed_values = ['ignore', 'exception'];

    protected $toString_behavior_allowed_values = ['json', 'exception', 'type'];

    function __construct(array $overrides = [])
    {
        // TODO: validate values
        $this->config = array_merge($overrides, $this->default);
    }

    function get($key)
    {
        return $this->config[$key];
    }
}