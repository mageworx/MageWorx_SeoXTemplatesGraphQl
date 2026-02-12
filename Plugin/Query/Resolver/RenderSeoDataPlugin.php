<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Plugin\Query\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\Resolver\Context;

/**
 * Plugin to modify SEO-attributes
 */
class RenderSeoDataPlugin
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\Observer\CategoryDataModifier
     */
    protected $categoryDataModifier;

    /**
     * @var \MageWorx\SeoXTemplatesGraphQl\Model\Category\Products\CategoryDataFiller
     */
    protected $categoryDataFiller;
    /**
     * @var \MageWorx\SeoXTemplatesGraphQl\Model\Product\ProductModifier
     */
    protected $productsDataModifier;

    public function __construct(
        \MageWorx\SeoXTemplatesGraphQl\Model\Category\CategoryModifier $categoryDataModifier,
        \MageWorx\SeoXTemplatesGraphQl\Model\Category\Products\CategoryDataFiller $categoryDataFiller,
        \MageWorx\SeoXTemplatesGraphQl\Model\Product\ProductModifier $productsDataModifier
    ) {
        $this->categoryDataModifier = $categoryDataModifier;
        $this->categoryDataFiller   = $categoryDataFiller;
        $this->productsDataModifier = $productsDataModifier;
    }

    /**
     * @param ResolverInterface $subject
     * @param mixed|Value $resolvedValue
     * @param Field $field
     * @param Context $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     */
    public function afterResolve(
        ResolverInterface $subject,
        $resolvedValue,
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if ($subject instanceof \Magento\CatalogGraphQl\Model\Resolver\CategoriesQuery) {
            $this->categoryDataModifier->modify($resolvedValue, $info, $args);
        } elseif ($subject instanceof \Magento\CatalogGraphQl\Model\Resolver\Products) {
            $this->categoryDataFiller->modify($resolvedValue, $info, $args);
            $this->productsDataModifier->modify($resolvedValue, $info, $args);
        }

        return $resolvedValue;
    }
}
