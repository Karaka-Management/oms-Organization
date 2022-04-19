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

use Modules\Organization\Models\Status;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="fPositionCreate" method="POST" action="<?= UriFactory::build('{/api}{/rootPath}{/lang}/api/organization/position'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Position'); ?></div>
                <div class="portlet-body">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tr><td><label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <tr><td><input type="text" name="name" id="iName" placeholder="&#xf040; Karaka" required>
                        <tr><td><label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <tr><td><?= $this->getData('position-selector')->render('iParent', 'parent', false); ?>
                        <tr><td><label for="iDepartment"><?= $this->getHtml('Department'); ?></label>
                        <tr><td><?= $this->getData('department-selector')->render('iDepartment', 'department', false); ?>
                        <tr><td><label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <tr><td><select name="status" id="iStatus">
                                    <option value="<?= Status::ACTIVE; ?>"><?= $this->getHtml('Active'); ?>
                                    <option value="<?= Status::INACTIVE; ?>"><?= $this->getHtml('Inactive'); ?>
                                    </select>
                        <tr><td><?= $this->getData('editor')->render('position-editor'); ?>
                        <tr><td><?= $this->getData('editor')->getData('text')->render('position-editor', 'description', 'fPositionCreate'); ?>
                    </table>
                </div>
                <div class="portlet-foot">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('position-selector')->getData('position-selector-popup')->render(); ?>
