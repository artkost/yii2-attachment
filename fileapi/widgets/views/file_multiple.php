<?php
/**
 * @var string $selector
 * @var string $paramName
 * @var string $input
 * @var array $settings
 */
use app\modules\attachment\Module;

?>
<div id="<?= $selector; ?>" class="attachment-widget attachment-widget_multiple">

    <div class="attachment-widget__dnd well" data-attachment="dnd">

        <?php /*<span class="attachment-widget__dnd-text">
            <i class="attachment-widget__dnd-icon glyphicon glyphicon-cloud"></i>
            <span class="attachment-widget__dnd-strong">Drop Files Here</span>
        </span>*/ ?>

        <div class="attachment-widget__files" data-attachment="list">
            <div class="attachment-widget__file" data-attachment="file.tpl" data-id="<%=uid%>" title="<%-name%>, <%-sizeText%>">
                <div class="attachment-widget__file-preview thumbnail">
                    <div class="attachment-widget__file-preview-image" data-attachment="file.preview"></div>

                    <?php /*<% if( /^image/.test(type) ){ %>
                    <div class="attachment-widget__file-rotate" data-attachment="file.rotate.cw">
                        <span class="glyphicon glyphicon-repeat"></span>
                    </div>
                    <% } %>*/ ?>


                    <div class="progress attachment-widget__file-progress" data-attachment="file.active.show" style="display: none">
                        <div class="progress-bar attachment-widget__file-progress-bar" data-attachment="file.progress"></div>
                    </div>
                </div>

                <div class="attachment-widget__file-meta">
                    <div>
                        <span class="attachment-widget__file-name"><%-name%></span>
                        <span class="attachment-widget__file-remove" data-attachment="file.remove" data-fileapi="file.remove">
                            <span class="glyphicon glyphicon-remove"></span>
                        </span>
                    </div>
                    <div class="attachment-widget__file-error alert alert-danger" data-attachment="file.error"></div>
                </div>

                <input class="attachment-widget__file-input"
                       data-attachment="file.input"
                       type="hidden"
                       name="<?= $inputName ?>[]"
                       value=""
                    />
            </div>
        </div>
    </div>

    <div class="attachment-widget__progress-wrapper" data-attachment="active.show" style="display: none">
        <div class="progress progress-success attachment-widget__progress">
            <div class="progress-bar attachment-widget__progress-bar"
                 role="progressbar"
                 data-attachment="progress"
                 aria-valuemin="0"
                 aria-valuemax="100"
                ></div>
        </div>
    </div>

    <div class="attachment-widget__actions" data-attachment="active.hide">

        <div class="btn attachment-widget__button-add">
            <span><i class="glyphicon glyphicon-folder-open"></i> <?= Module::t('widget', 'Add') ?></span>
            <input type="file" name="<?= $paramName ?>" class="attachment-widget__input">
        </div>

        <?php if (isset($settings['autoUpload']) && $settings['autoUpload'] == false): ?>
            <div class="btn btn-success" data-attachment="emptyQueue.hide">
                <span data-attachment="ctrl.upload"><?= Module::t('widget', 'Upload') ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
