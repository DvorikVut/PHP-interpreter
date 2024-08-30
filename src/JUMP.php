<?php

namespace IPP\Student;

use Exception;

class JUMP extends Instruction
{
    private Argument $arg1;

    public function __construct(string $opcode, int $order, Argument $arg1)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
    }

    public function run() : void
    {
       global $labels;
       global $currentOrder;

         if($labels->exists($this->arg1->value))
         {
              $currentOrder = $labels->get($this->arg1->value);
         }
         else
         {
              throw new Exception("Label not found", 52);
         }
    }

}