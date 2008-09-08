<?php

/**
 * An extension of sfWidgetFormDate.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  widget
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfWidgetasticFormDate extends sfWidgetFormDate
{
  /**
   * Adds the following options to sfWidgetFormDate:
   * 
   *  * year_from:  The earliest year option to present (default to -5 years or this year if year_to is set)
   *  * year_to:    The latest year option to present (default to +5 years or this year if year_from is set)
   * 
   * Both of these options can be either numeric years or a strings parsable
   * by {@link strtotime()}.
   * 
   *  * year_format:  A {@link strftime()} year format string (default to %Y)
   *  * month_format: A {@link strftime()} month format string (default to %m)
   *  * day_format:   A {@link strftime()} day format string (default to %d)
   * 
   * @see sfWidgetFormDate
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('year_from');
    $this->addOption('year_to');
    $this->addOption('year_format', '%Y');
    $this->addOption('month_format', '%m');
    $this->addOption('day_format', '%d');
  }

  /**
   * @see sfWidgetFormDate
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $monthFormat = $this->getFormatOption('month_format');
    $dayFormat   = $this->getFormatOption('day_format');
    $yearFormat  = $this->getFormatOption('year_format');

    // year_from and year_to
    $yearFrom = $this->getOption('year_from');
    $yearTo   = $this->getOption('year_to');
    if (!is_null($yearFrom) || !is_null($yearTo))
    {
      $yearFrom = is_null($yearFrom) ? date('Y') : (ctype_digit($yearFrom) ? $yearFrom : date('Y', strtotime($yearFrom)));
      $yearTo   = is_null($yearTo)   ? date('Y') : (ctype_digit($yearTo)   ? $yearTo   : date('Y', strtotime($yearTo)));

      $yearRange = range($yearFrom, $yearTo);
    }
    else
    {
      $yearRange = range(date('Y') - 5, date('Y') + 5);
    }

    // year_format
    $years = array();
    foreach ($yearRange as $year)
    {
      $years[$year] = strftime($yearFormat, strtotime($year.'-01-01'));
    }
    $this->setOption('years', $years);

    // month_format
    $months = array();
    foreach (range(1, 12) as $month)
    {
      $months[$month] = strftime($monthFormat, strtotime(sprintf('2000-%02d-01', $month)));
    }
    $this->setOption('months', $months);

    // day_format
    $days = array();
    foreach (range(1, 31) as $day)
    {
      $days[$day] = strftime($dayFormat, strtotime(sprintf('2000-01-%02d', $day)));
    }
    $this->setOption('days', $days);

    return parent::render($name, $value, $attributes, $errors);
  }

  /**
   * Get an option value to be used with {@link strftime()}.
   * 
   * @param   string $name
   * 
   * @return  string
   */
  protected function getFormatOption($name)
  {
    $value = $this->getOption($name);

    if ($value && '%' != $value{0})
    {
      $value = '%'.$value;
    }

    return $value;
  }
}
