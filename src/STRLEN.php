<?php

namespace IPP\Student;

use Exception;

class STRLEN extends Instruction
{
    private Argument $arg1;
    private Argument $arg2;

    public function __construct(string $opcode, int $order, Argument $arg1, Argument $arg2)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function run() : void
    {
        $frame = $this->get_Frame($this->arg1);
        $value = $this->getSymbolValue($this->arg2);
        if(gettype($value) == "string")
            $frame->update(substr($this->arg1->value,3 ), strlen($value));
        else{
            throw new Exception("Invalid type", 53);
        }
    }
}