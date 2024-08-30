<?php

namespace IPP\Student;

use Exception;

class ADD extends Instruction
{
    private Argument $arg1;
    private Argument $arg2;
    private Argument $arg3;

    public function __construct(string $opcode, int $order, Argument $arg1, Argument $arg2, Argument $arg3)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }

    public function run() : void
    {
        $frame = $this->get_Frame($this->arg1);
        $value1 = $this->getSymbolValue($this->arg2);
        $value2 = $this->getSymbolValue($this->arg3);

        if(gettype($value1) != "integer" || gettype($value2) != "integer")
        {
            throw new Exception("Invalid type", 53);
        }
        $frame->update(substr($this->arg1->value,3 ),(int)$value1 + (int)$value2);
    }
}