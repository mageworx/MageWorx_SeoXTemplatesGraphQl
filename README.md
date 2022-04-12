# MageWorx_SeoXTemplatesGraphQl

GraphQL API module for Mageworx [Magento 2 SEO Suite Ultimate](https://www.mageworx.com/magento-2-seo-extension.html) extension. 

## Installation
**1) Copy-to-paste method**
- Download this module and upload it to the `app/code/MageWorx/SeoXTemplatesGraphQl` directory *(create "SeoXTemplatesGraphQl" first if missing)*

**2) Installation using composer (from packagist)**
- Execute the following command: `composer require mageworx/module-seoxtemplates-graph-ql`

## How to use

<details>
  <summary>Categories query example</summary>
All Category SEO-attributes (meta title, meta description, ...) set in "Bags[ - Filters: {filter_all}]"

```graphql

query GetCategories($id: String!, $pageSize: Int!, $currentPage: Int!, $filters: ProductAttributeFilterInput!, $sort: ProductAttributeSortInput) {
  categories(filters: {ids: {in: [$id]}}) {
    items {
      uid
      ...CategoryFragment
    }
  }
  products(pageSize: $pageSize, currentPage: $currentPage, filter: $filters, sort: $sort) {
    ...ProductsFragment
    mw_seo_category_data {
      meta_title
      meta_description
      meta_keywords
      category_seo_name
    }
  }
}

fragment CategoryFragment on CategoryTree {
  uid
  meta_title
  meta_keywords
  meta_description
  category_seo_name
}

fragment ProductsFragment on Products {
  items {
    id
    uid
    name
    sku
    url_key
  }
  page_info {
    total_pages
  }
  total_count
}
```

Query Variables:

```json
{
  "currentPage": 1,
  "id": "4",
  "filters": {
    "color": {
      "eq": "49"
    },
    "category_id": {
      "eq": "4"
    }
  },
  "pageSize": 1,
  "sort": {
    "position": "ASC"
  }
}
```

Answer:

```json
{
  "data": {
    "categories": {
      "items": [
        {
          "uid": "NA==",
          "meta_title": "Bags",
          "meta_keywords": "Bags",
          "meta_description": "Bags",
          "category_seo_name": "Bags"
        }
      ]
    },
    "products": {
      "items": [
        {
          "id": 7,
          "uid": "Nw==",
          "name": "Impulse Duffle",
          "sku": "24-UB02",
          "url_key": "impulse-duffle"
        }
      ],
      "page_info": {
        "total_pages": 4
      },
      "total_count": 4,
      "mw_seo_category_data": {
        "meta_title": "Bags - Filters: Color: Black",
        "meta_description": "Bags - Filters: Color: Black",
        "meta_keywords": "Bags - Filters: Color: Black",
        "category_seo_name": "Bags - Filters: Color: Black"
      }
    }
  }
}
```

</details>


<details>
  <summary>Products query example</summary>

All Product SEO-attributes (meta title, meta description, ...) set in "Wayfarer Messenger Bag[ in {categories}]"  

```graphql
query getProductDetailForProductPage($urlKey: String!) {
  products(filter: {url_key: {eq: $urlKey}}) {
    items {
      id
      uid
      ...ProductDetailsFragment
    }
  }
}

fragment ProductDetailsFragment on ProductInterface {
  categories {
    uid
    breadcrumbs {
      category_uid
    }
  }
  id
  uid
  meta_title
  meta_description
  meta_keyword
	product_seo_name
  name
  sku
  url_key
  ... on ConfigurableProduct {
    configurable_options {
      attribute_code
      attribute_id
      uid
      label
      values {
        uid
        default_label
        label
        store_label
        use_default_value
      }
    }
    variants {
      attributes {
        code
        value_index
      }
      product {
        uid
        media_gallery_entries {
          uid
          disabled
          file
          label
          position
        }
        sku
        stock_status
      }
    }
  }
}
```

Query Variables:

```json
{
  "currentPage": 1,
  "id": "4",
  "filters": {
    "color": {
      "eq": "49"
    },
    "category_id": {
      "eq": "4"
    }
  },
  "pageSize": 1,
  "sort": {
    "position": "ASC"
  }
}
```

Answer:

```json
{
  "data": {
    "products": {
      "items": [
        {
          "id": 4,
          "uid": "NA==",
          "categories": [
            {
              "uid": "Mw==",
              "breadcrumbs": null
            },
            {
              "uid": "NA==",
              "breadcrumbs": [
                {
                  "category_uid": "Mw=="
                }
              ]
            },
            {
              "uid": "Nw==",
              "breadcrumbs": null
            },
            {
              "uid": "OA==",
              "breadcrumbs": null
            }
          ],
          "meta_title": "Wayfarer Messenger Bag",
          "meta_description": "Wayfarer Messenger Bag",
          "meta_keyword": "Wayfarer Messenger Bag",
          "product_seo_name": "Wayfarer Messenger Bag",
          "name": "Wayfarer Messenger Bag",
          "sku": "24-MB05",
          "url_key": "wayfarer-messenger-bag"
        }
      ]
    }
  }
}

```

</details>
