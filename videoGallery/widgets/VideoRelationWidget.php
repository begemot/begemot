<?php

class VideoRelationWidget extends CWidget
{
    public $entityType;
    public $entityId;

    public function run()
    {
        $this->render('videoRelationWidget', [
            'entityType' => $this->entityType,
            'entityId' => $this->entityId,
        ]);
    }
}
