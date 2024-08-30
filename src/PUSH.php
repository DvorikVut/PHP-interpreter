<?php

namespace IPP\Student;

class PUSH extends Instruction
{
    private Argument $arg1;

    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }

    public function run() : void
    {
        global $stack;
        $value = $this->getSymbolValue($this->arg1);
        $stack->push($value);
    }
}