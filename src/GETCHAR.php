<?php

namespace IPP\Student;

use Exception;

class GETCHAR extends Instruction
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
        $string = $this->getSymbolValue($this->arg2);
        $index = $this->getSymbolValue($this->arg3);

        if(gettype($string) == "string" && gettype($index) == "integer")
        {
            if($index >= 0 && $index < strlen($string))
            {
                $frame->update(substr($this->arg1->value,3 ), $string[$index]);
            }
            else
                throw new Exception("Index out of range", 58);
        }
        else
            throw new Exception("Invalid type", 53);
    }

}