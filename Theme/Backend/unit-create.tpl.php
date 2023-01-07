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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Models\Status;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="fUnitCreate" method="put" action="<?= UriFactory::build('{/api}organization/unit'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Unit'); ?></div>
                <div class="portlet-body">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tr><td><label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <tr><td><input type="text" name="name" id="iName" placeholder="&#xf040; Karaka" required>
                        <tr><td><label for="iParent"><?= $this->getHtml('UnitLogo'); ?></label>
                        <tr><td><img id="preview-logo" alt="<?= $this->getHtml('Logo'); ?>"
                                itemprop="logo" loading="lazy" width="40x" class="preview" src="<?= UriFactory::build('Modules/Organization/Theme/Backend/img/org_default.png'); ?>" accept="image/*">
                        <tr><td><?= $this->getData('media-preview-upload')->render('fUnitCreate', 'logo', '/Modules/Organization'); ?>
                        <tr><td><label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <tr><td><?= $this->getData('unit-selector')->render('iParent', 'parent', false); ?>
                        <tr><td><label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <tr><td><select name="status" id="iStatus">
                                    <option value="<?= Status::ACTIVE; ?>"><?= $this->getHtml('Active'); ?>
                                    <option value="<?= Status::INACTIVE; ?>"><?= $this->getHtml('Inactive'); ?>
                                    </select>
                        <tr><td><?= $this->getData('editor')->render('unit-editor'); ?>
                        <tr><td><?= $this->getData('editor')->getData('text')->render('unit-editor', 'description', 'fUnitCreate'); ?>
                    </table>
                </div>
                <div class="portlet-foot">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('unit-selector')->getData('unit-selector-popup')->render(); ?>