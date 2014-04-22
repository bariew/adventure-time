<?php $this->beginContent('//layouts/main'); ?>
    <?php $this->renderPartial('//default/header');?>
    <div class="ajaxWrapper"><?php echo $content; ?></div>
<?php $this->endContent(); ?>