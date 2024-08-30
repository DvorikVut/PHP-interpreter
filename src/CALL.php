<?php

namespace IPP\Student;

class CALL extends Instruction
{
    private Argument $arg1;

    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }
    public function run() : void
    {
       global $orderStack;
       global $currentOrder;
       global $labels;

       $orderStack->push($currentOrder);
       $currentOrder = $labels->get($this->arg1->value);
    }
}