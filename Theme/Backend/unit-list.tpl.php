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

use Modules\Media\Models\NullMedia;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                  $this
 * @var \Modules\Organization\Models\Unit[] $units
 */
$units = $this->getData('units') ?? [];

$previous = empty($units) ? 'organization/unit/list' : '{/base}/organization/unit/list?{?}&id=' . \reset($units)->id . '&ptype=p';
$next     = empty($units) ? 'organization/unit/list' : '{/base}/organization/unit/list?{?}&id=' . \end($units)->id . '&ptype=n';

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Units'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="unitList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="unitList-sort-1">
                            <input type="radio" name="unitList-sort" id="unitList-sort-1">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="unitList-sort-2">
                            <input type="radio" name="unitList-sort" id="unitList-sort-2">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Logo'); ?>
                        <label for="unitList-sort-3">
                            <input type="radio" name="unitList-sort" id="unitList-sort-3">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="unitList-sort-4">
                            <input type="radio" name="unitList-sort" id="unitList-sort-4">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="unitList-sort-5">
                            <input type="radio" name="unitList-sort" id="unitList-sort-5">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="unitList-sort-6">
                            <input type="radio" name="unitList-sort" id="unitList-sort-6">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Parent'); ?>
                        <label for="unitList-sort-7">
                            <input type="radio" name="unitList-sort" id="unitList-sort-7">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="unitList-sort-8">
                            <input type="radio" name="unitList-sort" id="unitList-sort-8">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                <tbody>
                <?php foreach ($units as $key => $value) :
                    $url = UriFactory::build('{/base}/organization/unit/profile?{?}&id=' . $value->id); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->id; ?></a>
                    <td><a href="<?= $url; ?>"><img alt="<?= $this->getHtml('IMG_alt_profile'); ?>" class="profile-image" src="<?= $value->image->id === 0 ?
                            UriFactory::build('Modules/Organization/Theme/Backend/img/org_default.png') :
                            UriFactory::build($value->image->getPath()); ?>"></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a class="content" href="<?= UriFactory::build('{/base}/organization/unit/profile?{?}&id=' . $value->parent->id); ?>"><?= $this->printHtml($value->parent->name); ?></a>
                        <?php endforeach; ?>
            </table>
            </div>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>
