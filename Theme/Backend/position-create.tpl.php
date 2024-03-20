<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Models\Status;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="fPositionCreate"
                method="PUT"
                action="<?= UriFactory::build('{/api}{/rootPath}{/lang}/api/organization/position'); ?>"
                autocomplete="off">
                <div class="portlet-head"><?= $this->getHtml('Position'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" required>
                    </div>

                    <div class="form-group">
                        <label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <?= $this->getData('position-selector')->render('iParent', 'parent', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iDepartment"><?= $this->getHtml('Department'); ?></label>
                        <?= $this->getData('department-selector')->render('iDepartment', 'department', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select name="status" id="iStatus">
                            <option value="<?= Status::ACTIVE; ?>"><?= $this->getHtml('Active'); ?>
                            <option value="<?= Status::INACTIVE; ?>"><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->render('position-editor'); ?>
                    </div>

                    <?= $this->getData('editor')->getData('text')->render('position-editor', 'description', 'fPositionCreate'); ?>
                </div>
                <div class="portlet-foot">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('position-selector')->getData('position-selector-popup')->render(); ?>
