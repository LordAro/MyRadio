<?php
/**
 * 
 * @todo Proper Documentation
 * @author Lloyd Wallis <lpw@ury.org.uk>
 * @version 21072012
 * @package MyURY_Scheduler
 */

//The Form definition
require 'Models/MyURY/Scheduler/seasonfrm.php';

echo nl2br(print_r($form->readValues(),true));

try {
  MyURY_Show::create($form->readValues());
} catch (MyURYException $e) {
  require 'Views/Errors/500.php';
  exit;
}

header('Location: '.CoreUtils::makeURL('Scheduler', 'myShows'));