<?php

namespace IPP\Student;

use Exception;

use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\FileInputReader;
use IPP\Core\Settings;
use IPP\Student\Parsing;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {

        global $readInput;
        $settings = new Settings();
        $settings->processArgs();
        $readInput = $settings->getInputReader();
        $XMLFile = $this->source->getDOMDocument();
        $parser = new Parsing();
        try{
        $instructions_list = $parser->parseXML($XMLFile);
        }catch(Exception $e){
            fwrite(STDERR, $e->getMessage() . "\n" . $e->getCode() . "\n");
            exit($e->getCode());
        }

        global $currentOrder;

        $firstOrder = 1;
        foreach ($instructions_list as $order => $instruction)
        {
            if($order < $firstOrder)
            {
                $firstOrder = $order;
            }
        }
        $lastOrder = 0;
        foreach($instructions_list as $order => $instruction)
        {
            if($order > $lastOrder)
            {
                $lastOrder = $order;
            }
        }

        $currentOrder = $firstOrder;

        while($currentOrder <= $lastOrder)
        {
            if(!isset($instructions_list[$currentOrder]))
            {
                $currentOrder++;
                continue;
            }
            $instruction = $instructions_list[$currentOrder];
            try {
                $instruction->run();
            } catch (Exception $e){
                fwrite(STDERR, $e->getMessage(). "\n". $e->getCode(). "\n");
                exit($e->getCode());
            }
            $currentOrder++;
        }
        return 0;
    }
}
