<?php

namespace IPP\Student;

class LABEL extends Instruction
{
    private Argument $arg1;

    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }

    public function run() : void
    {
        global $labels;
        $labels->add($this->arg1->value, $this->order);
    }

}