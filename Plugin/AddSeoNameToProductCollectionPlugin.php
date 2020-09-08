<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Product\CollectionProcessorInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\SearchCriteriaInterface;

class AddSeoNameToProductCollectionPlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * AddSeoNameToProductCollectionPlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param CollectionProcessorInterface $subject
     * @param Collection $result
     * @param Collection $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @param array $attributeNames
     * @return Collection
     */
    public function afterProcess(
        CollectionProcessorInterface $subject,
        Collection $result,
        Collection $collection,
        SearchCriteriaInterface $searchCriteria,
        array $attributeNames
    ) {
        if ($this->helperData->isUseProductSeoName()) {
            if ($result->isAttributeAdded('name')) {
                $result->addAttributeToSelect('product_seo_name');
            }
        }

        return $result;
    }
}