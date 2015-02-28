<?php

namespace app\modules\attachment\fileapi\widgets;

use app\modules\attachment\behaviors\AttachBehavior;
use app\modules\attachment\fileapi\assets\FileAsset;
use app\modules\attachment\fileapi\Asset;
use app\modules\attachment\models\AttachmentFile;
use app\modules\attachment\Module;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use Yii;

class File extends InputWidget
{
    /**
     * @var url
     */
    public $url;

    /**
     * @var string FileAPI selector
     */
    public $selector;

    /**
     * @var array
     */
    protected $defaultSettings = [
        'autoUpload' => false
    ];

    /**
     * Widget settings.
     *
     * @var array {@link https://github.com/RubaXa/jquery.fileapi/ FileAPI options}
     */
    public $settings = [];

    /**
     * @var string Widget template view
     *
     * @see \yii\base\Widget::render
     */
    public $template;

    protected $_attachments = [];

    protected $_multiple;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->settings = ArrayHelper::merge($this->defaultSettings, $this->settings);

        $this->checkBehavior();

        $this->checkMultiple();

        $this->setupCsrf();

        $this->setupUrl();

        $this->setupAttachments();

        $this->setupTemplate();
    }

    public function setupCsrf()
    {
        $request = Yii::$app->getRequest();

        if ($request->enableCsrfValidation === true) {
            $this->settings['data'][$request->csrfParam] = $request->getCsrfToken();
        }
    }

    public function setupUrl()
    {
        $request = Yii::$app->getRequest();

        if (!isset($this->settings['url']) ) {
            $this->settings['url'] = $this->url ? Url::to($this->url) : $request->getUrl();
        } else {
            $this->settings['url'] = Url::to($this->settings['url']);
        }
    }

    public function setupAttachments()
    {
        $related = $this->getModelAttributeValue();

        $this->_attachments = is_array($related) ? $related : [$related];
    }

    public function setupTemplate()
    {
        if ($this->template === null) {
            $this->template = $this->isMultiple() ? 'file_multiple' : 'file_single';
        }
    }

    public function checkBehavior()
    {
        /** @var AttachBehavior $behavior */
        $behavior = $this->getAttachBehavior();
        $class = AttachBehavior::className();
        $name = AttachBehavior::NAME;

        if (!$behavior) {
            throw new InvalidConfigException("Behavior '{$class}' with name '{$name}' does not exists in model");
        }
    }

    public function checkMultiple()
    {
        $config = $this->getModelAttachmentConfig();

        if ($config) {
            $this->setMultiple($config['multiple']);
            $this->settings['multiple'] = $config['multiple'];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerFiles();
        $this->register();

        $data = [
            'selector' => $this->getSelector(),
            'settings' => $this->settings,
            'paramName' => Module::PARAM_NAME,
            'value' => $this->value,
            'inputName' => $this->getHiddenInputName()
        ];

        return $this->render($this->template, $data);
    }

    /**
     * Registering already uploaded files.
     */
    public function registerFiles()
    {
        foreach ($this->_attachments as $attach) if ($attach) {
            $this->settings['files'][] = [
                'id' => $attach->id,
                'src' => $attach->fileUrl,
                'name' => $attach->name,
                'type' => $attach->mime
            ];
        }
    }

    /**
     * Register all widget scripts and callbacks
     */
    public function register()
    {
        $this->registerMainClientScript();
        $this->registerClientScript();
    }

    /**
     * Register widget main asset.
     */
    protected function registerMainClientScript()
    {
        $view = $this->getView();

        Asset::register($view);
    }

    /**
     * Register widget asset.
     */
    public function registerClientScript()
    {
        $view = $this->getView();

        FileAsset::register($view);

        $selector = $this->getSelector();
        $options = Json::encode($this->settings);

        $view->registerJs("jQuery('#$selector').yiiAttachmentFileAPI('file', $options);");
    }

    /**
     * @return bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * @param $value
     */
    public function setMultiple($value)
    {
        $this->_multiple = $value;
        $this->settings['multiple'] = $value;
    }

    /**
     * @return string Widget selector
     */
    public function getSelector()
    {
        return $this->selector !== null ? $this->selector : 'attachment-' . $this->options['id'];
    }

    /**
     * @return mixed
     */
    protected function getModelAttributeValue()
    {
        return $this->model->{$this->attribute};
    }

    /**
     * @return array
     */
    protected function getModelAttachmentConfig()
    {
        return $this->getAttachBehavior()->getAttachConfig($this->attribute);
    }

    /**
     * @return AttachmentFile
     */
    protected function getModelAttachment()
    {
        return $this->getAttachBehavior()->getAttach($this->attribute);
    }

    /**
     * @return string
     */
    protected function getHiddenInputName()
    {
        return $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
    }

    /**
     * @return AttachBehavior
     */
    protected function getAttachBehavior()
    {
        return $this->model->getBehavior(AttachBehavior::NAME);
    }

    protected function getHiddenInput()
    {
        return $this->hasModel() ?
            Html::activeHiddenInput(
                $this->model,
                $this->attribute,
                $this->options
            ) :
            Html::hiddenInput(
                $this->name,
                $this->value,
                $this->options
            );
    }
}
