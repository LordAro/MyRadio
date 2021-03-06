<?php

/**
 * Allows the addition of new quotes.
 *
 * @version 20140113
 * @author  Matt Windsor <mattbw@ury.org.uk>
 * @package MyRadio_Quotes
 */

use \MyRadio\MyRadio\CoreUtils;
use \MyRadio\ServiceAPI\ServiceAPI;
use \MyRadio\ServiceAPI\MyRadio_Quote;

CoreUtils::getTemplateObject()->setTemplate('table.twig')
    ->addVariable('title', 'Quotes')
    ->addVariable('tabledata', ServiceAPI::setToDataSource(MyRadio_Quote::getAll()))
    ->addVariable('tablescript', 'myradio.quotes')
    ->render();
