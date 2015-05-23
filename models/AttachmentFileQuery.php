<?php

namespace artkost\attachment\models;

use yii\db\ActiveQuery;

/**
 * Class AttachmentFileQuery
 * @package artkost\attachment\models\query
 */
class AttachmentFileQuery extends ActiveQuery
{

    public function temporary()
    {
        return $this->andWhere([AttachmentFile::tableName() . '.status_id' => AttachmentFile::STATUS_TEMPORARY]);
    }

    public function permanent()
    {
        return $this->andWhere([AttachmentFile::tableName() . '.status_id' => AttachmentFile::STATUS_PERMANENT]);
    }

    public function type($type)
    {
        return $this->andWhere([AttachmentFile::tableName() . '.type' => $type]);
    }
}
