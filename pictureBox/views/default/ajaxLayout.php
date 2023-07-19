<?php

//echo $this;
function delFileExt($filename) {

    $filename = strrev($filename);

    for ($i = 0; $i < strlen($filename); ++$i) {
        if ($filename{$i} != '.') {
            $filename{$i} = '';
        } else {
            $filename{$i} = '';
            break;
        }
    }
    return strrev(trim($filename));
}

function pictureHtml($data, $imageNumber) {
    $output = '';

    $images = $data['images'];

    $imageCounter = 0;

    foreach ($images as $imageKey => $image) {
        $imageCounter++;

        if ($imageNumber == $imageCounter) {

            $output.= '
                         <img src="' . $image['admin'] . '?tmp=' . rand(0, 100) . '" alt=""  width="298" height="198"/>
                        <p><a href="' . $image['original'] . '?tmp=' . rand(0, 100) . '">Оригинал1</a><!--&nbsp;<span>
                            (<a href="">Удалить изображение</a>)</span>!--></p>

                <!--<div style="display:block;width:310px;height:200px;overflow:hidden;float:right;border:1px solid #898989;margin-top:30px;">
                <img  width="310" src="' . $image['original'] . '?tmp=' . rand(0, 100) . '"></div>!-->';
            break;
        }
    }
    return $output;
}

function paginationHtml($id, $elementId, $images, $activePage = 1, $config) {
    $output = '';

    $page = 0;

    $favFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/favData.php';

    $favData = null;

    if (file_exists($favFile))
        $favData = require ($favFile);


    $output.= '<ul class="pagi">';
    foreach ($images as $imageId => $image) {

        $page++;

        if ($page == $activePage) {
            $class = " link ";
        } else {
            $class = "";
        }
        if (isset($favData[$imageId])) {
            $class.= " like ";
        } else {
            $class.= "";
        }

        $output.= '<li class="' . $class . '"><a href="javaScript:;" onClick="loadPage(' . $page . ',PB_' . $config['divId'] . ',\'' . $config['divId'] . '\');">' . $page . '</a></li>';
    };
    $output.= '</ul>';


    return $output;
}

function imagesLinks($data, $imageNumber, $config, $id, $elementId) {
    $output = '';

    $images = $data['images'];

    $imageCounter = 0;

    $activeImage = null;
    //print_t($images);


    if (count($images) > 0) {

        foreach ($images as $imageKey => $image) {

            $imageCounter++;

            if ($imageNumber == $imageCounter) {
                $activeImage = $imageKey;

                break;
            }
        }

        $PartsPath = pathinfo($image['original']);


        $filename = delFileExt($image['original']);

        $linksHtml = '<ol class="nav">';

        $deleteAllHtml = '<br /><a class="del" onClick="$.ajax({url:\'/pictureBox/default/ajaxDeleteImage/id/' . $id . '/elementId/' . $elementId . '/pictureId/' . $activeImage . '\',success: function(){;loadPage(PB_' . $config['divId'] . '.pictureBoxPage-1,PB_' . $config['divId'] . ',\'' . $config['divId'] . '\')}})" href="javascript:;">Удалить изображение</a>';

        foreach ($config['imageFilters'] as $keyFilter => $filter) {

            //Ищем фильтр с width height
            $width = null;
            $height = null;
            foreach ($filter as $subfilter){
                if (isset($subfilter['param']['width'])){
                    $width = $subfilter['param']['width'];
                    $height = $subfilter['param']['height'];
                    break;
                }
            }


            if ($keyFilter!='admin'){
                if (!isset($images[$activeImage][$keyFilter])) {
                    $delMakeHtml = '
                    <li>
                    ' . $config['filtersTitles'][$keyFilter] . '
                    <span>(<a href="javaScript:;" onclick="$.ajax({url: \'/pictureBox/default/ajaxMakeFilteredImage/id/' . $id . '/elementId/' . $elementId . '/pictureId/' . $activeImage . '/filterName/' . $keyFilter . '\',success: function(){refreshPictureBox(\'' . $config['divId'] . '\',PB_' . $config['divId'] . ')}})" class="add">Создать</a>)</span>

                            </li>';
                } else {
                    $imageOrig = $filename . '.' . $PartsPath['extension'] . '?tmp=' . rand(0, 100);
                    $imageSrc = $filename  .'_'.$keyFilter. '.' . $PartsPath['extension'] . '?tmp=' . rand(0, 100);

                    if ($width!==null){
                        $resizeBtnHtml ='<a onClick="resizeData.activeImage=\''.$activeImage.'\';resizeData.catId=\''.$elementId.'\';resizeData.filterName=\''.$keyFilter.'\';setResizeImage(\''.$imageOrig.'\','.$width.','.$height.',resizeData)" data-toggle="modal" data-target="#myModal" style="width:20px;display:inline;" href="javascript:;"><span class="icon-resize-full"></span></a>' ;
                    } else {
                        $resizeBtnHtml='';
                    }

                    $delMakeHtml = '
                    <li>'.$resizeBtnHtml.'
                    <a href="' . $imageSrc . '" style="display:inline;">' . $config['filtersTitles'][$keyFilter] . '</a>
                    <span style="margin-right: 6px;">(<a href="javaScript:;" onclick="$.ajax({url: \'/pictureBox/default/ajaxDeleteFilteredImage/id/' . $id . '/elementId/' . $elementId . '/pictureId/' . $activeImage . '/filterName/' . $keyFilter . '\',success: function(){refreshPictureBox(\'' . $config['divId'] . '\',PB_' . $config['divId'] . ')}})" class="del">Удалить</a>)</span>    
                     </li>';
                }
                   $linksHtml.= $delMakeHtml;
            }


        }

        $title = isset($image['title'])?$image['title']:'';
        $alt = isset($image['alt'])?$image['alt']:'';

        $altTitlehtml = '<form>
        title:<input style="width:600px;" name="title" type="text"  value="'.$title.'"/><br/>
        alt:<input style="width:600px;" name="alt" type="text" value="'.$alt.'" />

         <a href="javascript:;" onClick="setTitleAlt(PB_'.$config['divId'].',\''.$config['divId'].'\');">Сохранить заголовок.</a><br />
    </form>';
        return $output . $linksHtml . '</ol>' . $deleteAllHtml.$altTitlehtml;
    } else {
        return 'Изображений нет.';
    }
}

function pictureFavHtml($id, $elementId, $pictureNumber, $images, $config) {
    $output = '<ul class="lib">';


    $imageCounter = 0;
    $activeImage = null;
    foreach ($images as $imageKey => $image) {
        $imageCounter++;

        if ($pictureNumber == $imageCounter) {
            $activeImage = $imageKey;
            break;
        }
    }


    $favFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/favData.php';

    if (file_exists($favFile))
        $favData = require ($favFile);

    if (isset($favData[$activeImage])) {
        $output .='
        <li><a  class="sel" href="javascript:;" onClick="$.ajax({url:\'/pictureBox/default/ajaxDelFav/id/' . $id . '/elementId/' . $elementId . '/pictureId/' . $activeImage . '\',success: function(){refreshPictureBox(\'' . $config['divId'] . '\',PB_' . $config['divId'] . ')}});">Убрать из избранного</a></li>';
    } else {
        $output .='<li><a  class="sel" href="javascript:;" onClick="$.ajax({url:\'/pictureBox/default/ajaxAddFav/id/' . $id . '/elementId/' . $elementId . '/pictureId/' . $activeImage . '\',success: function(){refreshPictureBox(\'' . $config['divId'] . '\',PB_' . $config['divId'] . ');}});">Добавить в избранное</a></li>
        ';
    }

    return $output . '</ul>';
}

function moveHtml($data, $imageNumber, $id, $elementId, $config) {
    $output = '<ul class="lib">';

    $images = $data['images'];

    $imageCounter = 0;


    foreach ($images as $imageKey => $image) {
        $imageCounter++;

        if ($imageNumber - 1 == $imageCounter) {
            $prevImageId = $imageKey;
        }
        if ($imageNumber == $imageCounter) {
            $activeImage = $imageKey;
        }
        if ($imageNumber + 1 == $imageCounter) {
            $nextImageId = $imageKey;
        }
    }

    if (isset($prevImageId)) {
        $output.= '<li><a  class="prev" href="javascript:;" onClick="$.ajax({url:\'/pictureBox/default/ajaxFlipImages/id/' . $id . '/elementId/' . $elementId . '/pictureid1/' . $prevImageId . '/pictureid2/' . $activeImage . '\',success: function(){loadPage(PB_' . $config['divId'] . '.pictureBoxPage-1,PB_' . $config['divId'] . ',\'' . $config['divId'] . '\')}});"></a></li>';
    }
    $output.= '<li style="float: left;">Переместить</li>';
    if (isset($nextImageId)) {
        $output .= '<li><a class="next" href="javascript:;" onClick="$.ajax({url:\'/pictureBox/default/ajaxFlipImages/id/' . $id . '/elementId/' . $elementId . '/pictureid1/' . $activeImage . '/pictureid2/' . $nextImageId . '\',success: function(){loadPage(PB_' . $config['divId'] . '.pictureBoxPage+1,PB_' . $config['divId'] . ',\'' . $config['divId'] . '\');}});"></a></li>';
    }

    return $output . '</ul>';
}

?>



<div class="pictureBox">
    <?php echo paginationHtml($id, $elementId, $data['images'], $imageNumber, $config); ?>
    <div class="content">
        <?php echo pictureHtml($data, $imageNumber); ?>


        <?php echo imagesLinks($data, $imageNumber, $config, $id, $elementId); ?>
        <?php echo moveHtml($data, $imageNumber, $id, $elementId, $config); ?>
        <?php echo pictureFavHtml($id, $elementId, $imageNumber, $data['images'], $config); ?>


    </div>

    <?php

    $filters = array();
     foreach ($config['nativeFilters'] as $filterName => $accepted) {
                if ($accepted) {
                    $filters[$filterName] = 1;

                }
            }

    $formData = array(
        'id'=>$id,
        'elementId'=>$elementId,
        'filters'=>$filters,
        'config'=>  serialize($config)
    );

//    $this->widget('begemot.extensions.uploadify.CUploadifyWidget',
//                array(
//                    'filePath'=>Yii::getPathOfAlias('webroot').'/files',
//                    'uploader'=>'/pictureBox/default/upload',
//                    'formDataJson'=>CJSON::encode($formData),
//                )
//            );
    ?>


        <?php
    //        if (isset($config['nativeFilters'])) {
    //            foreach ($config['nativeFilters'] as $filterName => $accepted) {
    //                if ($accepted) {
    //                    echo '< name="filters[' . $filterName . ']" type="hidden" value="1"/><br />';
    //                }
    //            }
    //        }
        ?>



</div>

<script>
    //var state = {};
    PB_<?php echo $config['divId']; ?>.pictureBoxPage = <?php echo $imageNumber; ?>;

</script>    





