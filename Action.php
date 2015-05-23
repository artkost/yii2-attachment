<?php

namespace artkost\attachment;

use artkost\attachment\models\AttachmentFile;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Action is the base class for action classes that implement Attachment API.
 * @package artkost\attachment
 */
class Action extends \yii\base\Action
{
    /**
     * @var string class name of the model which will be handled by this action.
     * The model class must implement [[ActiveRecordInterface]].
     * This property must be set.
     */
    public $modelClass;

    /**
     * @var string model attribute with AttachmentFile ids
     */
    public $attribute;

    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $attribute = null) {
     *     // $attribute is the name of property containing attachment IDs.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

    protected $_modelBehavior;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }

        if ($this->attribute === null) {
            throw new InvalidConfigException(get_class($this) . '::$attribute must be set.');
        }
    }

    public function callCheckAccess()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $this->attribute);
        }
    }

    /**
     * @return AttachmentFile
     */
    public function getAttachmentModel()
    {
        return Manager::getInstance()->getAttachmentModel($this->modelClass, $this->attribute);
    }
}
