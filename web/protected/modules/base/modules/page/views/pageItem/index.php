<?php $this->renderPartial('//default/header');?>
<fieldset class="yellow">
<?php $this->widget('ext.bootstrap.widgets.BootGridView', array(
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-condensed',
    'summaryText'   => '',
    'columns' => array(
        'label',
        'url',
        'layout',
        array(
        	'name'=>'visible',
        	'type'=>'raw',
        	'value'=>'CHtml::tag("i", array("class"=>$data->visible ? "icon-ok" : "icon-remove"))',
        ),
        array(
            'class' => 'ext.bootstrap.widgets.BootButtonColumn',
            'template' => '{update} {delete}',
            'htmlOptions' => array('style' => 'width: 50px'),
        ),
    ),
));?>
</fieldset>