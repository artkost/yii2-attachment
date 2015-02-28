<?php

namespace app\modules\attachment\commands;

use app\modules\attachment\models\AttachmentFile;
use app\modules\attachment\models\ImageFile;
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
        $query = AttachmentFile::find()->where(['status_id' => AttachmentFile::STATUS_TEMPORARY]);

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

    /**
     * Refresh styles for images
     */
    public function actionRefreshStyles()
    {
        $query = ImageFile::find()->andWhere([
            'status_id' => AttachmentFile::STATUS_PERMANENT,
            'mime' => ['image/jpeg', 'image/png']
        ]);

        foreach ($query->batch(10) as $models) {
            /** @var ImageFile $model */
            foreach ($models as $model) {
                $url = $model->filePath;

                if ($model->saveStyles()) {
                    $this->stdout('Image styles ');
                    $this->stdout($url, Console::FG_YELLOW);
                    $this->stdout(' resaved');
                    echo PHP_EOL;
                }
            }
        }
    }

}
