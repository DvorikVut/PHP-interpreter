<?php

namespace IPP\Student;

class TYPE extends Instruction
{
    private Argument $arg1;
    private Argument $arg2;

    public function __construct(string $opcode, int $order, Argument $arg1, Argument $arg2)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function run(): void
    {
        $frame = $this->get_Frame($this->arg1);
        try {
            $value = $this->getSymbolValue($this->arg2);
            if ($value === null) {
                $frame->update(substr($this->arg1->value, 3), "nil");
            } elseif (gettype($value) == "string") {
                $frame->update(substr($this->arg1->value, 3), "string");
            } elseif (gettype($value) == "integer") {
                $frame->update(substr($this->arg1->value, 3), "int");
            } elseif (gettype($value) == "boolean") {
                $frame->update(substr($this->arg1->value, 3), "bool");
            }
        } catch (\Exception $e) {
            $frame->update(substr($this->arg1->value, 3), "");

        }
    }

}