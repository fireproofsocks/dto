<?php

namespace Dto;

interface ServiceContainerInterface
{
    public function make($service);

    public function bind($service, \Closure $closure);
}