<?php
/**
 * Jingga
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

namespace Modules\Organization\Controller;

use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeType;
use Modules\Attribute\Models\AttributeValue;
use Modules\Organization\Models\Attribute\AddressAttributeMapper;
use Modules\Organization\Models\Attribute\AddressAttributeTypeL11nMapper;
use Modules\Organization\Models\Attribute\AddressAttributeTypeMapper;
use Modules\Organization\Models\Attribute\AddressAttributeValueL11nMapper;
use Modules\Organization\Models\Attribute\AddressAttributeValueMapper;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;

/**
 * Organization class.
 *
 * @package Modules\Organization
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiAddressAttributeController extends Controller
{
    use \Modules\Attribute\Controller\ApiAttributeTraitController;

    /**
     * Api method to create address attribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $attribute = $this->createAttributeFromRequest($request);
        $this->createModel($request->header->account, $attribute, AddressAttributeMapper::class, 'attribute', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attribute);
    }

    /**
     * Api method to create address attribute l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $attrL11n = $this->createAttributeTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, AddressAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrL11n);
    }

    /**
     * Api method to create address attribute type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $attrType = $this->createAttributeTypeFromRequest($request);
        $this->createModel($request->header->account, $attrType, AddressAttributeTypeMapper::class, 'attr_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrType);
    }

    /**
     * Api method to create address attribute value
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeValueCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\Attribute\Models\AttributeType $type */
        $type = AddressAttributeTypeMapper::get()
            ->where('id', $request->getDataInt('type') ?? 0)
            ->execute();

        $attrValue = $this->createAttributeValueFromRequest($request, $type);
        $this->createModel($request->header->account, $attrValue, AddressAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault) {
            $this->createModelRelation(
                $request->header->account,
                (int) $request->getData('type'),
                $attrValue->id,
                AddressAttributeTypeMapper::class, 'defaults', '', $request->getOrigin()
            );
        }

        $this->createStandardCreateResponse($request, $response, $attrValue);
    }

    /**
     * Api method to create address attribute l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeValueL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $attrL11n = $this->createAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, AddressAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrL11n);
    }

    /**
     * Api method to update AddressAttribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var Attribute $old */
        $old = AddressAttributeMapper::get()
            ->with('type')
            ->with('type/defaults')
            ->with('value')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $new = $this->updateAttributeFromRequest($request, clone $old);

        if ($new->id === 0) {
            // Set response header to invalid request because of invalid data
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $new);

            return;
        }

        $this->updateModel($request->header->account, $old, $new, AddressAttributeMapper::class, 'address_attribute', $request->getOrigin());

        if ($new->value->getValue() !== $old->value->getValue()
            && $new->type->custom
        ) {
            $this->updateModel($request->header->account, $old->value, $new->value, AddressAttributeValueMapper::class, 'attribute_value', $request->getOrigin());
        }

        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to delete AddressAttribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        $addressAttribute = AddressAttributeMapper::get()
            ->with('type')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($addressAttribute->type->isRequired) {
            $this->createInvalidDeleteResponse($request, $response, []);

            return;
        }

        $this->deleteModel($request->header->account, $addressAttribute, AddressAttributeMapper::class, 'address_attribute', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $addressAttribute);
    }

    /**
     * Api method to update AddressAttributeTypeL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeL11nUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeL11nUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $old */
        $old = AddressAttributeTypeL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateAttributeTypeL11nFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, AddressAttributeTypeL11nMapper::class, 'address_attribute_type_l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to delete AddressAttributeTypeL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeL11nDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeL11nDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $addressAttributeTypeL11n */
        $addressAttributeTypeL11n = AddressAttributeTypeL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $addressAttributeTypeL11n, AddressAttributeTypeL11nMapper::class, 'address_attribute_type_l11n', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $addressAttributeTypeL11n);
    }

    /**
     * Api method to update AddressAttributeType
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var AttributeType $old */
        $old = AddressAttributeTypeMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateAttributeTypeFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, AddressAttributeTypeMapper::class, 'address_attribute_type', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to delete AddressAttributeType
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeTypeDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeTypeDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var AttributeType $addressAttributeType */
        $addressAttributeType = AddressAttributeTypeMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $addressAttributeType, AddressAttributeTypeMapper::class, 'address_attribute_type', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $addressAttributeType);
    }

    /**
     * Api method to update AddressAttributeValue
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeValueUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var AttributeValue $old */
        $old = AddressAttributeValueMapper::get()->where('id', (int) $request->getData('id'))->execute();

        /** @var \Modules\Attribute\Models\Attribute $attr */
        $attr = AddressAttributeMapper::get()
            ->with('type')
            ->where('id', $request->getDataInt('attribute') ?? 0)
            ->execute();

        $new = $this->updateAttributeValueFromRequest($request, clone $old, $attr);

        $this->updateModel($request->header->account, $old, $new, AddressAttributeValueMapper::class, 'address_attribute_value', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to delete AddressAttributeValue
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        return;
        // @todo: I don't think values can be deleted? Only Attributes
        // However, It should be possible to remove UNUSED default values
        // either here or other function?
        if (!empty($val = $this->validateAttributeValueDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\Organization\Models\AddressAttributeValue $addressAttributeValue */
        $addressAttributeValue = AddressAttributeValueMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $addressAttributeValue, AddressAttributeValueMapper::class, 'address_attribute_value', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $addressAttributeValue);
    }

    /**
     * Api method to update AddressAttributeValueL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueL11nUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeValueL11nUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $old */
        $old = AddressAttributeValueL11nMapper::get()->where('id', (int) $request->getData('id'));
        $new = $this->updateAttributeValueL11nFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, AddressAttributeValueL11nMapper::class, 'address_attribute_value_l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to delete AddressAttributeValueL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddressAttributeValueL11nDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAttributeValueL11nDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $addressAttributeValueL11n */
        $addressAttributeValueL11n = AddressAttributeValueL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $addressAttributeValueL11n, AddressAttributeValueL11nMapper::class, 'address_attribute_value_l11n', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $addressAttributeValueL11n);
    }
}
