<?php $this->renderPartial('//default/header');?>
<fieldset class="yellow">
<?php $this->widget('ext.bootstrap.widgets.BootGridView', array(
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped table-condensed',
    'summaryText'   => '',
    'columns' => array(
        'name',
        'role',
        'email',
        'phone',
        array(
            'name' => 'create_time',
            'value' => 'date("j.n.Y", $data->create_time)',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'width: 100px'),
        ),
        array(
            'name'  => 'active',
            'type'  => 'raw',
            'value' => '$data->active ? "Yes" : "No"' 
        ),
        array(
            'class' => 'ext.bootstrap.widgets.BootButtonColumn',
            'template' => '{update} {delete}',
            'htmlOptions' => array('style' => 'width: 70px'),
        ),
    ),
)); ?>
</fieldset>