<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array('type' => 'vertical')); ?>
    <?php $this->renderPartial('//default/header');?>
    <fieldset class="popup text-center">
        <div class="form no-label">
            <?php echo $form->passwordFieldRow($ChangePasswordForm, 'password', array('placeholder'=>'Password')); ?>
            <?php echo $form->passwordFieldRow($ChangePasswordForm, 'password_repeat', array('placeholder'=>'Password repeat')); ?>
            <div class="form-actions">
                <?php echo CHtml::htmlButton('<i class="icon-ok"></i>Save', array('class'=>'btn', 'type' => 'submit')); ?>
            </div>
        </div>
    </fieldset>
<?php $this->endWidget(); ?>