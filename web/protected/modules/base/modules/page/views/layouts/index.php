<?php $this->beginContent('//layouts/main'); ?>
      <div class="well well-small">
          <img class="help-inline img-circle" src="/themes/default/assets/img/logo.png">
          <h1 class="help-inline"><?php echo Yii::app()->name; ?></h1>
          <i class="help-inline"><?php echo $this->page->brief ;?></i>
      </div>
      <div class="span11"></div>
<?php $this->endContent(); ?>