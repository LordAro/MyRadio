<?php
/**
 * List all trainers
 *
 * @author Lloyd Wallis <lpw@ury.org.uk>
 * @version 20131014
 * @package MyRadio_Profile
 */

use \MyRadio\MyRadio\CoreUtils;
use \MyRadio\ServiceAPI\MyRadio_TrainingStatus;

$trainers = CoreUtils::dataSourceParser(
    MyRadio_TrainingStatus::getInstance(3)->getAwardedTo()
);

foreach ($trainers as $key => $value) {
    $trainers[$key]['awarded_time'] = date('Y/m/d', $trainers[$key]['awarded_time']);
}

CoreUtils::getTemplateObject()->setTemplate('table.twig')
    ->addVariable('tablescript', 'myradio.profile.listTrainers')
    ->addVariable('title', 'Trainers List')
    ->addVariable('tabledata', $trainers)
    ->render();
