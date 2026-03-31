<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\Value;

/**
 * Modify category SEO params for category - crop dynamic variables
 */
class CategoryModifier
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
     * @var CategoryTemplateRendererAdapter
     */
    protected $templateRendererAdapter;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    public function __construct(
        CollectionFactory $collectionFactory,
        \MageWorx\SeoXTemplates\Model\DynamicRenderer\Category $categoryDynamicRenderer,
        \MageWorx\SeoXTemplatesGraphQl\Model\RequestedFilterArgsStorage $requestedFilterArgsStorage,
        \MageWorx\SeoXTemplatesGraphQl\Model\Category\CategoryTemplateRendererAdapter $categoryTemplateRendererAdapter,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $attributes = []
    ) {
        $this->categoryDynamicRenderer    = $categoryDynamicRenderer;
        $this->collectionFactory          = $collectionFactory;
        $this->requestedFilterArgsStorage = $requestedFilterArgsStorage;
        $this->templateRendererAdapter    = $categoryTemplateRendererAdapter;
        $this->eventManager               = $eventManager;
        $this->attributes                 = $attributes;
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
            $this->requestedFilterArgsStorage->disable();

            $categoryIds = array_column($resolvedValue['items'], 'id');
            $collection  = $this->getCategoryCollection($categoryIds, $attributes);

            foreach ($resolvedValue['items'] as $key => $categoryData) {

                foreach ($attributes as $attribute) {

                    if (empty($categoryData[$attribute])) {
                        continue;
                    }

                    $category = $collection->getItemById($categoryData['id']);

                    if ($category) {
                        $category->setData($categoryData);
                        $renderedValue                            = $this->templateRendererAdapter->getRenderedValue(
                            $attribute,
                            $category
                        );
                        $resolvedValue['items'][$key][$attribute] = $renderedValue;
                    }
                }
            }

            $this->requestedFilterArgsStorage->enable();
        }
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

        $this->eventManager->dispatch(
            'mw_seoxtemplates_category_modifier_collection_load_before',
            ['collection' => $collection]
        );

        $collection->addAttributeToSelect($this->getTemplateVariables());

        $this->eventManager->dispatch(
            'mw_seoxtemplates_category_modifier_collection_load_after',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * @todo we need to retrieve parsed SEO-template's variables
     * @return string[]
     */
    protected function getTemplateVariables(): array
    {
        return ['name'];
    }
}
