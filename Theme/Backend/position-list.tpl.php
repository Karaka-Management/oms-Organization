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

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                      $this
 * @var \Modules\Organization\Models\Position[] $positions
 */
$positions = $this->getData('positions') ?? [];

$previous = empty($positions) ? 'organization/position/list' : 'organization/position/list?{?}&id=' . \reset($positions)->getId() . '&ptype=p';
$next     = empty($positions) ? 'organization/position/list' : 'organization/position/list?{?}&id=' . \end($positions)->getId() . '&ptype=n';

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Positions'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="positionList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="positionList-sort-1">
                            <input type="radio" name="positionList-sort" id="positionList-sort-1">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="positionList-sort-2">
                            <input type="radio" name="positionList-sort" id="positionList-sort-2">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="positionList-sort-3">
                            <input type="radio" name="positionList-sort" id="positionList-sort-3">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="positionList-sort-4">
                            <input type="radio" name="positionList-sort" id="positionList-sort-4">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Parent'); ?>
                        <label for="positionList-sort-5">
                            <input type="radio" name="positionList-sort" id="positionList-sort-5">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="positionList-sort-6">
                            <input type="radio" name="positionList-sort" id="positionList-sort-6">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Department'); ?>
                        <label for="positionList-sort-7">
                            <input type="radio" name="positionList-sort" id="positionList-sort-7">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="positionList-sort-8">
                            <input type="radio" name="positionList-sort" id="positionList-sort-8">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                <tbody>
                <?php $count = 0; foreach ($positions as $key => $value) : ++$count;
                $url         = \phpOMS\Uri\UriFactory::build('organization/position/profile?{?}&id=' . $value->getId()); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->getId(); ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a class="content" href="<?= UriFactory::build('organization/position/profile?{?}&id=' . $value->parent->getId()); ?>"><?= $this->printHtml($value->parent->name); ?></a>
                    <td data-label="<?= $this->getHtml('Department'); ?>"><a class="content" href="<?= UriFactory::build('organization/department/profile?{?}&id=' . $value->department->getId()); ?>"><?= $this->printHtml($value->department->name); ?></a>
                        <?php endforeach; ?>
                        <?php if ($count === 0) : ?>
                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                        <?php endif; ?>
            </table>
            </div>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>
