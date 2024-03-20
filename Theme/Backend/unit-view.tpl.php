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

/**
 * @var \phpOMS\Views\View                $this
 * @var \Modules\Organization\Models\Unit $unit;
 */
$unit = $this->data['unit'];

$countryCodes = ISO3166TwoEnum::getConstants();
$countries    = ISO3166NameEnum::getConstants();

echo $this->data['nav']->render(); ?>

<form id="iUnitUploadForm" action="<?= UriFactory::build('{/api}organization/unit/image?id={?id}'); ?>" method="post"><input class="preview" data-action='[{"listener": "change", "key": 1, "action": [{"key": 1, "type": "form.submit", "selector": "#iUnitUploadForm"}]}]' id="iUnitUpload" name="unitImage" type="file" accept="image/png,image/gif,image/jpeg" style="display: none;"></form>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iUnit" action="<?= UriFactory::build('{/api}organization/unit'); ?>" method="post">
                <div class="portlet-head row middle-xs">
                    <div class="col-xs-0">
                        <a id="iUnitUploadButton" href="#upload" data-action='[{"listener": "click", "key": 1, "action": [{"key": 1, "type": "event.prevent"}, {"key": 2, "type": "dom.click", "selector": "#iUnitUpload"}]}]'>
                            <img id="preview-unitImage" class="profile-image preview"
                                alt="<?= $this->getHtml('Logo'); ?>"
                                itemprop="logo" loading="lazy"
                                src="<?=
                                $unit->image->id === 0 ?
                                    UriFactory::build('Modules/Organization/Theme/Backend/img/org_default.png') :
                                    UriFactory::build($unit->image->getPath()); ?>"
                            width="40x">
                        </a>
                    </div>
                    <div><?= $this->getHtml('Unit'); ?></div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="<?= $this->printHtml($unit->name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <?= $this->getData('unit-selector')->render('iParent', 'parent', false); ?>
                    </div>

                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select name="status" id="iStatus">
                            <option value="<?= Status::ACTIVE; ?>"<?= $unit->status === Status::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Active'); ?>
                            <option value="<?= Status::INACTIVE; ?>"<?= $unit->status === Status::INACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->render('unit-editor'); ?>
                    </div>

                    <?= $this->getData('editor')->getData('text')->render(
                        'unit-editor',
                        'description',
                        'iUnit',
                        $unit->descriptionRaw,
                        $unit->description
                    ); ?>
                </div>
                <div class="portlet-foot">
                    <input id="iUnitId" name="id" type="hidden" value="<?= (int) $unit->id; ?>">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>

    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('MainAddress'); ?></div>
            <form id="iUnitMainAdress" action="<?= UriFactory::build('{/api}organization/unit/address/main'); ?>" method="post">
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iLegalName"><?= $this->getHtml('LegalName'); ?></label>
                        <input type="text" name="legal" id="iLegalName" value="<?= $this->printHtml($unit->mainAddress->name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iAddress"><?= $this->getHtml('Address'); ?></label>
                        <input type="text" name="address" id="iAddress" value="<?= $this->printHtml($unit->mainAddress->address); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iPostal"><?= $this->getHtml('Postal'); ?></label>
                        <input type="text" name="postal" id="iPostal" value="<?= $this->printHtml($unit->mainAddress->postal); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iCity"><?= $this->getHtml('City'); ?></label>
                        <input type="text" name="city" id="iCity" value="<?= $this->printHtml($unit->mainAddress->city); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iCountry"><?= $this->getHtml('Country'); ?></label>
                        <select id="iCountry" name="country">
                            <?php
                            $selected = false;
                            foreach ($countryCodes as $code3 => $code2) :
                                if ($code2 === $unit->mainAddress->country) {
                                    $selected = true;
                                }
                            ?>
                            <option value="<?= $this->printHtml($code2); ?>"<?= $code2 === $unit->mainAddress->country ? ' selected' : ''; ?>>
                                <?= $this->printHtml($countries[$code3]); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="portlet-foot">
                    <input id="iUnitId" name="id" type="hidden" value="<?= (int) $unit->id; ?>">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->getData('unit-selector')->getData('unit-selector-popup')->render(); ?>
