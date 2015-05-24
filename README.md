# Yii2 Attachments
[![Build Status](https://travis-ci.org/artkost/yii2-attachment.svg?branch=develop)](https://travis-ci.org/artkost/yii2-attachment)
This module provide ability to attach and upload files

All uploaded files by default have  `TEMPORARY` status.
When model with attachments save yourself, all files attached to the model change their status to permanent.


# Cli Commands

`php yii attachment/manager/clear` clears all temporary files from system

# How to use

Configure `Manager` component

```php
return [
    'components' => [
        'attachment' => [
            'class' => 'artkost\attachment\Manager',
            'storageUrl' => '@web/storage',
            'storagePath' => '@webroot/storage',
            'attachmentFileTable' => '{{%attachment_file}}'
        ]
    ]
]
```

Create your own type of file
```php 
namespace app\modules\user\models;

use artkost\attachment\models\ImageFile;

class UserAvatarFile extends ImageFile
{

    //subfolder of storgae folder
    public $path = 'user/profile';

    public static function type()
    {
        return 'user_profile';
    }
}

```

Create model that have `attachment_id` field, and attach behavior to it
> ATTENTION: model can have only one instance of behavior

```php
/**
 * Class Profile
 * @package app\modules\user\models
 * User profile model.
 *
 * @property integer $user_id User ID
 * @property string $name Name
 * @property string $surname Surname
 * @property int $avatar_id Avatar //our attachment_id
 * @property boolean $sex
 *
 * @property User $user User
 * @property UserAvatarFile $avatar avatar file
 */
class UserProfile extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    public function behaviors()
    {
        return [
            'attachBehavior' => [
                'class' => AttachBehavior::className(),
                'attributes' => [
                    'avatar' => [
                        'class' => UserAvatarFile::className(),
                        'attribute' => 'avatar_id'
                    ]
                ]
            ]
        ];
    }

    public function getAvatar()
    {
        // simply helper method with predefined conditions
        return $this->hasOneAttachment(UserAvatarFile::className(), ['id' => 'avatar_id']);
    }
}
```

Currently supported only `FileAPI` upload, but you can add yours.

Add action into controller
```php
namespace app\modules\user\controllers;

use artkost\attachmentFileAPI\actions\UploadAction as FileAPIUpload;
use app\modules\user\models\UserProfile;

/**
 * Profile controller for authenticated users.
 */
class ProfileController extends Controller
{

    public function actions()
    {
        return [
            'fileapi-upload' => [
                'class' => FileAPIUpload::className(),
                'modelClass' => UserProfile::className(),
                'attribute' => 'avatar'
                //'accessCheck' => function($action) {  }
            ]
        ];
    }

}
```

in action view file you can use widget for upload files

```php
use artkost\attachmentFileAPI\widgets\File as FileAPIWidget;
?>
...
<?= $form->field($model, 'preview')->widget(
    FileAPIWidget::className(),
    [
        'url' => ['upload-preview'],
        'settings' => [
            'autoUpload' => true
        ]
    ]
)->label(false) ?>
```
