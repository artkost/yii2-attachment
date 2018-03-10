<?php

namespace artkost\yii2\attachment\commands;

use artkost\yii2\attachment\models\AttachmentFile;
use artkost\yii2\attachment\models\ImageFile;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Attachment console controller.
 */
class ManagerController extends Controller
{
    /**
     * Clear temporary files
     */
    public function actionClear()
    {
        $query = AttachmentFile::find()->temporary();

        foreach ($query->batch(10) as $models) {
            /** @var AttachmentFile $model */
            foreach ($models as $model) {
                $url = $model->filePath;

                if ($model->delete()) {
                    $this->stdout('File ');
                    $this->stdout($url, Console::FG_YELLOW);
                    $this->stdout(' deleted');
                    echo PHP_EOL;
                }
            }
        }
    }
}
