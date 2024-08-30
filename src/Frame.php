<?php

namespace IPP\Student;

use Exception;

class Frame {

    /** @var array<string, mixed> $dictionary */
    private array $dictionary = [];

    /** @var string[] $definedVars */
    private array $definedVars = [];

    public function create(string $name): void {
        if (array_key_exists($name, $this->dictionary) || in_array($name, $this->definedVars)) {
            throw new Exception("Redefinition of a variable!", 52);
        } else {
            $this->definedVars[] = $name;
        }
    }

    public function update(string $name, string|int|bool|null $value): void {
        if (array_key_exists($name, $this->dictionary)) {
            $this->dictionary[$name] = $value;
        } elseif (in_array($name, $this->definedVars)) {
            $this->dictionary[$name] = $value;
            unset($this->definedVars[array_search($name, $this->definedVars)]);
        } else {
            throw new Exception("The variable does not exist!", 54);
        }
    }

    public function get(string $name) : mixed
    {
        if (array_key_exists($name, $this->dictionary)) {
            return $this->dictionary[$name];
        } else {
            throw new Exception("The variable does not exist!", 54);
        }
    }
}
