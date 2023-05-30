<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 * @var \Modules\Organization\Models\Position;
 */
$position = $this->data['position'];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iPosition" action="<?= UriFactory::build('{/api}organization/position?{?}'); ?>" method="POST">
                <div class="portlet-head"><?= $this->getHtml('Position'); ?></div>
                <div class="portlet-body">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tr><td><label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <tr><td><input type="text" name="name" id="iName" value="<?= $this->printHtml($position->name); ?>">
                        <tr><td><label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <tr><td><?= $this->getData('position-selector')->render('iParent', 'parent', false); ?>
                        <tr><td><label for="iDepartment"><?= $this->getHtml('Department'); ?></label>
                        <tr><td><?= $this->getData('department-selector')->render('iDepartment', 'department', false); ?>
                        <tr><td><label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <tr><td><select name="status" id="iStatus">
                                    <option><?= $this->getHtml('Active'); ?>
                                    <option><?= $this->getHtml('Inactive'); ?>
                                </select>
                        <tr><td><?= $this->getData('editor')->render('position-editor'); ?>
                        <tr><td><?= $this->getData('editor')->getData('text')->render(
                            'position-editor',
                            'description',
                            'iPosition',
                            $position->descriptionRaw,
                            $position->description
                        ); ?>
                    </table>
                </div>
                <div class="portlet-foot">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('position-selector')->getData('position-selector-popup')->render(); ?>
