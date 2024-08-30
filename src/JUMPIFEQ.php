<?php

namespace IPP\Student;

use Exception;

class JUMPIFEQ extends Instruction
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
        global $labels;
        global $currentOrder;

        $value1 = $this->getSymbolValue($this->arg2);
        $value2 = $this->getSymbolValue($this->arg3);

        if(gettype($value1) != gettype($value2))
        {
        throw new Exception("Invalid type", 53);
        }

        elseif($value1 === null || $value2 === null)
        {
            throw new Exception("Both values must be defined", 56);
        }

        if($value1 == $value2) {
            if($labels->exists($this->arg1->value))
            {
                $currentOrder = $labels->get($this->arg1->value);
            } else {
                throw new Exception("Label not found", 52);
            }
        }
    }
}