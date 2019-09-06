<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Config;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class Attributes
 * @package Emartech\Attributes\Config
 */
class Attributes
{
    const ATTRIBUTE_PRE_TAG = 'emarsys_test_';

    private $customerAttributes = [
        self::ATTRIBUTE_PRE_TAG . 'favorite_sport' => 'Favorite Sport',
        self::ATTRIBUTE_PRE_TAG . 'favorite_book'  => 'Favorite Book',
        self::ATTRIBUTE_PRE_TAG . 'favorite_movie' => 'Favorite Movie',
        self::ATTRIBUTE_PRE_TAG . 'favorite_song'  => 'Favorite Song',
        self::ATTRIBUTE_PRE_TAG . 'favorite_car'   => 'Favorite Car',
    ];

    private $customerAddressAttributes = [
        self::ATTRIBUTE_PRE_TAG . 'type_of_street'    => 'Type Of Street',
        self::ATTRIBUTE_PRE_TAG . 'type_of_residence' => 'Type Of Residence',
        self::ATTRIBUTE_PRE_TAG . 'floor'             => 'Floor',
        self::ATTRIBUTE_PRE_TAG . 'door_bell'         => 'Door Bell',
        self::ATTRIBUTE_PRE_TAG . 'building_type'     => 'Building Type',
    ];

    private $productAttributes = [
        self::ATTRIBUTE_PRE_TAG . 'fuel_type'       => 'Fuel Type',
        self::ATTRIBUTE_PRE_TAG . 'gearbox'         => 'Gearbox',
        self::ATTRIBUTE_PRE_TAG . 'number_of_seats' => 'Number Of Seats',
        self::ATTRIBUTE_PRE_TAG . 'number_of_doors' => 'Number Of Doors',
        self::ATTRIBUTE_PRE_TAG . 'vehicle_type'    => 'Vehicle Type',
    ];

    private $categoryAttributes = [
        self::ATTRIBUTE_PRE_TAG . 'facebook_code'    => 'Facebook Code',
        self::ATTRIBUTE_PRE_TAG . 'google_code'      => 'Google Code',
        self::ATTRIBUTE_PRE_TAG . 'insta_code'       => 'Insta Code',
        self::ATTRIBUTE_PRE_TAG . 'twitter_code'     => 'Twitter Code',
        self::ATTRIBUTE_PRE_TAG . 'google_plus_code' => 'Google Plus Code',
    ];

    /**
     * @return array
     */
    public function getCustomerAttributes()
    {
        return $this->customerAttributes;
    }

    /**
     * @return string
     */
    public function getCustomerEntityTypeId()
    {
        return CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER;
    }

    /**
     * @return array
     */
    public function getCustomerUsedInForms()
    {
        return ['adminhtml_customer'];
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->productAttributes;
    }

    /**
     * @return string
     */
    public function getProductEntityTypeId()
    {
        return ProductModel::ENTITY;
    }

    /**
     * @return array
     */
    public function getCategoryAttributes()
    {
        return $this->categoryAttributes;
    }

    /**
     * @return string
     */
    public function getCategoryEntityTypeId()
    {
        return CategoryModel::ENTITY;
    }

    /**
     * @return array
     */
    public function getCustomerAddressAttributes()
    {
        return $this->customerAddressAttributes;
    }

    /**
     * @return string
     */
    public function getCustomerAddressEntityTypeId()
    {
        return 'customer_address';
    }

    /**
     * @return array
     */
    public function getCustomerAddressUsedInForms()
    {
        return ['adminhtml_customer_address'];
    }
}
