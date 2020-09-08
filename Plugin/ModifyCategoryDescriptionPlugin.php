<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use MageWorx\SeoXTemplates\Model\DynamicRenderer\Category as Renderer;

class ModifyCategoryDescriptionPlugin
{
    /**
     * Filter object
     *
     * @var \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category
     */
    protected $dynamicRenderer;

    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * ModifyCategoryDescriptionPlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     * @param Renderer $renderer
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData,
        Renderer $renderer
    ) {
        $this->helperData      = $helperData;
        $this->dynamicRenderer = $renderer;
    }

    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Category\CategoryHtmlAttribute $subject
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

        /** @var \Magento\Catalog\Model\Category $category */
        $category  = $value['model'];
        $fieldName = $field->getName();

        if ($this->out($category, $fieldName)) {
            return $result;
        }

        if (!$this->isCurrentEntity($category, $info)) {
            return $result;
        }

        if ($this->dynamicRenderer->modifyCategoryDescription($category)) {
            return $category->getData('description');
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param ResolveInfo $info
     * @return bool
     */
    protected function isCurrentEntity($category, $info)
    {
        $variables = $info->variableValues;

        return !empty($variables['_filter_0']['category_url_path']['eq'])
            && $variables['_filter_0']['category_url_path']['eq'] === $category->getUrlPath();
    }

    /**
     * Check if go out
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param string $fieldName
     * @return boolean
     */
    protected function out($category, $fieldName)
    {
        if (!is_object($category)) {
            return true;
        }

        if ($fieldName !== 'description') {
            return true;
        }

        return false;
    }
}