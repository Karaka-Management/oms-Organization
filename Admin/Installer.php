<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Admin;

use Modules\Organization\Models\Unit;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;

/**
 * Installer class.
 *
 * @package Modules\Organization\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

        /* Unit Attributes */
        $fileContent = \file_get_contents(__DIR__ . '/Install/unit_attributes.json');
        if ($fileContent === false) {
            return;
        }

        $attributes = \json_decode($fileContent, true);
        if (!\is_array($attributes)) {
            return;
        }

        $attrTypes  = self::createUnitAttributeTypes($app, $attributes);
        $attrValues = self::createUnitAttributeValues($app, $attrTypes, $attributes);

        /* Address Attributes */
        $fileContent = \file_get_contents(__DIR__ . '/Install/address_attributes.json');
        if ($fileContent === false) {
            return;
        }

        $attributes = \json_decode($fileContent, true);
        if (!\is_array($attributes)) {
            return;
        }

        $attrTypes  = self::createAddressAttributeTypes($app, $attributes);
        $attrValues = self::createAddressAttributeValues($app, $attrTypes, $attributes);

        self::installDefaultUnit();
    }

    /**
     * Install default attribute types
     *
     * @param ApplicationAbstract $app        Application
     * @param array               $attributes Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createUnitAttributeTypes(ApplicationAbstract $app, array $attributes) : array
    {
        /** @var array<string, array> $unitAttrType */
        $unitAttrType = [];

        /** @var \Modules\Organization\Controller\ApiAttributeController $module */
        $module = $app->moduleManager->getModuleInstance('Organization', 'ApiAttribute');

        /** @var array $attribute */
        foreach ($attributes as $attribute) {
            $response = new HttpResponse();
            $request  = new HttpRequest();

            $request->header->account = 1;
            $request->setData('name', $attribute['name'] ?? '');
            $request->setData('title', \reset($attribute['l11n']));
            $request->setData('language', \array_keys($attribute['l11n'])[0] ?? 'en');
            $request->setData('repeatable', $attribute['repeatable'] ?? false);
            $request->setData('internal', $attribute['internal'] ?? false);
            $request->setData('is_required', $attribute['is_required'] ?? false);
            $request->setData('custom', $attribute['is_custom_allowed'] ?? false);
            $request->setData('validation_pattern', $attribute['validation_pattern'] ?? '');
            $request->setData('datatype', (int) $attribute['value_type']);

            $module->apiUnitAttributeTypeCreate($request, $response);

            $responseData = $response->getData('');

            if (!\is_array($responseData)) {
                continue;
            }

            $unitAttrType[$attribute['name']] = \is_array($responseData['response'])
                ? $responseData['response']
                : $responseData['response']->toArray();

            $isFirst = true;
            foreach ($attribute['l11n'] as $language => $l11n) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $response = new HttpResponse();
                $request  = new HttpRequest();

                $request->header->account = 1;
                $request->setData('title', $l11n);
                $request->setData('language', $language);
                $request->setData('type', $unitAttrType[$attribute['name']]['id']);

                $module->apiUnitAttributeTypeL11nCreate($request, $response);
            }
        }

        return $unitAttrType;
    }

    /**
     * Create default attribute values for types
     *
     * @param ApplicationAbstract $app          Application
     * @param array               $unitAttrType Attribute types
     * @param array               $attributes   Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createUnitAttributeValues(ApplicationAbstract $app, array $unitAttrType, array $attributes) : array
    {
        /** @var array<string, array> $unitAttrValue */
        $unitAttrValue = [];

        /** @var \Modules\Organization\Controller\ApiAttributeController $module */
        $module = $app->moduleManager->getModuleInstance('Organization', 'ApiAttribute');

        foreach ($attributes as $attribute) {
            $unitAttrValue[$attribute['name']] = [];

            /** @var array $value */
            foreach ($attribute['values'] as $value) {
                $response = new HttpResponse();
                $request  = new HttpRequest();

                $request->header->account = 1;
                $request->setData('value', $value['value'] ?? '');
                $request->setData('unit', $value['unit'] ?? '');
                $request->setData('default', true); // always true since all defined values are possible default values
                $request->setData('type', $unitAttrType[$attribute['name']]['id']);

                if (isset($value['l11n']) && !empty($value['l11n'])) {
                    $request->setData('title', \reset($value['l11n']));
                    $request->setData('language', \array_keys($value['l11n'])[0] ?? 'en');
                }

                $module->apiUnitAttributeValueCreate($request, $response);

                $responseData = $response->getData('');
                if (!\is_array($responseData)) {
                    continue;
                }

                $attrValue = \is_array($responseData['response'])
                    ? $responseData['response']
                    : $responseData['response']->toArray();

                $unitAttrValue[$attribute['name']][] = $attrValue;

                $isFirst = true;
                foreach (($value['l11n'] ?? []) as $language => $l11n) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }

                    $response = new HttpResponse();
                    $request  = new HttpRequest();

                    $request->header->account = 1;
                    $request->setData('title', $l11n);
                    $request->setData('language', $language);
                    $request->setData('value', $attrValue['id']);

                    $module->apiUnitAttributeValueL11nCreate($request, $response);
                }
            }
        }

        return $unitAttrValue;
    }

    /**
     * Install default attribute types
     *
     * @param ApplicationAbstract $app        Application
     * @param array               $attributes Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createAddressAttributeTypes(ApplicationAbstract $app, array $attributes) : array
    {
        /** @var array<string, array> $addressAttrType */
        $addressAttrType = [];

        /** @var \Modules\Organization\Controller\ApiAddressAttributeController $module */
        $module = $app->moduleManager->getModuleInstance('Organization', 'ApiAddressAttribute');

        /** @var array $attribute */
        foreach ($attributes as $attribute) {
            $response = new HttpResponse();
            $request  = new HttpRequest();

            $request->header->account = 1;
            $request->setData('name', $attribute['name'] ?? '');
            $request->setData('title', \reset($attribute['l11n']));
            $request->setData('language', \array_keys($attribute['l11n'])[0] ?? 'en');
            $request->setData('repeatable', $attribute['repeatable'] ?? false);
            $request->setData('internal', $attribute['internal'] ?? false);
            $request->setData('is_required', $attribute['is_required'] ?? false);
            $request->setData('custom', $attribute['is_custom_allowed'] ?? false);
            $request->setData('validation_pattern', $attribute['validation_pattern'] ?? '');
            $request->setData('datatype', (int) $attribute['value_type']);

            $module->apiAddressAttributeTypeCreate($request, $response);

            $responseData = $response->getData('');

            if (!\is_array($responseData)) {
                continue;
            }

            $addressAttrType[$attribute['name']] = \is_array($responseData['response'])
                ? $responseData['response']
                : $responseData['response']->toArray();

            $isFirst = true;
            foreach ($attribute['l11n'] as $language => $l11n) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $response = new HttpResponse();
                $request  = new HttpRequest();

                $request->header->account = 1;
                $request->setData('title', $l11n);
                $request->setData('language', $language);
                $request->setData('type', $addressAttrType[$attribute['name']]['id']);

                $module->apiAddressAttributeTypeL11nCreate($request, $response);
            }
        }

        return $addressAttrType;
    }

    /**
     * Create default attribute values for types
     *
     * @param ApplicationAbstract $app             Application
     * @param array               $addressAttrType Attribute types
     * @param array               $attributes      Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createAddressAttributeValues(ApplicationAbstract $app, array $addressAttrType, array $attributes) : array
    {
        /** @var array<string, array> $addressAttrValue */
        $addressAttrValue = [];

        /** @var \Modules\Organization\Controller\ApiAddressAttributeController $module */
        $module = $app->moduleManager->getModuleInstance('Organization', 'ApiAddressAttribute');

        foreach ($attributes as $attribute) {
            $addressAttrValue[$attribute['name']] = [];

            /** @var array $value */
            foreach ($attribute['values'] as $value) {
                $response = new HttpResponse();
                $request  = new HttpRequest();

                $request->header->account = 1;
                $request->setData('value', $value['value'] ?? '');
                $request->setData('unit', $value['unit'] ?? '');
                $request->setData('default', true); // always true since all defined values are possible default values
                $request->setData('type', $addressAttrType[$attribute['name']]['id']);

                if (isset($value['l11n']) && !empty($value['l11n'])) {
                    $request->setData('title', \reset($value['l11n']));
                    $request->setData('language', \array_keys($value['l11n'])[0] ?? 'en');
                }

                $module->apiAddressAttributeValueCreate($request, $response);

                $responseData = $response->getData('');
                if (!\is_array($responseData)) {
                    continue;
                }

                $attrValue = \is_array($responseData['response'])
                    ? $responseData['response']
                    : $responseData['response']->toArray();

                $addressAttrValue[$attribute['name']][] = $attrValue;

                $isFirst = true;
                foreach (($value['l11n'] ?? []) as $language => $l11n) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }

                    $response = new HttpResponse();
                    $request  = new HttpRequest();

                    $request->header->account = 1;
                    $request->setData('title', $l11n);
                    $request->setData('language', $language);
                    $request->setData('value', $attrValue['id']);

                    $module->apiAddressAttributeValueL11nCreate($request, $response);
                }
            }
        }

        return $addressAttrValue;
    }

    /**
     * Install default unit
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installDefaultUnit() : void
    {
        $unit       = new Unit();
        $unit->name = 'Jingga';

        UnitMapper::create()->execute($unit);
    }
}
