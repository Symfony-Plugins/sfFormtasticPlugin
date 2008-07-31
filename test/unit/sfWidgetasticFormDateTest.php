<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(6, new lime_output_color);

$yearFrom = strftime('%Y', strtotime('-18 years'));
$yearTo = strftime('%Y');

$yearTop = <<<EOF
<select name="date[year]" id="date_year">
<option value="$yearFrom">$yearFrom</option>
EOF;

$yearBottom = <<<EOF
<option value="$yearTo">$yearTo</option>
</select>
EOF;

$widget = new sfWidgetasticFormDate(array('year_from' => $yearFrom, 'can_be_empty' => false));
$t->like($widget->render('date'), '/'.preg_quote($yearTop, '/').'/', 'literal "year_from" ok');
$t->like($widget->render('date'), '/'.preg_quote($yearBottom, '/').'/', 'ending year set ok');

$widget = new sfWidgetasticFormDate(array('year_from' => '-18 years', 'can_be_empty' => false));
$t->like($widget->render('date'), '/'.preg_quote($yearTop, '/').'/', 'calculated "year_from" ok');

$widget = new sfWidgetasticFormDate(array('year_format' => '%y'));
$t->like($widget->render('date'), '/>'.strftime('%y').'</', '"year_format" works');

$widget = new sfWidgetasticFormDate(array('month_format' => '%B'));
$t->like($widget->render('date'), '/>January</', '"month_format" works');

$widget = new sfWidgetasticFormDate(array('day_format' => '%d'));
$t->like($widget->render('date'), '/>01</', '"day_format" works');
