<?php $this->renderPartial('//default/header');?>
<div class="form-actions">
    <?php echo CHtml::link('Delete '.get_class($model).'?', 
        array('', 'id'=>$model['id'], 'confirm'=>1),
        array('class'=>'btn btn-danger')
    ); ?>
</div>