<?php

namespace IPP\Student;

use Exception;
use IPP\Student\Argument;
require_once 'globals.php';

class Instruction
{
    private string $opcode;
    protected int $order;

    public function  __construct(string $opcode, int $order)
    {
        $this->opcode = $opcode;
        $this->order = $order;
    }
    public function run(): void
    {
    }

    public function __toString(): string
    {
        return "Opcode: $this->opcode, Order: $this->order";
    }

    public function get_Frame(Argument $arg1): Frame
    {
        global $globalFrame;
        global $temporaryFrame;
        global $localFrame;
        $frame = null;
        if ($arg1->value[0] == "G") {
            $frame = $globalFrame;
        } elseif ($arg1->value[0] == "T") {
            $frame = $temporaryFrame;
        } elseif ($arg1->value[0] == "L") {
            $frame = $localFrame;
        } else {
            throw new Exception("Invalid frame prefix.", 32);
        }
        return $frame;
    }

    function getSymbolValue(Argument $symbol): string|int|bool|null
    {
        if ($symbol->type === "var") {
            switch ($symbol->value[0]) {
                case "L":
                    $value = $GLOBALS['localFrame']->get(substr($symbol->value, 3));
                    break;
                case "T":
                    $value = $GLOBALS['temporaryFrame']->get(substr($symbol->value, 3));
                    break;
                case "G":
                    $value = $GLOBALS['globalFrame']->get(substr($symbol->value, 3));
                    break;
                default:
                    throw new Exception("Invalid frame prefix.", 32);
            }
        } elseif ($symbol->type === "nil") {
            $value = null;
        } elseif ($symbol->type === "int") {
            $value = (int)$symbol->value;
        } elseif ($symbol->type === "bool") {
            if (strtolower($symbol->value) === "true") {
                $value = true;
            } elseif (strtolower($symbol->value) === "false") {
                $value = false;
            } else {
                throw new Exception("Bool must be TRUE or FALSE!", 32);
            }
        } elseif ($symbol->type === "string") {
            if ($symbol->value == null) {
                $value = "";
            } else {
                $value = preg_replace_callback('/\\\\([0-9]{3})/', function ($matches) {
                    return chr((int)$matches[1]);
                }, $symbol->value);
            }
        } else {
            $value = $symbol->value;
        }

        return $value;
    }

}