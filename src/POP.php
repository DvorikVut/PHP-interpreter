<?php

namespace IPP\Student;
class POP extends Instruction

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
        $frame = $this->get_Frame($this->arg1);
        $frame->update(substr($this->arg1->value,3 ), $stack->pop());
    }

}