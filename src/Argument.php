<?php

namespace IPP\Student;

class Argument
{
    public string $type;
    public string $value;

    final function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
