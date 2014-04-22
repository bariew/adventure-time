<?php $this->beginContent('//layouts/admin'); ?>
    <div class="span4">
        <div class="well sidebar">
            <?php if($this->searchBar){
                //$this->widget('AdminSearchWidget', array());
            }; ?>
            <?php $this->module->adminMenu(); ?>
        </div>
    </div>
    <div class="span10 ajaxWrapper">
        <?php echo $content; ?>
    </div>
<?php $this->endContent(); ?>