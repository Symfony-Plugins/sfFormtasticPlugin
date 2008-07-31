<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(9, new lime_output_color);

$hoursIntervalTest = <<<EOF
<select name="time[hour]" id="time_hour">
<option value="01">01</option>
<option value="03">03</option>
EOF;

$hoursIntervalTest24 = <<<EOF
<select name="time[hour]" id="time_hour">
<option value="00">00</option>
<option value="02">02</option>
EOF;

$minutesIntervalTest = <<<EOF
<select name="time[minute]" id="time_minute">
<option value="00">00</option>
<option value="05">05</option>
EOF;

$secondsIntervalTest = <<<EOF
<select name="time[second]" id="time_second">
<option value="00">00</option>
<option value="05">05</option>
EOF;

$twentyFourHourTest = <<<EOF
<option value="12">12</option>
</select>
EOF;

$widget = new sfWidgetasticFormTime(array('format_without_seconds' => ''));
$t->is($widget->render('time'), '', 'uses "format_without_seconds" by default');

$widget = new sfWidgetasticFormTime(array('format' => '', 'with_seconds' => true));
$t->is($widget->render('time'), '', 'uses "format" if with_seconds is on');

$widget = new sfWidgetasticFormTime(array('format_without_seconds_or_ampm' => '', '24_hour_clock' => true));
$t->is($widget->render('time'), '', 'uses "format_without_seconds_or_ampm" if 24_hour_clock is on');

$widget = new sfWidgetasticFormTime(array('format_without_ampm' => '', '24_hour_clock' => true, 'with_seconds' => true));
$t->is($widget->render('time'), '', 'uses "format_without_ampm" if 24_hour_clock and with_seconds are on');

$widget = new sfWidgetasticFormTime(array('hours_interval' => 2, 'can_be_empty' => false));
$t->like($widget->render('time'), '/'.preg_quote($hoursIntervalTest, '/').'/', '"hours_interval" option works');

$widget = new sfWidgetasticFormTime(array('hours_interval' => 2, '24_hour_clock' => true, 'can_be_empty' => false));
$t->like($widget->render('time'), '/'.preg_quote($hoursIntervalTest24, '/').'/', '"hours_interval" option works when 24_hour_clock is on');

$widget = new sfWidgetasticFormTime(array('minutes_interval' => 5, 'can_be_empty' => false));
$t->like($widget->render('time'), '/'.preg_quote($minutesIntervalTest, '/').'/', '"minutes_interval" option works');

$widget = new sfWidgetasticFormTime(array('with_seconds' => true, 'seconds_interval' => 5, 'can_be_empty' => false));
$t->like($widget->render('time'), '/'.preg_quote($secondsIntervalTest, '/').'/', '"seconds_interval" option works');

$widget = new sfWidgetasticFormTime(array('can_be_empty' => false));
$t->like($widget->render('time'), '/'.preg_quote($twentyFourHourTest, '/').'/', '"24_hour_clock" defaults to false');







