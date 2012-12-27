<?php
/**
 * Controller for outputting a Datatable of Seasons within the specified Show
 * @author Lloyd Wallis <lpw@ury.org.uk>
 * @version 26122012
 * @package MyURY_Scheduler
 */

$show = MyURY_Show::getInstance($_GET['showid']);
$seasons = $show->getAllSeasons();
require 'Views/MyURY/Scheduler/seasonList.php';