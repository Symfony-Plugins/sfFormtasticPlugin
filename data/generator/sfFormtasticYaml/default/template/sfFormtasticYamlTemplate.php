/**
 * <?php echo $this->form->getClass() ?>.
 * 
 * @package     symfony
 * @subpackage  form
 * @author      auto-generated from YAML
 * @version     SVN: $Id$
 */
class <?php echo $this->form->getClass() ?> extends sfFormtastic
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    $this->setWidgets(array(
<?php foreach ($this->form->getFields() as $field): ?>
      <?php echo $this->var_export($field->getName()) ?><?php echo $this->form->getPaddingForFieldName($field->getName()) ?> => <?php echo $field->generateWidget() ?>,
<?php endforeach; ?>
    ));

    $this->setValidators(array(
<?php foreach ($this->form->getFields() as $field): ?>
<?php if ($field->countValidators() > 1): ?>
      <?php echo $this->var_export($field->getName()) ?><?php echo $this->form->getPaddingForFieldName($field->getName()) ?> => new sfValidatorAnd(array(
<?php foreach ($field->getValidators() as $validator): ?>
        <?php echo $validator->generate() ?>,
<?php endforeach; ?>
      ), <?php echo $field->isRequired() ? 'array(\'required\' => true)' : 'array()'?>, <?php echo $field->isRequired() ? $this->var_export(array('required' => $field->getRequiredMessage())) : 'array()' ?>),
<?php else: ?>
      <?php echo $this->var_export($field->getName()) ?><?php echo $this->form->getPaddingForFieldName($field->getName()) ?> => <?php echo $field->getFirstValidator()->generate() ?>,
<?php endif; ?>
<?php endforeach; ?>
    ));
<?php if ($labels = $this->form->getLabels()): ?>

    $this->widgetSchema->setLabels(array(
<?php foreach ($labels as $field => $label): ?>
      <?php echo $this->var_export($field) ?> => <?php echo $this->var_export($label) ?>,
<?php endforeach; ?>
    ));
<?php endif; ?>
<?php if ($helps = $this->form->getHelps()): ?>

    $this->widgetSchema->setHelps(array(
<?php foreach ($helps as $field => $help): ?>
      <?php echo $this->var_export($field) ?> => <?php echo $this->var_export($help) ?>,
<?php endforeach; ?>
    ));
<?php endif; ?>
<?php if ($nameFormat = $this->form->getNameFormat()): ?>

    $this->widgetSchema->setNameFormat(<?php echo $this->var_export($nameFormat) ?>);
<?php endif; ?>
  }
}

