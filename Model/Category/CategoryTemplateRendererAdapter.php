<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model\Category;

class CategoryTemplateRendererAdapter
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category
     */
    protected $categoryDynamicRenderer;

    public function __construct(
        \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category $categoryDynamicRenderer
    ) {
        $this->categoryDynamicRenderer = $categoryDynamicRenderer;
    }

    /**
     * @param string $attribute
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getRenderedValue($attribute, $category)
    {
        $attributeValue = '';

        if ('meta_title' === $attribute) {
            $this->categoryDynamicRenderer->modifyCategoryTitle($category, $attributeValue, true);
        } elseif ('meta_description' === $attribute) {
            $this->categoryDynamicRenderer->modifyCategoryMetaDescription($category, $attributeValue, true);
        } elseif ('meta_keywords' === $attribute) {
            $this->categoryDynamicRenderer->modifyCategoryMetaKeywords($category, $attributeValue, true);
        } elseif ('description' === $attribute) {
            $this->categoryDynamicRenderer->modifyCategoryDescription($category, $attributeValue, true);
        } elseif ('category_seo_name' === $attribute) {
            $attributeValue = $this->categoryDynamicRenderer->getModifiedCategorySeoName(
                $category,
                $category->getCategorySeoName(),
                true
            );
        }

        return $attributeValue;
    }
}
