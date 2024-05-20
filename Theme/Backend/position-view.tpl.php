<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Models\NullPosition;
use Modules\Organization\Models\Status;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 * @var \Modules\Organization\Models\Position;
 */
$position = $this->data['position'] ?? new NullPosition();

$isNew = $position->id === 0;

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form id="iPosition"
                method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}organization/position?{?}&csrf={$CSRF}'); ?>"
                <?= $isNew ? 'data-redirect="' . UriFactory::build('{/base}/organization/position/view') . '?id={/0/response/id}"' : ''; ?>>
                <div class="portlet-head"><?= $this->getHtml('Position'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="<?= $this->printHtml($position->name); ?>">
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
                            <option value="<?= Status::ACTIVE; ?>"<?= $position->status === Status::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Active'); ?>
                            <option value="<?= Status::INACTIVE; ?>"<?= $position->status === Status::INACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->render('position-editor'); ?>
                    </div>

                    <?= $this->getData('editor')->getData('text')->render(
                        'position-editor',
                        'description',
                        'iPosition',
                        $position->descriptionRaw,
                        $position->description
                    ); ?>
                </div>
                <div class="portlet-foot">
                    <?php if ($isNew) : ?>
                        <input id="iCreateSubmit" type="Submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php else : ?>
                        <input id="iSaveSubmit" type="Submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>
</div>

<?= $this->getData('position-selector')->getData('position-selector-popup')->render(); ?>
