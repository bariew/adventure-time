<?php $this->renderPartial('//default/header');?>
<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array(
    'id' => 'page',
    'type' => 'vertical',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
    <?php echo $form->errorSummary($model); ?>
    <fieldset class="yellow">
    <?php $this->beginClip('_content'); ?>
        <?php echo $form->textFieldRow($model, 'title', array('class' => 'span10')); ?>
        <?php echo $form->textAreaRow($model, 'brief', array('class' => 'span10')); ?>
        <div class="control-group">
            <?php echo CHtml::activeLabelEx($model, 'content', array('class'=>'control-label')); ?>
            <div class="controls">
                <?php $this->widget('ext.redactor.ERedactorWidget',array(
                    'model'     => $model,
                    'attribute' => 'content',
                    'options'   => array(
                        'minHeight' => 300,
                        'imageUpload'=>Yii::app()->createUrl('/base/backend/imageUpload',array(
                            'attr'=>'content'
                        )),
                     )
                ));?>
            </div>
        </div>
    <?php $this->endClip(); ?>

    <?php $this->beginClip('_params'); ?>
        <?php echo $form->textFieldRow($model, 'page_title', array('class' => 'span10')); ?>
        <?php echo $form->textFieldRow($model, 'page_keywords', array('class' => 'span10')); ?>
        <?php echo $form->textFieldRow($model, 'page_description', array('class' => 'span10')); ?>
        <?php echo $form->textFieldRow($model, 'label', array('class' => 'span10 link')); ?>
        <?php echo $form->textFieldRow($model, 'name', array('class' => 'span10 link')); ?>
        <?php echo $form->textFieldRow($model, 'layout', array('class' => 'span10')); ?>
        <?php echo $form->checkBoxRow($model, 'visible', array('class' => 'link')); ?>
    <?php $this->endClip(); ?>

        <?php $this->widget('ext.bootstrap.widgets.BootTabbed', array(
            'type' => 'tabs',
            'tabs' => array(
                array('label' => 'Home', 'content' => $this->clips['_content']),
                array('label' => 'Parameters', 'content' => $this->clips['_params']),
            ),
        )); ?>

        <div class="form-actions">
            <?php echo CHtml::htmlButton(BHtml::icon('ok white').' Save', array('class' => 'btn btn-primary', 'type' => 'submit')); ?>
            <?php echo CHtml::link(BHtml::icon('ban-circle').' Cancel',
               array('index'),
               array('title'=>'Cancel', 'class'=>'btn small')); ?>
        </div>
    </fieldset>
<?php $this->endWidget(); ?>