<!-- левое меню для bs2 -->
<?php $this->beginContent('begemot.views.layouts.main'); ?>

<div class="row-fluid">
  <div class="span3">
      	<?php

        $this->widget('begemot.extensions.bootstrap.widgets.TbMenu', array(
           'type'=>'list',
           'items'=>$this->menu,
       ));

	?>
      
  </div>
  <div class="span9"><?php echo $content; ?></div>
</div>

<?php $this->endContent(); ?>