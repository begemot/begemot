<?php

//Move this file into application.config
//for implement new configuration in any new site
return array(
    'divId' => 'pictureBox',
    'nativeFilters' => array(
        'main' => true,

    ),
    'filtersTitles' => array(
        'main' => '640 480',

    ),
    'imageFilters' => array(
        'big_watermark'=>array(
            0 => array(
                'filter' => 'WaterMark',
                'param' => array(
                    'watermark' => '/images/watermark.png',
                ),
            ),
        ),
        'main' => array(
            0 => array(
                'filter' => 'CropResize',
                'param' => array(
                    'width' => 640,
                    'height' => 480,
                ),
            ),
        ),

    ),

);
?>
