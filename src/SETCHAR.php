<?php

namespace IPP\Student;

use Exception;

class SETCHAR extends Instruction
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
        $value1 = $this->getSymbolValue($this->arg1);
        $value2 = $this->getSymbolValue($this->arg2);
        $value3 = $this->getSymbolValue($this->arg3);


        if(gettype($value1) == "string" && gettype($value2) == "integer" && gettype($value3) == "string")
        {
            if($value2 < 0 || $value2 >= strlen($value1))
                throw new Exception("Invalid index", 58);

            if(strlen($value3) != 1) {
                $value3 = substr($value3, 0, 1);
            }
            $frame->update(substr($this->arg1->value,3 ), substr_replace($value1, $value3, $value2, 1));
        }
        else
        {
            throw new Exception("Invalid type", 53);
        }
    }
}