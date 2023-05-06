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

/**
 * @var \phpOMS\Views\View $this
 */
$unitTree = $this->getData('unitTree');
$depTree  = $this->getData('departmentTree');
$posTree  = $this->getData('positionTree');

function renderTree($parent, $ref, &$unitTree, &$depTree, &$posTree, $type = 'unit')
{
    $first = true;

    $tree = [];
    if ($type === 'unit') {
        $tree = &$unitTree;
    } elseif ($type === 'dep') {
        $tree = &$depTree;
    } else {
        $tree = &$posTree;
    }

    $result = '';

    foreach ($tree as $leaf) {
        if (($parent !== $leaf['obj']->parent->id) || $ref !== $leaf['ref']) {
            continue;
        }

        if ($first && $parent !== 0) {
            $result .= '<ul>';
            $first = false;
        }

        $result .= '<li><span><section class="box">' . $leaf['obj']->name . '</section>';

        if ($type === 'unit') {
            $temp = renderTree(0, $leaf['obj']->id, $unitTree, $depTree, $posTree, $type === 'unit' ? 'dep' : 'pos');

            if ($temp !== '') {
                $result .= '<ul class="tree">';
                $result .= $temp;
                $result .= '</ul>';
            }
        }

        $result .= '</span>';

        $result .= renderTree($leaf['obj']->id, $ref, $unitTree, $depTree, $posTree, $type);
        $result .= '</li>';
    }

    if (!$first) {
        $result .= '</ul>';
    }

    return $result;
}

?>

<div class="row">
    <div class="col-xs-12">
        <ul class="tree wf-100">
            <?= renderTree(0, 0, $unitTree, $depTree, $posTree, 'unit') ?>
        </ul>
    </div>
</div>