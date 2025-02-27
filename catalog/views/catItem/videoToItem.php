<?php

$this->renderPartial('bs5TabMenu', ['model'=>$model,'tab'=>$tab]);

$this->renderPartial('manageVideo', ['model'=>$model]);