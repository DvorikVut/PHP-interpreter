<?php

namespace IPP\Student;
require_once 'globals.php';

class CREATEFRAME extends Instruction
{
    public function run() : void
    {
        global $temporaryFrame;
        $temporaryFrame = new Frame();
    }
}