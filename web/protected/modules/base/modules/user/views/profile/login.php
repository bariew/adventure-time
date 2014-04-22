<?php if(!@$_GET['ajax']): ?>
	<?php $this->renderPartial('//default/header');?>	    
<?php endif; ?>
<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array(
    'id' => 'user',
    'type' => 'vertical',
    'htmlOptions'   => array(
       'class'=>'text-center'
    )
)); ?>
<?php if(Yii::app()->user->id): ?>
<script>
	$.colorbox.close();
	window.location.href = "<?php echo $this->getLoginRedirect(); ?>";
</script>
<?php else:?>
    <fieldset class="yellow view">
        <?php echo $form->textFieldRow($LoginForm, 'email'); ?>
        <?php echo $form->passwordFieldRow($LoginForm, 'password'); ?>
        <div class="form-actions">
            <?php echo CHtml::htmlButton('Log in', array(
                'class'=>'btn btn-primary', 'type' => 'submit'
            )); ?>
            <br /><br />
            <?php echo CHtml::link('Register',
                array('registration'),
                array('class'=>'colorbox text-success')
            ); ?>
            <?php echo CHtml::link('Recover',
                array('passwordRecovery'),
                array('class'=>'colorbox text-error')
            ); ?>
        </div>
    </fieldset>
	<div class="enterSocial">
		<ul class="inline text-center">
			<?php Yii::app()->eauth->renderWidget();?>
		</ul>
	</div>
<?php endif;?>
<?php $this->endWidget(); ?>
