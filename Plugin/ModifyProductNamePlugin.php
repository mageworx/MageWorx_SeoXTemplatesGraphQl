<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ModifyProductNamePlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * ModifyProductNamePlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param \MageWorx\SeoAllGraphQl\Model\Resolver\Product\SeoRenderedElement $subject
     * @param string|null $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return string
     */
    public function afterResolve(
        $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$result) {
            return $result;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product   = $value['model'];
        $fieldName = $field->getName();

        if ($this->out($product, $fieldName)) {
            return $result;
        }

        $productSeoName = $product->getData('product_seo_name');
        $product->setData('name', $productSeoName);

        return $productSeoName;
    }

    /**
     * Check if go out
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $fieldName
     * @return boolean
     */
    protected function out($product, $fieldName)
    {
        if (!is_object($product)) {
            return true;
        }

        if ($fieldName !== 'name') {
            return true;
        }

        if (!$this->helperData->isUseProductSeoName()) {
            return true;
        }

        if (empty($product->getData('product_seo_name'))) {
            return true;
        }

        return false;
    }
}