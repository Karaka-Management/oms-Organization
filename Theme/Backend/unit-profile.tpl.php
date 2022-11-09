<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Media\Models\NullMedia;
use Modules\Organization\Models\Status;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                $this
 * @var \Modules\Organization\Models\Unit $unit;
 */
$unit = $this->getData('unit');

echo $this->getData('nav')->render(); ?>

<form id="iUnitUploadForm" action="<?= UriFactory::build('{/api}organization/unit/image?id={?id}'); ?>" method="post"><input class="preview" data-action='[{"listener": "change", "key": 1, "action": [{"key": 1, "type": "form.submit", "selector": "#iUnitUploadForm"}]}]' id="iUnitUpload" name="unitImage" type="file" accept="image/png,image/gif,image/jpeg" style="display: none;"></form>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iUnit" action="<?= UriFactory::build('{/api}organization/unit'); ?>" method="post">
                <div class="portlet-head row middle-xs">
                    <div class="col-xs-0">
                        <a id="iUnitUploadButton" href="#upload" data-action='[{"listener": "click", "key": 1, "action": [{"key": 1, "type": "event.prevent"}, {"key": 2, "type": "dom.click", "selector": "#iUnitUpload"}]}]'>
                            <img id="preview-unitImage" class="profile-image preview"
                                alt="<?= $this->getHtml('Logo'); ?>"
                                itemprop="logo" loading="lazy"
                                src="<?=
                                $unit->image instanceof NullMedia ?
                                    UriFactory::build('Web/Backend/img/user_default_' . \mt_rand(1, 6) .'.png') :
                                    UriFactory::build('' . $unit->image->getPath()); ?>"
                            width="40x">
                        </a>
                    </div>
                    <div><?= $this->getHtml('Unit'); ?></div>
                </div>
                <div class="portlet-body">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tr><td><label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <tr><td><input type="text" name="name" id="iName" value="<?= $this->printHtml($unit->name); ?>">
                        <tr><td><label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <tr><td><?= $this->getData('unit-selector')->render('iParent', 'parent', false); ?>
                        <tr><td><label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <tr><td><select name="status" id="iStatus">
                                    <option value="<?= Status::ACTIVE; ?>"<?= $unit->getStatus() === Status::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Active'); ?>
                                    <option value="<?= Status::INACTIVE; ?>"<?= $unit->getStatus() === Status::INACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Inactive'); ?>
                                </select>
                        <tr><td><?= $this->getData('editor')->render('unit-editor'); ?>
                        <tr><td><?= $this->getData('editor')->getData('text')->render(
                            'unit-editor',
                            'description',
                            'iUnit',
                            $unit->descriptionRaw,
                            $unit->description
                        ); ?>
                    </table>
                </div>
                <div class="portlet-foot">
                    <input id="iUnitId" name="id" type="hidden" value="<?= (int) $unit->getId(); ?>">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('unit-selector')->getData('unit-selector-popup')->render(); ?>
