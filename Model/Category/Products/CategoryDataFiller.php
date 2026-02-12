<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model\Category\Products;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Adds category SEO params for products - replacing dynamic variables in category SEO-attributes using products filter
 */
class CategoryDataFiller
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\Observer\CategoryDataModifier
     */
    protected $categoryDataModifier;

    /**
     * @var \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category
     */
    protected $categoryDynamicRenderer;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageWorx\SeoXTemplatesGraphQl\Model\RequestedFilterArgsStorage
     */
    protected $requestedFilterArgsStorage;

    /**
     * @var \MageWorx\SeoXTemplatesGraphQl\Model\Category\CategoryTemplateRendererAdapter
     */
    protected $categoryTemplateRendererAdapter;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $attributes;

    public function __construct(
        CollectionFactory $collectionFactory,
        \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category $categoryDynamicRenderer,
        \MageWorx\SeoXTemplatesGraphQl\Model\RequestedFilterArgsStorage $requestedFilterArgsStorage,
        \MageWorx\SeoXTemplatesGraphQl\Model\Category\CategoryTemplateRendererAdapter $categoryTemplateRendererAdapter,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $attributes = []
    ) {
        $this->categoryDynamicRenderer         = $categoryDynamicRenderer;
        $this->collectionFactory               = $collectionFactory;
        $this->requestedFilterArgsStorage      = $requestedFilterArgsStorage;
        $this->categoryTemplateRendererAdapter = $categoryTemplateRendererAdapter;
        $this->eventManager                    = $eventManager;
        $this->attributes                      = $attributes;
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

        if (!empty($fieldSelection['mw_seo_category_data'])
            && !empty($resolvedValue['categories'])
            && count($resolvedValue['categories']) === 1
            && $resolvedValue['layer_type'] === 'category'
        ) {
            $requestedAttributes = array_keys($fieldSelection['mw_seo_category_data']);

            $attributes = array_intersect($requestedAttributes, $this->attributes);

            if ($attributes) {

                $this->requestedFilterArgsStorage->set($args['filter']);
                $collection = $this->getCategoryCollection([$resolvedValue['categories'][0]], $attributes);
                /** @var \Magento\Catalog\Model\Category $category */
                $category = $collection->getFirstItem();

                if ($category->getId()) {
                    $resolvedValue['mw_seo_category_data'] = $fieldSelection['mw_seo_category_data'];

                    foreach ($attributes as $attribute) {
                        $renderedValue = $this->categoryTemplateRendererAdapter->getRenderedValue(
                            $attribute,
                            $category
                        );

                        $resolvedValue['mw_seo_category_data'][$attribute] = $renderedValue;
                    }
                }
            }
        }

        return $resolvedValue;
    }

    /**
     * Retrieve loaded category collection
     *
     * @param array $ids
     * @param array $attributes
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCategoryCollection(array $ids, array $attributes)
    {
        $collection = $this->collectionFactory->create();
        $collection->addIdFilter($ids);
        $collection->addAttributeToSelect($attributes);
        $collection->addAttributeToSelect($this->getTemplateVariables());

        $this->eventManager->dispatch(
            'mw_seoxtemplates_category_data_filler_collection_load_before',
            ['collection' => $collection]
        );

        $collection->load();

        $this->eventManager->dispatch(
            'mw_seoxtemplates_category_data_filler_collection_load_after',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * @todo we need to retrieve parsed SEO-template's variables
     * @return string[]
     */
    protected function getTemplateVariables()
    {
        return ['name'];
    }
}
