<?php

namespace artkost\attachment\behaviors;

use artkost\attachment\Manager;
use artkost\attachment\models\AttachmentFile;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class AttachBehavior
 * @package artkost\attachment
 * Attachment file behavior.
 *
 * @property ActiveRecord $owner
 */
class AttachBehavior extends Behavior
{
    /**
     * Are available 3 indexes:
     * - `path` Path where the file will be moved.
     * - `tempPath` Temporary path from where file will be moved.
     * - `url` Path URL where file will be saved.
     *
     * @var array Models instance config
     */
    public $models = [];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!is_array($this->models) || empty($this->models)) {
            throw new InvalidParamException('Invalid or empty models array.');
        } else {
            foreach ($this->models as $relationName => $config) {
                Manager::getInstance()->addAttachmentModel($this->owner, $relationName, $config);

                $this->checkRelationExistence($relationName);
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
     * @param $attribute
     * @return null|array
     */
    public function getAttachmentConfig($attribute)
    {
        return isset($this->models[$attribute]) ? $this->models[$attribute] : null;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    protected function getRelationValues($name)
    {
        return Html::getAttributeValue($this->owner, $name);
    }

    /**
     * @param int|array $values
     * @return bool|int
     */
    protected function deleteFiles($values)
    {
        return AttachmentFile::deleteAll(['id' => $values]);
    }

    /**
     * @param int|array $values
     * @return bool
     */
    protected function markFilesAsPermanent($values)
    {
        return AttachmentFile::updateAll([
            'status_id' => AttachmentFile::STATUS_PERMANENT
        ], ['id' => $values]);
    }

    /**
     * @param int|array $values
     * @return bool
     */
    protected function markFilesAsTemporary($values)
    {
        return AttachmentFile::updateAll([
            'status_id' => AttachmentFile::STATUS_TEMPORARY
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

            $this->models[$name]['multiple'] = $value->multiple;
        } else {
            throw new InvalidConfigException("Relation '$class::$getter' for attribute '$name' does not exists");
        }
    }

    /**
     * Helper method for define attachment relations
     * @param $class
     * @param $link
     * @param bool $status
     * @return static
     */
    public function hasOneAttachment($class, $link, $status = false)
    {
        return $this->owner->hasOne($class, $link)
            ->andWhere(['type' => $class::type(), 'status_id' => $class::STATUS_PERMANENT]);
    }

    /**
     * Helper method for define attachment relations
     * @param $class
     * @param $link
     * @param bool $status
     * @return static
     */
    public function hasManyAttachments($class, $link, $status = false)
    {
        return $this->owner->hasMany($class, $link)
            ->andWhere(['type' => $class::type(), 'status_id' => $class::STATUS_PERMANENT]);
    }

    /**
     *
     */
    public function beforeInsert()
    {
        foreach ($this->models as $relationName => $config) {
            $this->markFilesAsPermanent($this->getRelationValues($relationName));
        }
    }

    /**
     * Function will be called before updating the record.
     */
    public function beforeUpdate()
    {
        foreach ($this->models as $relationName => $config) {
            $this->markFilesAsPermanent($this->getRelationValues($relationName));
        }
    }

    /**
     * Function will be called before deleting the record.
     */
    public function beforeDelete()
    {
        foreach ($this->models as $relationName => $config) {
            $this->deleteFiles($config);
        }
    }
}
