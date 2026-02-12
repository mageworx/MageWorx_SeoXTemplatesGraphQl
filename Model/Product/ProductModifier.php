<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model\Product;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\Value;

/**
 * Modify product SEO params for product - crop dynamic variables
 */
class ProductModifier
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\Observer\CategoryDataModifier
     */
    protected $categoryDataModifier;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var ProductTemplateRendererAdapter
     */
    protected $templateRendererAdapter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\ShortDescription
     */
    protected $shortDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Product\Description
     */
    protected $descriptionConverter;

    public function __construct(
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \MageWorx\SeoXTemplatesGraphQl\Model\Product\ProductTemplateRendererAdapter $templateRendererAdapter,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $attributes = []
    ) {
        $this->productFactory          = $productFactory;
        $this->eventManager            = $eventManager;
        $this->templateRendererAdapter = $templateRendererAdapter;
        $this->attributes              = $attributes;
    }

    /**
     * @param mixed|Value $resolvedValue
     * @param ResolveInfo $info
     * @param array|null $args
     * @return mixed
     */
    public function modify(
        &$resolvedValue,
        ResolveInfo $info,
        ?array $args = null
    ) {
        $fieldSelection = $info->getFieldSelection(1);

        if (empty($fieldSelection['items']) || !is_array($fieldSelection['items'])) {
            return;
        }

        $requestedAttributes = array_keys($fieldSelection['items']);
        $attributes          = array_intersect($requestedAttributes, $this->attributes);

        if ($attributes) {

            foreach ($resolvedValue['items'] as $key => $productData) {

                foreach ($attributes as $attribute) {

                    if (empty($productData[$attribute])) {
                        continue;
                    }

                    $product = $this->productFactory->create();
                    $product->setData($productData);

                    $renderedValue = $this->templateRendererAdapter->getRenderedValue($attribute, $product);

                    $resolvedValue['items'][$key][$attribute] = $renderedValue;
                }
            }
        }
    }
}
