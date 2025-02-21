<?php

class VideoRelationController extends Controller
{
    public function actionIndex()
    {
        $entityType = Yii::app()->request->getQuery('entity_type');
        $entityId = Yii::app()->request->getQuery('entity_id');

        if (!$entityType || !$entityId) {
            throw new CHttpException(400, 'Не указаны entity_type или entity_id');
        }

        $relations = Yii::app()->db->createCommand()
            ->select('*')
            ->from('video_entity_relation')
            ->where('entity_type=:entityType AND entity_id=:entityId', [
                ':entityType' => $entityType,
                ':entityId' => $entityId,
            ])
            ->queryAll();

        echo CJSON::encode($relations);
    }

    public function actionCreate()
    {
        
        $postData = CJSON::decode(Yii::app()->request->rawBody, true);

        if (!isset($postData['video_id'], $postData['entity_type'], $postData['entity_id'])) {
            throw new CHttpException(400, 'Недостаточно данных');
        }

        Yii::app()->db->createCommand()
            ->insert('video_entity_relation', [
                'video_id' => $postData['video_id'],
                'entity_type' => $postData['entity_type'],
                'entity_id' => $postData['entity_id'],
            ]);

        echo CJSON::encode(['success' => true]);
    }

    public function actionDelete($id)
    {
        $entityType = Yii::app()->request->getQuery('entity_type');
        $entityId = Yii::app()->request->getQuery('entity_id');
    
        if (!$id || !$entityType || !$entityId) {
            echo CJSON::encode([
                'success' => false,
                'error' => 'Некорректные параметры',
                'id' => $id,
                'entity_type' => $entityType,
                'entity_id' => $entityId
            ]);
            Yii::app()->end();
        }
    
        $deleted = Yii::app()->db->createCommand()
            ->delete('video_entity_relation', 
                'video_id=:video_id AND entity_type=:entity_type AND entity_id=:entity_id', 
                [
                    ':video_id' => $id,
                    ':entity_type' => $entityType,
                    ':entity_id' => $entityId
                ]
            );
    
        if ($deleted) {
            echo CJSON::encode(['success' => true]);
        } else {
            echo CJSON::encode(['success' => false, 'error' => 'Связь не найдена']);
        }
    }
    
    
}
