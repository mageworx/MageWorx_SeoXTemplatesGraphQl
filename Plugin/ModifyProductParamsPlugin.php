<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ModifyProductParamsPlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle
     */
    protected $metaTitleConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription
     */
    protected $metaDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords
     */
    protected $metaKeywordsConverter;

    /**
     * ModifyProductParamsPlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle $metaTitleConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription $metaDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords $metaKeywordsConverter
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle $metaTitleConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription $metaDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords $metaKeywordsConverter
    ) {
        $this->helperData               = $helperData;
        $this->metaTitleConverter       = $metaTitleConverter;
        $this->metaDescriptionConverter = $metaDescriptionConverter;
        $this->metaKeywordsConverter    = $metaKeywordsConverter;
    }

    /**
     * @param \MageWorx\SeoAllGraphQl\Model\Resolver\Product\SeoRenderedElement $subject
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

        if (!$this->isCurrentEntity($product, $info)) {
            return $result;
        }

        if ($fieldName === 'meta_title') {
            $result = $this->metaTitleConverter->convert($product, $result, true);
            $product->setMetaTitle($result);
        }

        if ($fieldName === 'meta_description') {
            $result = $this->metaDescriptionConverter->convert($product, $result, true);
            $product->setMetaDescription($result);
        }

        if ($fieldName === 'meta_keyword') {
            $result = $this->metaKeywordsConverter->convert($product, $result, true);
            $product->setMetaKeyword($result);
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

        if (!in_array($fieldName, ['meta_title', 'meta_description', 'meta_keyword'])) {
            return true;
        }

        return false;
    }
}