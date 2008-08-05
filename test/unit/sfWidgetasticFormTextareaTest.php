<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(3, new lime_output_color);

$widget = new sfWidgetasticFormTextarea;
$t->is($widget->render('comment'), '<textarea name="comment" id="comment"></textarea>', '->render() does not include "rows" or "cols" attributes if not explicitly defined');

$widget = new sfWidgetasticFormTextarea(array(), array('rows' => 10));
$t->is($widget->render('comment'), '<textarea rows="10" name="comment" id="comment"></textarea>', '->render() includes "rows" attributes when explicitly defined');

$widget = new sfWidgetasticFormTextarea(array(), array('cols' => 30));
$t->is($widget->render('comment'), '<textarea cols="30" name="comment" id="comment"></textarea>', '->render() includes "cols" attributes when explicitly defined');
