<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
/**
 * @var \phpOMS\Views\View $this
 */

$footerView = new \phpOMS\Views\PaginationView($this->app, $this->request, $this->response);
$footerView->setTemplate('/Web/Templates/Lists/Footer/PaginationBig');

$footerView->setPages(1 / 25);
$footerView->setPage(1);
$footerView->setResults(1);

$listElements = $this->getData('list:elements') ?? [];

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
            <table id="positionList" class="table darkred">
                <caption><?= $this->getHtml('Positions') ?></caption>
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', 0, 0); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td class="wf-100"><?= $this->getHtml('Name') ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Parent') ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Department') ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                        <tfoot>
                <tr><td colspan="3">
                        <tbody>
                        <?php $count = 0; foreach ($listElements as $key => $value) : $count++;
                        $url = \phpOMS\Uri\UriFactory::build('/{/lang}/backend/organization/position/profile?{?}&id=' . $value->getId()); ?>
                <tr data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', 0, 0) ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getId()); ?></a>
                    <td data-label="<?= $this->getHtml('Name') ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getName()); ?></a>
                    <td data-label="<?= $this->getHtml('Parent') ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getParent()->getName()); ?></a>
                    <td data-label="<?= $this->getHtml('Department') ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getDepartment()->getName()); ?></a>
                        <?php endforeach; ?>
                        <?php if ($count === 0) : ?>
                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', 0, 0); ?>
                        <?php endif; ?>
            </table>
        </div>
    </div>
</div>
