<?php

namespace app\modules\attachment\fileapi\actions;

use app\modules\attachment\behaviors\AttachBehavior;
use app\modules\attachment\models\AttachmentFile;
use app\modules\attachment\Module;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class SingleUploadAction
 * @package app\modules\attachment\fileapi\actions
 */
class UploadAction extends Action
{
    /**
     * @var ActiveRecord
     */
    public $modelClass;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" attribute must be set.');
        }

        if ($this->attribute === null) {
            throw new InvalidConfigException('The "attribute" attribute must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->getRequest()->isPost) {

            $result = [];

            $modelClass = $this->modelClass;
            /** @var ActiveRecord $model */
            $model = new $modelClass;
            /** @var AttachBehavior $behavior */
            $behavior = $model->getBehavior(AttachBehavior::NAME);
            /** @var UploadedFile $file */
            $file = Module::getUploadedFile();

            if ($behavior && $file) {
                /**
                 * @var  AttachmentFile $instance
                 */
                $instance = $behavior->getAttach($this->attribute);

                if ($instance) {
                    $instance
                        ->setFile($file)
                        ->setUser($this->getUser());

                    $status = $instance->save();

                    $result = $this->formatFile($instance, $status);
                }
            } else {
                throw new NotSupportedException('Upload for this model not supported, ensure you attach behavior');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        } else {
            throw new BadRequestHttpException('Only POST is allowed');
        }
    }

    /**
     * @param AttachmentFile $model
     * @param boolean $status
     * @return array
     */
    protected function formatFile($model, $status)
    {
        return [
            'status' => $status,
            'id' => $model->id,
            'src' => $model->fileUrl,
            'name' => $model->name,
            'size' => $model->size,
            'errors' => $model->errors
        ];
    }

    /**
     * @return null|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        return Yii::$app->user->identity;
    }

    /**
     * @return \yii\web\Request
     */
    protected function getRequest()
    {
        return Yii::$app->request;
    }
}
