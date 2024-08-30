<?php

namespace IPP\Student;

use Exception;

class WRITE extends Instruction
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
        if(gettype($value) == "integer" || gettype($value) == "string")
        {
            echo $value;
        }
        elseif(gettype($value) == "NULL")
        {
            echo "";
        }
        elseif(gettype($value) == "boolean")
        {
            if($value == "true")
            {
                echo "true";
            }
            else
            {
                echo "false";
            }
        }
        else {
            throw new Exception("Invalid type", 53);
        }
    }
}