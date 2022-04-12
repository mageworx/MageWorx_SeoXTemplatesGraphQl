<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model\Product;

class ProductTemplateRendererAdapter
{
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
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\SeoName
     */
    protected $seoNameConverter;

    public function __construct(
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaTitle $metaTitleConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaDescription $metaDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\MetaKeywords $metaKeywordsConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Product\SeoName $seoNameConverter
    ) {
        $this->metaTitleConverter       = $metaTitleConverter;
        $this->metaDescriptionConverter = $metaDescriptionConverter;
        $this->metaKeywordsConverter    = $metaKeywordsConverter;
        $this->seoNameConverter         = $seoNameConverter;
    }

    /**
     * @param string $attribute
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getRenderedValue($attribute, $product)
    {
        $attributeValue = '';

        if ($attribute === 'meta_title') {
            $attributeValue = $this->metaTitleConverter->convert($product, $product->getData($attribute), true);
        } elseif ($attribute === 'meta_description') {
            $attributeValue = $this->metaDescriptionConverter->convert($product, $product->getData($attribute), true);
        } elseif ($attribute === 'meta_keyword') {
            $attributeValue = $this->metaKeywordsConverter->convert($product, $product->getData($attribute), true);
        } elseif ($attribute === 'product_seo_name') {
            $attributeValue = $this->seoNameConverter->convert($product, $product->getData($attribute), true);
        }

        return $attributeValue;
    }
}
