<?php

namespace IPP\Student;

use Exception;

require_once 'globals.php';
class POPFRAME extends Instruction
{
    public function run() : void
    {
        global $temporaryFrame;
        global $localFrame;
        global $stack;
        $temporaryFrame = $localFrame;
        try{
        $popped = $stack->pop();
        }catch(Exception $e){
            throw new Exception("Stack is empty", 55);
        }
        $localFrame = $popped;
    }
}