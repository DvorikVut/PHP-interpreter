<?php

namespace IPP\Student;

class BREAKi extends Instruction
{
    public function __construct(string $opcode, int $order)
    {
        parent::__construct($opcode, $order);
    }

    public function run(): void
    {
        global $labels;
        global $currentOrder;
        global $globalFrame;
        global $temporaryFrame;
        fwrite(STDERR, "Current order: $currentOrder\n");
        fwrite(STDERR, "Temporary frame: " . print_r($temporaryFrame, true) . "\n");
        fwrite(STDERR, "Global frame: " . print_r($globalFrame, true) . "\n");
    }

}