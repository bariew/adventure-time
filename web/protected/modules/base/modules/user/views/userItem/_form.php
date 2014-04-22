<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array('type' => 'horizontal', 'htmlOptions'=>array('enctype'=>'multipart/form-data'))); ?>
    <?php $this->renderPartial('//default/header');?>
    <fieldset class="yellow">
        <div class="controls">
            <?php echo BHtml::imageArea($model, 'image'); ?>
            <div class="clearfix"></div>
        </div>
        <?php echo $form->textFieldRow($model, 'name', array('autocomplete'=>'off')); ?>
        <?php echo $form->textFieldRow($model, 'email', array('autocomplete'=>'off')); ?>
        <?php echo $form->textFieldRow($model, 'phone', array('autocomplete'=>'off')); ?>
        <?php if(Yii::app()->user->level('admin')): ?>
            <?php echo $form->dropDownListRow($model, 'role', $model->roleList()); ?>
            <?php echo $form->checkBoxRow($model, 'active'); ?>
        <?php endif; ?>
        <?php echo $form->passwordFieldRow($model, 'new_password', array('autocomplete'=>'off')); ?>
        <?php echo $form->passwordFieldRow($model, 'new_password_repeat', array('autocomplete'=>'off')); ?>
        <div class="form-actions">
            <?php echo CHtml::htmlButton(
                '<i class="icon-ok icon-white"></i>'
                . ($model->isNewRecord ? ' Register' : ' Update'), 
                array('class'=>'btn btn-primary', 'type' => 'submit')
            ); ?>
        </div>
    </fieldset>
<?php $this->endWidget(); ?>