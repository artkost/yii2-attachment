<?php

namespace app\modules\attachment\behaviors;

use app\modules\attachment\models\AttachmentFile;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class UploadBehavior
 * @package app\modules\attachment
 * Uploading file behavior.
 *
 * @property ActiveRecord $owner
 */
class AttachBehavior extends Behavior
{
    const NAME = 'attachBehavior';

    /**
     * Are available 3 indexes:
     * - `path` Path where the file will be moved.
     * - `tempPath` Temporary path from where file will be moved.
     * - `url` Path URL where file will be saved.
     *
     * @var array Attributes array
     */
    public $attributes = [];

    /**
     * Instantiated attributes
     * @var AttachmentFile[]
     */
    protected $attaches = [];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!is_array($this->attributes) || empty($this->attributes)) {
            throw new InvalidParamException('Invalid or empty attributes array.');
        } else {
            foreach ($this->attributes as $name => $config) {
                $this->attaches[$name] = Yii::createObject($config);

                $this->checkRelationExistence($name);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete'
        ];
    }

    /**
     * @return AttachmentFile[]
     */
    public function getAttaches()
    {
        return $this->attaches;
    }

    /**
     * @return array
     */
    public function getAttachConfigs()
    {
        return $this->attributes;
    }

    /**
     * Get attached instance by attribute name
     * @param $name
     * @return AttachmentFile|null
     */
    public function getAttach($name)
    {
        return isset($this->attaches[$name]) ? $this->attaches[$name] : null;
    }

    /**
     * @param $name
     * @return array
     */
    public function getAttachConfig($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : [];
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    protected function getAttributeValue($name)
    {
        return Html::getAttributeValue($this->owner, $name);
    }

    /**
     * @param $attribute
     * @return bool|int
     * @throws \Exception
     */
    protected function deleteFiles($attribute)
    {
        $values = $this->getAttributeValue($attribute);

        return AttachmentFile::deleteAll(['id' => $values]);
    }

    /**
     * @param string $attribute
     * @param int|array $values
     * @return bool
     */
    protected function markFilesAsPermanent($attribute, $values)
    {
        $config = $this->attributes[$attribute];
        /** @var AttachmentFile $class */
        $class = $config['class'];

        return AttachmentFile::updateAll([
            'status_id' => AttachmentFile::STATUS_PERMANENT
        ], ['id' => $values, 'type' => $class::type()]);
    }

    /**
     * @param string $attribute
     * @return bool
     */
    protected function markFilesAsTemporary($attribute)
    {
        $values = $this->getAttributeValue($attribute);

        return AttachmentFile::updateAll([
            'status_id' => AttachmentFile::STATUS_PERMANENT
        ], ['id' => $values]);
    }

    /**
     * Check if relation with given name exists in model
     * @param $name
     * @throws InvalidConfigException
     */
    protected function checkRelationExistence($name)
    {
        $getter = 'get' . ucfirst($name);
        $class = get_class($this->owner);

        if (method_exists($this->owner, $getter)) {
            /** @var ActiveQuery $value */
            $value = $this->owner->$getter();

            if (!($value instanceof ActiveQueryInterface)) {
                throw new InvalidConfigException("Value of relation '$getter' not valid");
            }

            $this->attributes[$name]['multiple'] = $value->multiple;
        } else {
            throw new InvalidConfigException("Relation '$class::$getter' for attribute '$name' does not exists");
        }
    }

    /**
     *
     */
    public function beforeInsert()
    {
        foreach ($this->attributes as $attribute => $config) {
            $this->markFilesAsPermanent($attribute, $this->getAttributeValue($attribute));
        }
    }

    /**
     * Function will be called before updating the record.
     */
    public function beforeUpdate()
    {
        foreach ($this->attributes as $attribute => $config) {
            $this->markFilesAsTemporary($attribute);
            $this->markFilesAsPermanent($attribute, $this->getAttributeValue($attribute));
        }
    }

    /**
     * Function will be called before deleting the record.
     */
    public function beforeDelete()
    {
        foreach ($this->attributes as $attribute => $config) {
            $this->deleteFiles($attribute, $this->getAttributeValue($attribute));
        }
    }
}
