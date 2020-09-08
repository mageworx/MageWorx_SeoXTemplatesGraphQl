<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use GraphQL\Language\AST\FieldNode;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\CatalogGraphQl\Model\AttributesJoiner;

class AddSeoNameToCategoryCollectionPlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * AddSeoNameToCategoryCollectionPlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Adds "category_seo_name" attribute to category collection if needed
     *
     * @param AttributesJoiner $subject
     * @param $result
     * @param FieldNode $fieldNode
     * @param AbstractCollection $collection
     */
    public function afterJoin(
        AttributesJoiner $subject,
        $result,
        FieldNode $fieldNode,
        AbstractCollection $collection
    ): void {
        if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {
            if ($this->helperData->isUseCategorySeoName()) {
                if ($collection->isAttributeAdded('name')) {
                    $collection->addAttributeToSelect('category_seo_name');
                }
            }
        }
    }
}