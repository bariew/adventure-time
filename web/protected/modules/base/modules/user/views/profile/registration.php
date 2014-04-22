<?php if(Yii::app()->user->id): ?>
    <script>
        $.colorbox.close();
        window.location.href = "<?php echo $this->getLoginRedirect(); ?>";
    </script>
<?php endif;?>

<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array('type' => 'vertical')); ?>
    <?php $this->renderPartial('//default/header');?>
    <fieldset>
        <?php echo $form->textFieldRow($RegistrationForm, 'name', array('placeholder'=>'name')); ?><br />
        <?php echo $form->textFieldRow($RegistrationForm, 'email', array('placeholder'=>'email')); ?><br />
        <?php echo $form->passwordFieldRow($RegistrationForm, 'password', array('placeholder'=>'Password')); ?><br />
        <?php echo $form->passwordFieldRow($RegistrationForm, 'password_repeat', array('placeholder'=>'Password repeat')); ?>
        <div class="form-actions">
            <?php echo CHtml::htmlButton('<i class="icon-ok"></i>Register', array('class'=>'btn', 'type' => 'submit')); ?>
        </div>
    </fieldset>
<?php $this->endWidget(); ?>