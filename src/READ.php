<?php

namespace IPP\Student;

use Exception;
use IPP\Core\FileInputReader;

class READ extends Instruction
{
    private Argument $arg1;
    private Argument $arg2;

    public function __construct(string $opcode, int $order, Argument $arg1, Argument $arg2)
    {
        parent::__construct($opcode, $order);
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function run() : void
    {
        global $readInput;


        $frame = $this->get_Frame($this->arg1);
        if($this->arg2->type != "type"){
            throw new Exception("Second argument must be of type type", 53);
        }

        $value = $this->getSymbolValue($this->arg2);

        //check if it [int] or [bool] or [string]
           if($value != "int" && $value != "bool" && $value != "string"){
               throw new Exception("Invalid type", 53);
            }
           $input = '';
       if($readInput === null) {
           $frame->update(substr($this->arg1->value,3 ), "");
              return;
       }
       if($value == "int"){
           $input = $readInput->readInt();
              if($input !== null){
                $frame->update(substr($this->arg1->value,3 ), (int)$input);
                return;
           }
       }
         elseif($value == "bool"){
              $input = $readInput->readBool();
              if($input !== null){
                $frame->update(substr($this->arg1->value,3 ), (boolean)$input);
                return;
              }
         }
            elseif($value == "string") {
                $input = $readInput->readString();
                if ($input !== null) {
                    $frame->update(substr($this->arg1->value, 3), (string)$input);
                    return;
                }
            }
        $frame->update(substr($this->arg1->value, 3), $input);

    }
}
