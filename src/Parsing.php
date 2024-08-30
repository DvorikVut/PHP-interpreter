<?php

namespace IPP\Student;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use IPP\Student\Argument;
use IPP\Student\Move;

class Parsing
{

    /**
     * @brief arg_check
     * @param DOMElement $instruction
     * @param int $count_of_args
     * @throws Exception 32
     */

    public static function arg_check(DOMElement $instruction, int $count_of_args): void
    {
        for ($i = 1; $i <= $count_of_args; $i++) {
            $args = $instruction->getElementsByTagName('arg' . $i);
            if ($args->length === 0) {
                throw new Exception("Missing argument", 32);
            }
            $arg = $args->item(0);
            if (!($arg instanceof DOMElement)) {
                throw new Exception("Invalid argument type", 32);
            }
            if ($arg->getAttribute('type') === '') {
                throw new Exception("Missing argument type", 32);
            }
            if ($arg->nodeValue === '') {
                if ($arg->getAttribute('type') !== 'nil' && $arg->getAttribute('type') !== 'string') {
                    throw new Exception("Missing argument value", 32);
                }
            }
        }
    }

    /**
     * @brief parseXML
     * @details Parse XML file and create array of instructions
     * @param DOMDocument $XMLFile
     * @throws Exception
     * @return Instruction[]
     */
    public static function parseXML(DOMDocument $XMLFile): array
    {
        $instructions_list = [];
        $XMLFile->preserveWhiteSpace = false;
        $xpath = new DOMXPath($XMLFile);

        $instructions = $xpath->query('/program/instruction');

        if ($instructions === false) {
            throw new Exception("Failed to query instructions from XML.", 33);
        }

        foreach ($instructions as $instructionNode){
            if (!($instructionNode instanceof DOMElement)) {
                continue;
            }
            $instruction = $instructionNode;
            $order = $instruction->getAttribute('order');
            if((int)$order < 1){
                throw new Exception("Order must be greater than 0", 32);
            }
            if ($order === '') {
                throw new Exception("Missing instruction order attribute", 34);
            }
            if (array_key_exists($order, $instructions_list)) {
                throw new Exception("Instruction with order $order already exists", 32);
            }

            $opcode = strtoupper($instruction->getAttribute('opcode'));

            //// CREATE ALL LABELS FIRST ////

            if($opcode === 'LABEL'){
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for LABEL instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $label_ins = new LABEL(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $label_ins;
                $label_ins->run();
            }
        }

            //// PARSE ALL OTHERS INSTRUCTIONS ////

        foreach ($instructions as $instructionNode) {
            if (!($instructionNode instanceof DOMElement)) {
                continue;
            }

            $instruction = $instructionNode;

            $order = $instruction->getAttribute('order');
            if ($order === '') {
                throw new Exception("Missing instruction order attribute", 34);
            }
            if (array_key_exists($order, $instructions_list) && $instruction->getAttribute('opcode') !== 'LABEL'){
                throw new Exception("Instruction with order $order already exists", 32);
            }

            $opcode = strtoupper($instruction->getAttribute('opcode'));

            ////  Create instruction object based on opcode ////

            if ($opcode === 'MOVE') {
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for MOVE instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $move_ins = new Move(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $move_ins;
            }
            elseif ($opcode === 'TYPE') {
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for TYPE instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $type_ins = new TYPE(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $type_ins;
            }
            elseif($opcode === 'READ'){
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for READ instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $read_ins = new READ(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $read_ins;
            }
            elseif ($opcode === 'LABEL'){  // LABELS ARE CREATED FIRST, SO WE SKIP THEM HERE
                continue;
            }
            elseif ($opcode === 'DEFVAR') {
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for DEFVAR instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $defvar_ins = new DEFVAR(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $defvar_ins;
            }
            elseif ($opcode === 'CREATEFRAME') {
                $createframe_ins = new CREATEFRAME($opcode, (int)$order);
                $instructions_list[$order] = $createframe_ins;
            }
            elseif ($opcode === 'PUSHFRAME') {
                $pushframe_ins = new PUSHFRAME($opcode, (int)$order);
                $instructions_list[$order] = $pushframe_ins;
            }
            elseif ($opcode === 'POPFRAME') {
                $popframe_ins = new POPFRAME($opcode, (int)$order);
                $instructions_list[$order] = $popframe_ins;
            }
            elseif ($opcode === 'CALL') {
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for CALL instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $call_ins = new CALL(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $call_ins;
            }
            elseif ($opcode === 'RETURN') {
                $return_ins = new iRETURN($opcode, (int)$order);
                $instructions_list[$order] = $return_ins;
            }
            elseif ($opcode === 'PUSHS') {
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for PUSHS instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $pushs_ins = new PUSH(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $pushs_ins;
            }
            elseif ($opcode === 'POPS') {
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for POPS instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $pops_ins = new POP(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $pops_ins;
            }
            elseif($opcode === 'ADD'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for ADD instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $add_ins = new ADD(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $add_ins;
            }
            elseif($opcode === 'SUB'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for SUB instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $sub_ins = new SUB(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $sub_ins;
            }
            elseif($opcode === 'MUL'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for MUL instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $mul_ins = new MUL(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $mul_ins;
            }
            elseif($opcode === 'IDIV'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for IDIV instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $idiv_ins = new IDIV(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $idiv_ins;
            }
            elseif($opcode === 'LT'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for LT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $lt_ins = new LT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $lt_ins;
            }
            elseif($opcode === 'GT'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for GT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $gt_ins = new GT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $gt_ins;
            }
            elseif($opcode === 'EQ'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for EQ instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $eq_ins = new EQ(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $eq_ins;
            }
            elseif($opcode === 'AND'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for AND instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $and_ins = new ANDi(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $and_ins;
            }
            elseif($opcode === 'OR'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for OR instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $or_ins = new ORi(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $or_ins;
            }
            elseif($opcode === 'WRITE'){
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for WRITE instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $write_ins = new WRITE(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $write_ins;
            }
            elseif($opcode === 'STRLEN'){
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for STRLEN instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $strlen_ins = new STRLEN(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $strlen_ins;
            }
            elseif($opcode === 'CONCAT'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for CONCAT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $concat_ins = new CONCAT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $concat_ins;
            }
            elseif ($opcode === 'STRI2INT'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for STRI2INT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $stri2int_ins = new STRI2INT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $stri2int_ins;
            }
            elseif($opcode === 'INT2CHAR'){
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for INT2CHAR instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $int2char_ins = new INT2CHAR(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $int2char_ins;
            }
            elseif($opcode === 'GETCHAR'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for GETCHAR instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $getchar_ins = new GETCHAR(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $getchar_ins;
            }
            elseif($opcode === 'SETCHAR'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for SETCHAR instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $setchar_ins = new SETCHAR(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $setchar_ins;
            }
            elseif($opcode === 'BREAK'){
                $break_ins = new BREAKi($opcode, (int)$order);
                $instructions_list[$order] = $break_ins;
            }
            elseif($opcode === 'JUMP'){
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for JUMP instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $jump_ins = new JUMP(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $jump_ins;
            }
            elseif($opcode === 'JUMPIFEQ'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for JUMPIFEQ instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $jumpifeq_ins = new JUMPIFEQ(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $jumpifeq_ins;
            }
            elseif($opcode === 'NOT'){
                self::arg_check($instruction, 2);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                if (!$arg1 || !$arg2 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for NOT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $not_ins = new NOT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value)
                );
                $instructions_list[$order] = $not_ins;
            }
            elseif($opcode === 'JUMPIFNEQ'){
                self::arg_check($instruction, 3);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
                $arg3 = $instruction->getElementsByTagName('arg3')->item(0);
                if (!$arg1 || !$arg2 || !$arg3 || !($arg1 instanceof DOMElement) || !($arg2 instanceof DOMElement) || !($arg3 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for JUMPIFNEQ instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $arg2Value = $arg2->nodeValue !== null ? $arg2->nodeValue : "";
                $arg3Value = $arg3->nodeValue !== null ? $arg3->nodeValue : "";
                $jumpifneq_ins = new JUMPIFNEQ(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value),
                    new Argument($arg2->getAttribute('type'), $arg2Value),
                    new Argument($arg3->getAttribute('type'), $arg3Value)
                );
                $instructions_list[$order] = $jumpifneq_ins;
            }
            elseif($opcode === 'EXIT'){
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for EXIT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $exit_ins = new EXITi(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $exit_ins;
            }
            elseif($opcode === 'DPRINT'){
                self::arg_check($instruction, 1);
                $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
                if (!$arg1 || !($arg1 instanceof DOMElement)) {
                    throw new Exception("Failed to get arguments for DPRINT instruction", 35);
                }
                $arg1Value = $arg1->nodeValue !== null ? $arg1->nodeValue : "";
                $dprint_ins = new DPRINT(
                    $opcode,
                    (int)$order,
                    new Argument($arg1->getAttribute('type'), $arg1Value)
                );
                $instructions_list[$order] = $dprint_ins;
            }
            else {
                throw new Exception("Unknown opcode", 32);
            }
        }
        return $instructions_list;
    }
}
