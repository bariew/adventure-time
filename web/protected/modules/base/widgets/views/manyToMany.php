<h4>Attached <?php echo $this->relation; ?></h4>
<?php $fieldName = get_class($this->model)."[{$this->relation}]"; ?>
<?php echo CHtml::hiddenField($fieldName, 0); ?>
<?php echo CHtml::checkBoxList($fieldName, $selected, $all, array('separator'=>'  ', 'labelOptions'=>array('style'=>'display:inline'))); ?>