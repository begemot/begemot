<?php

$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>
<h1>Анализ html-тегов сайта</h1>
<p>Делается после парсинга всех страниц сайта.</p>

<?php
Yii::import('seo.models.*');
$pageModel = SeoPages::model()->findByAttributes(['tagsCoputedFlag' => 0]);
if ($pageModel) {


    $pageId = $pageModel->id;
    $page = $pageModel->content;
    $options = array("indent" => true,
        "output-xml" => true,
        "clean" => true,
        "drop-proprietary-attributes" => true,
        "drop-font-tags" => true,
        "drop-empty-paras" => true,
        "hide-comments" => true,
        "join-classes" => true,
        "join-styles" => true,
        "show-body-only" => true);

    $tidy = new tidy();
    $str = $tidy->parseString($page, $options, 'utf8');
    $tidy->cleanRepair();


//$xmlparser = xml_parser_create();

    $xmldata = $tidy;
//
//xml_parser_set_option($xmlparser, XML_OPTION_CASE_FOLDING, 0);
//xml_parser_set_option($xmlparser, XML_OPTION_SKIP_WHITE, 1);
//xml_parse_into_struct($xmlparser,$xmldata,$values,$indexes);
//xml_parser_free($xmlparser);
//print_r($indexes);
// this note is about how to get a DOMNode's outerHTML and innerHTML.
    $dom = new DOMDocument(1, 'UTF-8');

    @$dom->loadHTML($xmldata);

    $node = $dom->getElementsByTagName('html');
    $currentNode = $node->item(0);
//print_r($htmlNode->firstChild->firstChild->nextSibling->nextSibling->nextSibling->nodeName);
//echo '!!!'.$node->count();
    $tags = [];
    $tagsCount = [];
    function nodeChildsWalk($currentNode, &$tags, &$tagsCount, $level = 0)
    {
        if ($currentNode->hasChildNodes()) {

            $nodeCurrentChild = $currentNode->firstChild;
            do {
                if ($nodeCurrentChild->nodeName == '#text') continue;
                if ($nodeCurrentChild->nodeName == '#cdata-section') continue;
                $nodeName = $nodeCurrentChild->nodeName;

                if (isset($tagsCount[$nodeName])) {
                    $tagsCount[$nodeName]++;
                } else {
                    $tagsCount[$nodeName] = 1;
                }

//            echo $nodeCurrentChild->nodeName;echo ' '.$level.' <br>';
                if ($nodeCurrentChild->hasChildNodes()) {
                    nodeChildsWalk($nodeCurrentChild, $tags, $tagsCount, $level++);
                }
            } while ($nodeCurrentChild = $nodeCurrentChild->nextSibling);

        }
    }

    nodeChildsWalk($currentNode, $tags, $tagsCount);

//print_r($tagsCount);


//foreach ($node->childNodes as $childNode){
//    $innerHTML .= $childNode->ownerDocument->saveHTML($childNode);
//}
//echo $innerHTML;

    $schema = Yii::app()->db->getSchema();
    $tables = $schema->tables;

    $columns = $tables['seo_tags']->columns;

    foreach ($tagsCount as $tagName => $value) {
        if (!isset($columns[$tagName])) {

            $sql = $schema->addColumn('seo_tags', $tagName, 'INT');
            Yii::app()->db->createCommand($sql)->execute();

        }

        $user = Yii::app()->db->createCommand()
            ->select('id')
            ->from('seo_tags')
            ->where('pageId=:pageId', [':pageId' => $pageId])
            ->queryRow();

        if (!$user) {
            Yii::app()->db->createCommand()
                ->insert('seo_tags', array(
                    'pageId' => $pageId,
                ));
        }

        Yii::app()->db->createCommand()
            ->update('seo_tags', array(
                $tagName => $value,
            ), 'pageId=:pageId', array(':pageId' => $pageId));


    }

    $pageModel->tagsCoputedFlag = 1;
    $pageModel->save();
    echo '<script>location.reload();</script>';

} else {
    echo 'Закончили!';
}
//print_r($schema);
//print_r($data['seo_tags']);
?>
