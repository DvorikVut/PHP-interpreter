<?php

namespace IPP\Student;

use IPP\Student\Frame;



class DEFVAR extends Instruction
{
    private Argument $arg1;
    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }

public function run() : void
    {
        $frame = $this->get_Frame($this->arg1);
        $frame->create(substr($this->arg1->value,3 ));
    }




}