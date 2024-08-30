<?php

namespace IPP\Student;

class DPRINT extends Instruction
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
        if(gettype($value) == "boolean")
        {
            if($value)
                fwrite(STDERR, "true");
            else
                fwrite (STDERR, "false");
        }
        elseif($value == null)
        {
            fwrite(STDERR, "Value is null");
        }
        else {
            fwrite(STDERR, (string)$value);
        }
    }
}