<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Model\LayeredFiltersProvider;

class GraphQl implements \MageWorx\SeoXTemplates\Model\LayeredFiltersProviderInterface
{
    /**
     * @var \MageWorx\SeoXTemplatesGraphQl\Model\RequestedFilterArgsStorage
     */
    protected $requestedFilterArgsStorage;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $attributeResource;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $attributeRepository;

    public function __construct(
        \MageWorx\SeoXTemplatesGraphQl\Model\RequestedFilterArgsStorage $requestedFilterArgsStorage,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResource,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    ) {
        $this->requestedFilterArgsStorage = $requestedFilterArgsStorage;
        $this->storeManager               = $storeManager;
        $this->attributeResource          = $attributeResource;
        $this->attributeRepository        = $attributeRepository;
    }

    public function getCurrentLayeredFilters(): array
    {
        $attributes = $this->requestedFilterArgsStorage->get();

        $filterData = [];

        foreach ($attributes as $attributeCode => $rule) {
            if ('category_id' === $attributeCode) {
                continue;
            }

            if (!empty($rule['eq'])) {
                $requestedOption = $rule['eq'];

                try {
                    $attribute = $this->attributeRepository->get($attributeCode);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    continue;
                }

                $attribute->setData('store_id', $this->storeManager->getStore()->getId());
                $attributeOptions = $attribute->getOptions();

                foreach ($attributeOptions as $option) {

                    if ($option['value'] == $requestedOption) {
                        $filterData[] = [
                            'name'  => $this->getAttributeLabel($attribute),
                            'label' => $this->getAttributeOptionLabel($option),
                            'code'  => $attributeCode
                        ];
                    }
                }
            }
        }

        return $filterData;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return string|null
     */
    protected function getAttributeLabel($attribute)
    {
        foreach ($attribute->getFrontendLabels() as $label) {
            if ((int)$label->getStoreId() === (int)$this->storeManager->getStore()->getId()) {
                return $label->getLabel();
            }
        }

        return $attribute->getDefaultFrontendLabel();
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $option
     * @return string|null
     */
    protected function getAttributeOptionLabel($option)
    {
        $optionStoreLabel = $this->getOptionStoreLabel($option->getId());
        if ($optionStoreLabel) {
            return $optionStoreLabel;
        }

        return $option->getLabel();
    }

    /**
     * @param (int)$optionId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getOptionStoreLabel($optionId)
    {
        $connection = $this->attributeResource->getConnection();
        $bind       = [
            ':attribute_id' => (int)$optionId,
            ':store_id'     => (int)$this->storeManager->getStore()->getId()
        ];
        $select     = $connection->select()->from(
            $this->attributeResource->getTable('eav_attribute_label'),
            ['store_id', 'value']
        )->where(
            'attribute_id = :attribute_id'
        )->where(
            'store_id = :store_id'
        );

        return $connection->fetchRow($select, $bind);
    }
}
