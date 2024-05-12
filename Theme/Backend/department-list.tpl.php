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

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                        $this
 * @var \Modules\Organization\Models\Department[] $departments
 */
$departments = $this->data['departments'] ?? [];

$previous = empty($departments)
    ? 'organization/department/list'
    : 'organization/department/list?{?}&offset=' . \reset($departments)->id . '&ptype=p';
$next = empty($departments)
    ? 'organization/department/list'
    : 'organization/department/list?{?}&id='
        . ($this->getData('hasMore') ? \end($departments)->id : $this->request->getData('id'))
        . '&ptype=n';

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Departments'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="departmentList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="departmentList-sort-1">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="departmentList-sort-2">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="departmentList-sort-3">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="departmentList-sort-4">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Parent'); ?>
                        <label for="departmentList-sort-5">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="departmentList-sort-6">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Unit'); ?>
                        <label for="departmentList-sort-7">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-7">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="departmentList-sort-8">
                            <input type="radio" name="departmentList-sort" id="departmentList-sort-8">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                <tbody>
                <?php $c = 0;
                    foreach ($departments as $key => $value) : ++$c;
                    $url = UriFactory::build('{/base}/organization/department/view?{?}&id=' . $value->id); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->id; ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a class="content" href="<?= UriFactory::build('{/base}/organization/department/view?{?}&id=' . $value->parent->id); ?>"><?= $this->printHtml($value->parent->name); ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a class="content" href="<?= UriFactory::build('{/base}/organization/unit/view?{?}&id=' . $value->unit->id); ?>"><?= $this->printHtml($value->unit->name); ?></a>
                        <?php endforeach; ?>
                <?php if ($c === 0) : ?>
                <tr>
                    <td colspan="4" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <!--
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
            -->
        </section>
    </div>
</div>