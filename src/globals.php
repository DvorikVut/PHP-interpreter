<?php

use IPP\Student\Frame;
use IPP\Student\Labels;
use IPP\Student\Stack;

require_once 'Labels.php';
require_once 'Frame.php';
require_once 'Stack.php';

global $frameStack;
$frameStack = new Stack();
$frameStack->push(null);

global $localFrame;
$localFrame = null;

global $temporaryFrame;
$temporaryFrame = null;

global $globalFrame;
$globalFrame = new Frame();

global $stack;
$stack = new Stack();

global $labels;
$labels = new Labels();

global $currentOrder;
$currentOrder = 1;

global $orderStack;
$orderStack = new Stack();

global $readInput;
