<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplatesGraphQl\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use MageWorx\SeoXTemplates\Model\DynamicRenderer\Category as Renderer;

class ModifyCategoryNamePlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Helper\Data
     */
    protected $helperData;

    /**
     * ModifyCategoryNamePlugin constructor.
     *
     * @param \MageWorx\SeoXTemplates\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\SeoXTemplates\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param \MageWorx\SeoAllGraphQl\Model\Resolver\Category\SeoRenderedElement $subject
     * @param string|null $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return strung
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

        $categorySeoName = $category->getData('category_seo_name');
        $category->setData('name', $categorySeoName);

        return $categorySeoName;
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

        if ($fieldName !== 'name') {
            return true;
        }

        if (!$this->helperData->isUseCategorySeoName()) {
            return true;
        }

        if (empty($category->getData('category_seo_name'))) {
            return true;
        }

        return false;
    }
}