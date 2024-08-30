<?php

namespace IPP\Student;

use Exception;

class Stack
{
    /** @var array<mixed> $list */
    private array $list = [];

    public function push(mixed $value): void
    {
        $this->list[] = $value;
    }

    public function pop() : mixed
    {
        if (empty($this->list)) {
            throw new Exception("The stack is empty!", 56);
        } else {
            return array_pop($this->list);
        }
    }

}