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

use Modules\Organization\Models\NullDepartment;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View           $this
 * @var \Mouldes\Organization\Models $department;
 */
$department = $this->data['department'] ?? new NullDepartment();
$isNew = $department->id === 0;

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iDepartment"
                method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}organization/department?{?}&csrf={$CSRF}'); ?>"
                <?= $isNew ? 'data-redirect="' . UriFactory::build('{/base}/organization/department/view') . '?id={/0/response/id}"' : ''; ?>>
                <div class="portlet-head"><?= $this->getHtml('Department'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="<?= $this->printHtml($department->name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <?= $this->getData('department-selector')->render('iParent', 'parent', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iUnit"><?= $this->getHtml('Unit'); ?></label>
                        <?= $this->getData('unit-selector')->render('iUnit', 'unit', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select name="status" id="iStatus">
                            <option><?= $this->getHtml('Active'); ?>
                            <option><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->render('department-editor'); ?>
                    </div>

                    <?= $this->getData('editor')->getData('text')->render(
                        'department-editor',
                        'description',
                        'iDepartment',
                        $department->descriptionRaw,
                        $department->description
                    ); ?>
                </div>
                <div class="portlet-foot">
                    <input type="hidden" name="unit" value="<?= $this->data['unit'] ?? 1; ?>">
                    <?php if ($isNew) : ?>
                        <input id="iCreateSubmit" type="Submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php else : ?>
                        <input id="iSaveSubmit" type="Submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('department-selector')->getData('department-selector-popup')->render(); ?>
