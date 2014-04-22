<?php echo CHtml::beginForm(array('adminSearch'), 'get', array('class'=>'form-search')); ?>
	<?php echo CHtml::textField('adminSearch', $searchString, array('class'=>'input-small search-query')); ?>
	<?php foreach($addQuery as $name=>$value): ?>
		<?php echo CHtml::hiddenField($name, $value); ?>
	<?php endforeach; ?>
	<?php echo CHtml::htmlButton('<i class="icon-search"></i>', array('class'=>'btn pull-right', 'type'=>'submit')); ?>
<?php echo CHtml::endForm(); ?>