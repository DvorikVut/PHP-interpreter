<?php

namespace IPP\Student;

class iRETURN extends Instruction
{
    public function __construct(string $opcode, int $order)
    {
        parent::__construct($opcode, $order);
    }
    public function run() : void
    {
        global $orderStack;
        global $currentOrder;
        $currentOrder = $orderStack->pop();
    }
}