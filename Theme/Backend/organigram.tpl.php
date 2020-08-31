<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

/**
 * @var \phpOMS\Views\View $this
 */
$unitTree = $this->getData('unitTree');
$depTree  = $this->getData('departmentTree');
$posTree  = $this->getData('positionTree');

$unitRoot = $unitTree[null][0]['children'];
// units
//      departments
//          positions
?>
<div class="row organigram">
    <?php foreach ($unitRoot as $unitEle) : ?>
        <div class="row">
        <?php while (!empty($unitEle) && $unitEle['obj'] !== null) {
            $unitTree[null][$unitEle['obj']->getParent()->getId()]['index'] = $unitTree[null][$unitEle['obj']->getParent()->getId()]['index'] + 1;
            ?>
            <?php while (!empty($unitEle)) {
                $unitId  = $unitEle['obj']->getId(); ?>
                <div class="col">
                    <div class="portlet unit"><div class="portlet-body"><?= $unitEle['obj']->getName(); ?></div></div>

                    <?php if (isset($depTree[$unitId]) && !empty($depTree[$unitId])) : ?>
                    <!-- departments -->
                    <div class="row">
                        <?php
                            $depRoot = $depTree[$unitId][0]['children'] ?? []; foreach ($depRoot as $depEle) : ?>
                            <div class="row" style="margin: 0 auto;">
                            <?php while (!empty($depEle) && $depEle['obj'] !== null) {
                                $depTree[$unitId][$depEle['obj']->getParent()->getId()]['index'] = $depTree[$unitId][$depEle['obj']->getParent()->getId()]['index'] + 1;
                                ?>
                                <?php while (!empty($depEle)) { ?>
                                    <div class="departments">
                                        <div class="portlet">
                                        <div class="portlet-head"><?= $depEle['obj']->getName(); ?></div>

                                        <!-- positions -->
                                        <div class="portlet-body">
                                        <ul>
                                            <?php
                                                $depId   = $depEle['obj']->getId();
                                                $posRoot = !isset($posTree[$depId]) ? [] : $posTree[$depId];

                                                foreach ($posRoot as $posEle) : ?>
                                                <?php while (!empty($posEle) && $posEle['obj'] !== null) {
                                                    if (isset($posTree[$depId][$posEle['obj']->getParent()->getId()])) {
                                                        // here is a bug or somewhere else... the index is not moved correctly $c is always 0
                                                        $posTree[$depId][$posEle['obj']->getParent()->getId()]['index'] = $posTree[$depId][$posEle['obj']->getParent()->getId()]['index'] + $c + 1;
                                                    }

                                                    $c = 0; while (!empty($posEle)) {  ?>
                                                            <li><?= $posEle['obj']->getName(); ?>
                                                    <?php
                                                        $posEle = [];
                                                    } // if no more children go back to parrent
                                                    if (isset($posTree[$depId][$parentPosId ?? 0])) {
                                                    $posEle = $posTree[$depId][$parentPosId]['children'][$posTree[$depId][$parentPosId]['index'] + $c + 1] ?? [];
                                                    }
                                                } ?>
                                            <?php endforeach; ?>
                                        </ul>
                                            </div>
                                            </div>

                                        <div class="row">
                                <?php
                                    // find the closest parent who has un-rendered children
                                    $toCloseDep = 0;

                                    if (empty($depEle['children'])) {
                                        $parentDep = $depEle['obj'];

                                        do {
                                            ++$toCloseDep;
                                            $parentDep   = $parentDep->getParent();
                                            $parentDepId = $parentDep->getId();
                                        } while ($parentDepId !== 0
                                            && !isset($depTree[$unitId][$parentDepId]['children'][($depTree[$unitId][$parentDepId]['index'] ?? 0) + 1])
                                        );
                                    }

                                    $depEle = $depEle['children'][0] ?? [];
                                } // if no more children go back to parrent?>
                                <?= \str_repeat('</div>', $toCloseDep * 2); ?>
                            <?php
                                $depEle = $depTree[$unitId][$parentDepId]['children'][$depTree[$unitId][$parentDepId]['index'] + 1] ?? [];
                            } ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="row">
            <?php
                // find the closest parent who has un-rendered children
                $toCloseUnit = 0;

                if (empty($unitEle['children'])) {
                    $parentUnit = $unitEle['obj'];

                    do {
                        ++$toCloseUnit;
                        $parentUnit   = $parentUnit->getParent();
                        $parentUnitId = $parentUnit->getId();
                    } while ($parentUnitId !== 0
                        && !isset($unitTree[null][$parentUnitId]['children'][($unitTree[null][$parentUnitId]['index'] ?? 0) + 1])
                    );
                }

                $unitEle = $unitEle['children'][0] ?? [];
            } // if no more children go back to parrent?>
            <?= \str_repeat('</div>', $toCloseUnit * 2); ?>
        <?php
            $unitEle = $unitTree[null][$parentUnitId]['children'][$unitTree[null][$parentUnitId]['index'] + 1] ?? [];
        } ?>
    </div>
    <?php endforeach; ?>
</div>