<?php
/**
 *
 */
use \MyRadio\MyRadio\CoreUtils;
use \MyRadio\ServiceAPI\MyRadio_Scheduler;

$terms = array_map(
    function ($x) {
        $x['start'] = CoreUtils::happyTime($x['start'], false);
        return $x;
    },
    MyRadio_Scheduler::getTerms()
);

CoreUtils::getTemplateObject()->setTemplate('Scheduler/listTerms.twig')
    ->addVariable('title', 'Terms')
    ->addVariable('tabledata', CoreUtils::dataSourceParser($terms))
    ->addVariable('tablescript', 'myradio.scheduler.termlist')
    ->render();
