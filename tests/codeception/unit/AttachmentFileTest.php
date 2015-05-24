<?php
namespace artkost\attachment\tests;

use artkost\attachment\Manager;
use artkost\attachment\models\AttachmentFile;
use Codeception\Specify;

use yii\codeception\TestCase;
use Yii;

class AttachmentFileTest extends TestCase
{
    use Specify;
    /**
     * @var \artkost\attachment\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $_FILES[Manager::PARAM_NAME] = [
            'name' => 'testfile.xls',
            'mime' => 'application/xls',
            'size' => 123,
            'tmp_name' => ''
        ];
    }

    protected function _after()
    {
    }

    // tests
    public function testValidation()
    {
        /**
         * @property integer $id
         * @property integer $user_id
         * @property string $name
         * @property string $uri
         * @property string $mime
         * @property integer $size
         * @property string $type
         * @property integer $status_id
         * @property integer $created_at
         * @property integer $updated_at
         */

        /** @var $model */
        $model = new AttachmentFile();

        $this->specify('name is required', function() use($model) {
            $model->name = null;
            $this->assertFalse($model->validate(['name']));
        });
    }

}
