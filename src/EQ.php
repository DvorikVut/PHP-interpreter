<?php

namespace IPP\Student;

use Exception;

class EQ extends Instruction
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

        if ($value1 === null && $value2 === null) {
            $frame->update(substr($this->arg1->value, 3), true);
        }

        if (gettype($value1) == gettype($value2) || gettype($value1) == "NULL" || gettype($value2) == "NULL") {
            if (gettype($value1) == "string" && gettype($value2) == "string" || (gettype($value1) == "NULL" && gettype($value2) == "string") || (gettype($value1) == "string" && gettype($value2) == "NULL")){
                $frame->update(substr($this->arg1->value, 3), (string)$value1 == (string)$value2);
            } else {
                $frame->update(substr($this->arg1->value, 3), (int)$value1 == (int)$value2);
            }
        } else {
            throw new Exception("Both values must be of the same type", 53);
        }
    }
}