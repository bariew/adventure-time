<?php $this->renderPartial('//default/header');?>
<fieldset class="yellow view">
    <div class="inner">
        <div class="switcher"><?php echo CHtml::activeLabel($model, 'name') ;?></div>
        <div class="control-label"><?php echo $model['name']; ?></div>
        
        <div class="switcher"><?php echo CHtml::activeLabel($model, 'email') ;?></div>
        <div class="control-label"><?php echo $model['email']; ?></div>
        
        <div class="switcher"><?php echo CHtml::activeLabel($model, 'phone') ;?></div>
        <div class="control-label"><?php echo $model['phone']; ?></div>
    </div>

    <div class="form-actions">
        <?php echo CHtml::link('Change info', array('update', 'id'=>$model->id), array('class' => 'btn btn-primary span2')); ?>
    </div>    
</fieldset>
