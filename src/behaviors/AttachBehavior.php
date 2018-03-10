<?php

namespace artkost\attachment\behaviors;

use artkost\attachment\Manager;
use artkost\attachment\models\AttachmentFile;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\IdentityInterface;

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
            throw new InvalidParamException('AttachBehavior::models attribute is required and must be an array.');
        } else {
            foreach ($this->models as $relationName => $config) {
                Manager::getInstance()->addAttachmentModel($this->owner, $relationName, $config);
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
     * Get attachment model config by attribute
     * @param $attribute
     * @return null|array
     */
    public function getAttachmentConfig($attribute)
    {
        return isset($this->models[$attribute]) ? $this->models[$attribute] : null;
    }

    /*
     * set attachment model config by attribute for this owner
     */
    public function setAttachmentConfig($attribute, array $config)
    {
        if (isset($this->models[$attribute])) {
            $this->models[$attribute] = $config;
        }
    }

    /**
     * @param $attribute
     * @return AttachmentFile
     */
    public function getAttachmentModel($attribute)
    {
        return Manager::getInstance()->getAttachmentModel(get_class($this->owner), $attribute);
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
     * Helper method for define attachment relations
     * @param string $class
     * @param array $link
     * @param int $status
     * @return static
     */
    public function hasOneAttachment($class, $link, $status = AttachmentFile::STATUS_PERMANENT)
    {
        $class = isset($this->models[$class]) ? $this->models[$class]['class'] : $class;

        return $this->owner->hasOne($class, $link)
            ->andWhere(['type' => $class::TYPE, 'status_id' => $status]);
    }

    /**
     * Helper method for define attachment relations
     * @param string $class full class
     * @param array $link
     * @param int $status
     * @return static
     */
    public function hasManyAttachments($class, $link, $status = AttachmentFile::STATUS_PERMANENT)
    {
        $class = isset($this->models[$class]) ? $this->models[$class]['class'] : $class;

        return $this->owner->hasMany($class, $link)
            ->andWhere(['type' => $class::TYPE, 'status_id' => $status]);
    }

    /**
     * Attaches file to owner model as attribute and saves it
     * @param $attribute
     * @param IdentityInterface $user
     * @return bool
     */
    public function attachFile($attribute, IdentityInterface $user)
    {
        $file = Manager::getUploadedFile();
        $model = $this->getAttachmentModel($attribute);

        if ($model && $file) {
            $model->setFile($file)->setUser($user)->save();
        }

        return false;
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
