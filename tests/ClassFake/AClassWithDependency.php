<?php

namespace tests\ClassFake;

class AClassWithDependency
{
    public function __construct(public AClass $dependency) // scrivendo public davanti lo rendo anche attributo della classe
    {
        
    }
}