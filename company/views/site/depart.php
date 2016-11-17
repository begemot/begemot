<?php
$employees = $depart->emps;
if (is_array($employees) && count($employees)>0):
    ?>
<div class="medics_panel" style=" width:560px;
                                float:left;
                               border:1px solid white;
                               background-color:#f2f1f1;
                               background-image:url(&quot;http://www.nucleomed.ru/i/back_r_line.gif&quot;);
                               background-repeat: repeat-y;
                               background-position:right;
                               margin-bottom:60px;
                               ">
    <div class="medics_panel_txt" style="  margin-right:10px;
                                              background-image:url(&quot;http://www.nucleomed.ru/i/txt.gif&quot;);
                                              height:31px;
                                              background-repeat: no-repeat;
                                              background-position:top right;
                                              ">&nbsp;
    </div>


    <div style="margin-left:20px;margin-right:20px;">
        <?php


        Yii::import('pictureBox.components.PBox');

        foreach ($employees as $emp){
        $PBox = new PBox('companyEmployee', $emp->id);
        $empImage = $PBox->getFirstImage('forDepart');

        ?>
        <div style="float:left;margin-bottom:20px;">
            <h2><a href="<?= $empImage ?>"><img
                        class="text_pic_a" title="<?= $emp->name ?>"
                        src="<?= $empImage ?>"
                        alt="<?= $emp->name ?>"></a> <strong><?= $emp->name ?></strong></h2>

            <p>&nbsp;</p>

            <p>
                <span style="font-size: small;">
                    <?=$emp->text;?><br>
<!--                    <a class="more" href="doctors/1_section_Akusherstvo-Ginekologiya.html">подробнее</a></span>-->
            </p>


            <?php


            }

            ?>

        </div>


    </div>

</div>
<?php endif;?>

<br>
<h1><?= $depart->name; ?></h1>


<?php

echo $depart->text;
?>