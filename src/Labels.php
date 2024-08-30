<?php

namespace IPP\Student;

use Exception;

class Labels
{
    /** @var array<string,int> $dictionary*/
    private array $dictionary = [];

    public function add(string $name, int $order): void
    {
        if (isset($this->dictionary[$name]) && $this->dictionary[$name] !== $order) {
            throw new Exception("The label was already defined!", 52);
        } else {
            $this->dictionary[$name] = $order;
        }
    }

    public function get(string $name): int
    {
        if (isset($this->dictionary[$name])) {
            return $this->dictionary[$name];
        } else {
            throw new Exception("Undefined label!", 52);
        }
    }

    public function exists(string $name): bool
    {
        return isset($this->dictionary[$name]);
    }
}