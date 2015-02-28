<?php
/**
 * @var string $selector
 * @var string $paramName
 * @var string $inputName
 * @var array $settings
 */
use app\modules\attachment\Module;

?>
<div id="<?= $selector; ?>" class="attachment-widget attachment-widget__single">

    <div class="attachment-widget__progress-wrapper" data-attachment="active.show" style="display: none">
        <div class="attachment-widget__progress progress progress-success">
            <div class="attachment-widget__progress-bar"
                 role="progressbar"
                 data-attachment="progress"
                 aria-valuemin="0"
                 aria-valuemax="100"
                ></div>
        </div>
    </div>
    <div class="attachment-widget__actions" data-attachment="active.hide">
        <div class="btn btn-primary attachment-widget__button-add">
            <span data-attachment="empty.show">
                <i class="glyphicon glyphicon-folder-open"></i>
                <?= Module::t('widget', 'Browse..') ?>
            </span>
            <span data-attachment="name"></span>
            <input type="file" name="<?= $paramName ?>" class="attachment-widget__input">
        </div>
        <?php if (isset($settings['autoUpload']) && $settings['autoUpload'] == false): ?>
            <div class="btn btn-success" data-attachment="empty.hide">
                <span data-attachment="ctrl.upload"><?= Module::t('widget', 'Upload') ?></span>
            </div>
        <?php endif; ?>
        <div class="btn btn-danger" data-attachment="empty.hide" title="<?= Module::t('widget', 'Remove') ?>">
            <div data-attachment="remove">
                <span class="glyphicon glyphicon-trash"></span>
            </div>
        </div>
    </div>

    <div class="alert alert-danger" data-attachment="error" style="display:none"></div>

    <input type="hidden" name="<?= $inputName ?>" value="" data-attachment="input" />
</div>

<?php /*if ($crop === true) : ?>
    <div id="<?= $selector; ?>-crop-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">adasd</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-preview" data-attachment="crop.preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-attachment="crop.save">Save</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; */?>
