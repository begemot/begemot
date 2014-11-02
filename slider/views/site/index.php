
<div class="container_for_main_slider">

   <div class="bx-wrapper" style="max-width: 100%;"><div class="bx-viewport" style="width: 100%; overflow: hidden; position: relative; height: 650px;"><div class="bxslider" style="width: 615%; position: relative; -webkit-transition: 0s; transition: 0s; -webkit-transform: translate3d(-3200px, 0, 0);"><div class="slide bx-clone" style="float: left; list-style: none; position: relative; width: 800px;">

       <div class="aaa">                          
               <div class='w102 overflow-hidden'>
                   <img src="<?php echo $slides[0]->image; ?>" alt="">
               </div>
           </div>
       </div>



   <?php for($i = 1; $i < count($slides); $i++): ?>
   <div class="slide bx-clone" style="float: left; list-style: none; position: relative; width: 800px;">
   <img src="<?php echo $slides[$i]->image; ?>" alt="">
   </div>
   <?php endfor; ?>
   </div></div><div class="bx-controls bx-has-pager bx-has-controls-direction"><div class="bx-pager bx-default-pager"><div class="bx-pager-item"><a href="" data-slide-index="0" class="bx-pager-link">1</a></div><div class="bx-pager-item"><a href="" data-slide-index="1" class="bx-pager-link">2</a></div><div class="bx-pager-item"><a href="" data-slide-index="2" class="bx-pager-link">3</a></div><div class="bx-pager-item"><a href="" data-slide-index="3" class="bx-pager-link active">4</a></div></div><div class="bx-controls-direction"><a class="bx-prev" href="">Prev</a><a class="bx-next" href="">Next</a></div></div></div>

</div>