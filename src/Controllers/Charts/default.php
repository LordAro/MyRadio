<?php
CoreUtils::getTemplateObject(
)->setTemplate(
  'table.twig'
)->addVariable(
  'tablescript',
  'myury.datatable.default'
)->addVariable(
  'title',
  'Charts'
)->addVariable(
  'tabledata',
  ServiceAPI::setToDataSource(MyRadio_ChartType::getAll())
)->render();
?>