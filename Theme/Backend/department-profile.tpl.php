<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
/**
 * @var \phpOMS\Views\View $this
 */

$department = $this->getData('department');

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="box wf-100">
            <header><h1><?= $this->getHtml('Department') ?></h1></header>
            <div class="inner">
                <form>
                    <table class="layout wf-100">
                        <tr><td><label for="iName"><?= $this->getHtml('Name') ?></label>
                        <tr><td><input type="text" name="name" id="iName" value="<?= htmlspecialchars($department->getName(), ENT_COMPAT, 'utf-8'); ?>">
                        <tr><td><label for="iParent"><?= $this->getHtml('Parent') ?></label>
                        <tr><td><input type="text" name="parent" id="iParent" value="<?= htmlspecialchars($department->getParent(), ENT_COMPAT, 'utf-8'); ?>">
                        <tr><td><label for="iStatus"><?= $this->getHtml('Status') ?></label>
                        <tr><td><select name="status" id="iStatus">
                                    <option><?= $this->getHtml('Active') ?>
                                    <option><?= $this->getHtml('Inactive') ?>
                                </select>
                        <tr><td><label for="iDescription"><?= $this->getHtml('Description') ?></label>
                        <tr><td><textarea name="description" id="iDescription"><?= htmlspecialchars($department->getDescription(), ENT_COMPAT, 'utf-8'); ?></textarea>
                        <tr><td><input type="submit" value="<?= $this->getHtml('Save', 0); ?>">
                    </table>
                </form>
            </div>
        </section>
    </div>
</div>