<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Media\Models\NullMedia;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                  $this
 * @var \Modules\Organization\Models\Unit[] $units
 */
$units = $this->getData('units') ?? [];

$previous = empty($units) ? '{/prefix}organization/unit/list' : '{/prefix}organization/unit/list?{?}&id=' . \reset($units)->getId() . '&ptype=p';
$next     = empty($units) ? '{/prefix}organization/unit/list' : '{/prefix}organization/unit/list?{?}&id=' . \end($units)->getId() . '&ptype=n';

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Units'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table id="unitList" class="default">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Logo'); ?>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Parent'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                        <tbody>
                        <?php foreach ($units as $key => $value) :
                        $url = UriFactory::build('{/prefix}organization/unit/profile?{?}&id=' . $value->getId()); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getId()); ?></a>
                    <td><a href="<?= $url; ?>"><img class="profile-image" src="<?= $value->image instanceof NullMedia ?
                                    UriFactory::build('Web/Backend/img/user_default_' . \mt_rand(1, 6) .'.png') :
                                    UriFactory::build('{/prefix}' . $value->image->getPath()); ?>"></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->parent->name); ?></a>
                        <?php endforeach; ?>
            </table>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>
