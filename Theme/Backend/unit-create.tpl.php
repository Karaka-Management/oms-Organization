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

use Modules\Organization\Models\Status;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Uri\UriFactory;

$countryCodes = ISO3166TwoEnum::getConstants();
$countries    = ISO3166NameEnum::getConstants();

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<form id="fUnitCreate"
    method="put"
    action="<?= UriFactory::build('{/api}organization/unit?csrf={$CSRF}'); ?>"
    data-redirect="<?= UriFactory::build('{/base}/organization/unit/view'); ?>?id={/0/response/id}">
<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
                <div class="portlet-head"><?= $this->getHtml('Unit'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="" required>
                    </div>

                    <div class="form-group">
                        <label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <?= $this->getData('unit-selector')->render('iParent', 'parent', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select name="status" id="iStatus">
                            <option value="<?= Status::ACTIVE; ?>"><?= $this->getHtml('Active'); ?>
                            <option value="<?= Status::INACTIVE; ?>"><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->render('unit-editor'); ?>
                    </div>

                    <?= $this->getData('editor')->getData('text')->render('unit-editor', 'description', 'fUnitCreate'); ?>
                </div>
                <div class="portlet-foot">
                    <input id="iUnitCreate" name="submit" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </section>
    </div>

    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('MainAddress'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iLegalName"><?= $this->getHtml('LegalName'); ?></label>
                        <input type="text" name="legal" id="iLegalName" value="">
                    </div>

                    <div class="form-group">
                        <label for="iAddress"><?= $this->getHtml('Address'); ?></label>
                        <input type="text" name="address" id="iAddress" value="">
                    </div>

                    <div class="form-group">
                        <label for="iPostal"><?= $this->getHtml('Postal'); ?></label>
                        <input type="text" name="postal" id="iPostal" value="">
                    </div>

                    <div class="form-group">
                        <label for="iCity"><?= $this->getHtml('City'); ?></label>
                        <input type="text" name="city" id="iCity" value="">
                    </div>

                    <div class="form-group">
                        <label for="iCountry"><?= $this->getHtml('Country'); ?></label>
                        <select id="iCountry" name="country">
                            <option disabled selected><?= $this->getHtml('PleaseSelect', '0', '0'); ?>
                            <?php
                                foreach ($countryCodes as $code3 => $code2) :
                            ?>
                            <option value="<?= $this->printHtml($code2); ?>">
                                <?= $this->printHtml($countries[$code3]); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>

<?= $this->getData('unit-selector')->getData('unit-selector-popup')->render(); ?>