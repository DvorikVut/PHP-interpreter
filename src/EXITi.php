<?php

namespace IPP\Student;

use Exception;
class EXITi extends Instruction
{
    private Argument $arg1;

    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }

    public function run() : void
    {
        $value = $this->getSymbolValue($this->arg1);

        if(gettype($value) != "integer") {
            throw new Exception("Invalid type of argument", 53);
        }
        if($value < 0 || $value > 49) {
            throw new Exception("Invalid value of argument", 57);
        }
        exit($value);
    }
}