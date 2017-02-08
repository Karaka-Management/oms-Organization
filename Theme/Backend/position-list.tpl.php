<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
/**
 * @var \phpOMS\Views\View $this
 */

$footerView = new \Web\Views\Lists\PaginationView($this->app, $this->request, $this->response);
$footerView->setTemplate('/Web/Templates/Lists/Footer/PaginationBig');

$footerView->setPages(1 / 25);
$footerView->setPage(1);
$footerView->setResults(1);

$listElements = $this->getData('list:elements') ?? [];

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
            <table class="table">
                <caption><?= $this->getText('Positions'); ?></caption>
                <thead>
                <tr>
                    <td><?= $this->getText('ID', 0, 0); ?>
                    <td class="wf-100"><?= $this->getText('Name'); ?>
                    <td><?= $this->getText('Parent'); ?>
                        <tfoot>
                <tr><td colspan="3"><?= $footerView->render(); ?>
                        <tbody>
                        <?php $count = 0; foreach($listElements as $key => $value) : $count++;
                        $url = \phpOMS\Uri\UriFactory::build('/{/lang}/backend/organization/position/profile?{?}id=' . $value->getId()); ?>
                <tr>
                    <td><a href="<?= $url; ?>"><?= $value->getId(); ?></a>
                    <td><a href="<?= $url; ?>"><?= $value->getName(); ?></a>
                    <td><a href="<?= $url; ?>"><?= $value->getParent(); ?></a>
                        <?php endforeach; ?>
                        <?php if($count === 0) : ?>
                    <tr><td colspan="5" class="empty"><?= $this->getText('Empty', 0, 0); ?>
                        <?php endif; ?>
            </table>
        </div>
    </div>
</div>
