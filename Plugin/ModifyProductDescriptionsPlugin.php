<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ModifyProductDescriptionsPlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription
     */
    protected $shortDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\Description
     */
    protected $descriptionConverter;

    /**
     * ModifyProductDescriptionsPlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription $shortDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\Description $descriptionConverter
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData,
        \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription $shortDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\Description $descriptionConverter
    ) {
        $this->helperData                = $helperData;
        $this->shortDescriptionConverter = $shortDescriptionConverter;
        $this->descriptionConverter      = $descriptionConverter;
    }

    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Product\ProductComplexTextAttribute $subject
     * @param string|null $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
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

        if (empty($result['html'])) {
            return $result;
        }

        if (!$this->isCurrentEntity($product, $info)) {
            return $result;
        }

        if ($fieldName === 'short_description') {
            $result['html'] = $this->shortDescriptionConverter->convert($product, $result['html'], true);
            $product->setShortDescription($result['html']);
        }

        if ($fieldName === 'description') {
            $result['html'] = $this->descriptionConverter->convert($product, $result['html'], true);
            $product->setDescription($result['html']);
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param ResolveInfo $info
     * @return bool
     */
    protected function isCurrentEntity($product, $info)
    {
        $variables = $info->variableValues;

        return !empty($variables['_filter_0']['url_key']['eq'])
            && $variables['_filter_0']['url_key']['eq'] === $product->getUrlKey();
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

        if (!in_array($fieldName, ['description', 'short_description'])) {
            return true;
        }

        return false;
    }
}