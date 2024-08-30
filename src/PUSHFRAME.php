<?php

namespace IPP\Student;

use Exception;

class PUSHFRAME extends Instruction
{
    public function run() : void
    {
        global $temporaryFrame;
        global $localFrame;
        global $stack;
        if($temporaryFrame === null)
        {
            throw new Exception("Temporary frame is not initialized", 55);
        }
        $stack->push($temporaryFrame);
        $localFrame = $temporaryFrame;
        $temporaryFrame = null;
    }
}