# Procurement Module — API Documentation
> Base URL: `http://localhost/api/v1/procurement`
> Auth: **Bearer Token** (Laravel Passport)
> Content-Type: `application/json`

---

## Table of Contents
1. [Authentication](#authentication)
2. [Standard Response Format](#standard-response-format)
3. [API Endpoint List (Quick Reference)](#api-endpoint-list-quick-reference)
4. [Products](#1-products)
5. [Variation Templates](#2-variation-templates)
6. [Brands](#3-brands)
7. [Units](#4-units)
8. [Categories](#5-categories)
9. [Warranties](#6-warranties)
10. [Selling Price Groups](#7-selling-price-groups)
11. [Next.js Integration Guide](#nextjs-integration-guide)

---

## Authentication

All endpoints require a Bearer token in the `Authorization` header.

```http
Authorization: Bearer {your_access_token}
```

Obtain the token by calling the Auth module login endpoint first.

---

## Standard Response Format

### Success
```json
{
  "success": true,
  "message": "Description of what happened",
  "data": { ... }
}
```

### Paginated Success
```json
{
  "success": true,
  "message": "...",
  "data": {
    "data": [ ...items... ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72,
    "meta": { "api_version": "1.0" }
  }
}
```

### Error
```json
{
  "success": false,
  "message": "Error description",
  "errors": { "field": ["Validation message"] }
}
```

### HTTP Status Codes
| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthenticated (bad/missing token) |
| 404 | Resource not found |
| 422 | Validation error |
| 500 | Server error |

---

## API Endpoint List (Quick Reference)

| # | Method | Endpoint | Description |
|---|--------|----------|-------------|
| **Products** | | | |
| 1 | GET | `/v1/procurement/products` | List products (paginated + filters) |
| 2 | POST | `/v1/procurement/products` | Create product |
| 3 | GET | `/v1/procurement/products/{id}` | Get product detail |
| 4 | PUT | `/v1/procurement/products/{id}` | Update product |
| 5 | DELETE | `/v1/procurement/products/{id}` | Delete product |
| 6 | GET | `/v1/procurement/products/{id}/stock` | Get stock per location |
| 7 | GET | `/v1/procurement/products/{id}/variations` | Get product variations |
| 8 | GET | `/v1/procurement/products/{id}/group-prices` | Get selling price group prices |
| 9 | POST | `/v1/procurement/products/{id}/group-prices` | Update selling price group prices |
| 10 | PATCH | `/v1/procurement/products/{id}/activate` | Activate a product |
| 11 | POST | `/v1/procurement/products/mass-deactivate` | Bulk deactivate products |
| 12 | POST | `/v1/procurement/products/mass-delete` | Bulk delete products |
| 13 | POST | `/v1/procurement/products/check-sku` | Check SKU uniqueness |
| **Variation Templates** | | | |
| 14 | GET | `/v1/procurement/variation-templates` | List templates |
| 15 | POST | `/v1/procurement/variation-templates` | Create template |
| 16 | GET | `/v1/procurement/variation-templates/{id}` | Get template |
| 17 | PUT | `/v1/procurement/variation-templates/{id}` | Update template |
| 18 | DELETE | `/v1/procurement/variation-templates/{id}` | Delete template |
| 19 | POST | `/v1/procurement/variation-templates/{id}/values` | Add value to template |
| 20 | DELETE | `/v1/procurement/variation-templates/{id}/values/{valueId}` | Remove value from template |
| **Brands** | | | |
| 21 | GET | `/v1/procurement/brands` | List brands |
| 22 | POST | `/v1/procurement/brands` | Create brand |
| 23 | GET | `/v1/procurement/brands/{id}` | Get brand |
| 24 | PUT | `/v1/procurement/brands/{id}` | Update brand |
| 25 | DELETE | `/v1/procurement/brands/{id}` | Delete brand |
| **Units** | | | |
| 26 | GET | `/v1/procurement/units` | List units |
| 27 | POST | `/v1/procurement/units` | Create unit |
| 28 | GET | `/v1/procurement/units/{id}` | Get unit |
| 29 | PUT | `/v1/procurement/units/{id}` | Update unit |
| 30 | DELETE | `/v1/procurement/units/{id}` | Delete unit |
| **Categories** | | | |
| 31 | GET | `/v1/procurement/categories` | List categories |
| 32 | POST | `/v1/procurement/categories` | Create category |
| 33 | GET | `/v1/procurement/categories/{id}` | Get category |
| 34 | PUT | `/v1/procurement/categories/{id}` | Update category |
| 35 | DELETE | `/v1/procurement/categories/{id}` | Delete category |
| **Warranties** | | | |
| 36 | GET | `/v1/procurement/warranties` | List warranties |
| 37 | POST | `/v1/procurement/warranties` | Create warranty |
| 38 | GET | `/v1/procurement/warranties/{id}` | Get warranty |
| 39 | PUT | `/v1/procurement/warranties/{id}` | Update warranty |
| 40 | DELETE | `/v1/procurement/warranties/{id}` | Delete warranty |
| **Selling Price Groups** | | | |
| 41 | GET | `/v1/procurement/selling-price-groups` | List price groups |
| 42 | POST | `/v1/procurement/selling-price-groups` | Create price group |
| 43 | GET | `/v1/procurement/selling-price-groups/{id}` | Get price group |
| 44 | PUT | `/v1/procurement/selling-price-groups/{id}` | Update price group |
| 45 | DELETE | `/v1/procurement/selling-price-groups/{id}` | Delete price group |
| 46 | PATCH | `/v1/procurement/selling-price-groups/{id}/toggle-active` | Toggle active state |

---

## 1. Products

### 1.1 List Products
```
GET /v1/procurement/products
```

**Query Parameters**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `page` | integer | No | 1 | Page number |
| `per_page` | integer | No | 15 | Items per page |
| `type` | string | No | — | `single` \| `variable` \| `modifier` \| `combo` |
| `category_id` | integer | No | — | Filter by category |
| `brand_id` | integer | No | — | Filter by brand |
| `location_id` | integer | No | — | Filter by business location |
| `is_inactive` | boolean | No | — | `true` = inactive only, `false` = active only |
| `search` | string | No | — | Search by name or SKU |
| `sort_by` | string | No | `created_at` | Column to sort by |
| `sort_order` | string | No | `desc` | `asc` \| `desc` |

**Response `200`**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Widget Pro",
        "sku": "WGT-001",
        "type": "single",
        "is_inactive": false,
        "not_for_selling": false,
        "enable_stock": true,
        "enable_sr_no": false,
        "alert_quantity": 5.0,
        "barcode_type": "C128",
        "tax_type": "exclusive",
        "tax": 1,
        "weight": "1.5kg",
        "product_description": "High quality widget",
        "expiry_period": null,
        "expiry_period_type": null,
        "woocommerce_disable_sync": false,
        "brand": { "id": 2, "name": "BrandX" },
        "unit": { "id": 1, "actual_name": "Piece", "short_name": "pc" },
        "category": { "id": 3, "name": "Electronics" },
        "sub_category": { "id": 7, "name": "Gadgets" },
        "warranty": { "id": 1, "name": "Standard", "duration": 12, "duration_type": "months" },
        "image_url": "http://localhost/storage/products/img.jpg",
        "locations": [1, 2],
        "custom_fields": { "product_custom_field1": "value" },
        "created_at": "2026-01-01T00:00:00.000000Z",
        "updated_at": "2026-01-15T00:00:00.000000Z"
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 45
  }
}
```

---

### 1.2 Create Product
```
POST /v1/procurement/products
```

**Request Body (Single Product)**
```json
{
  "name": "Widget Pro",
  "sku": "WGT-001",
  "type": "single",
  "unit_id": 1,
  "secondary_unit_id": null,
  "brand_id": 2,
  "category_id": 3,
  "sub_category_id": 7,
  "tax": 1,
  "tax_type": "exclusive",
  "enable_stock": true,
  "alert_quantity": 5,
  "barcode_type": "C128",
  "warranty_id": 1,
  "weight": "1.5kg",
  "product_description": "High quality widget",
  "is_inactive": false,
  "not_for_selling": false,
  "expiry_period": null,
  "expiry_period_type": null,
  "enable_sr_no": false,
  "location_ids": [1, 2],
  "product_custom_field1": "Custom Value",
  "variations": [
    {
      "sub_sku": "WGT-001-D",
      "default_purchase_price": 10.00,
      "dpp_inc_tax": 11.00,
      "profit_percent": 50,
      "default_sell_price": 15.00,
      "sell_price_inc_tax": 16.50
    }
  ]
}
```

**Request Body (Variable Product)**
```json
{
  "name": "T-Shirt",
  "sku": "TS-001",
  "type": "variable",
  "unit_id": 1,
  "product_variations": [
    {
      "name": "Color",
      "variation_template_id": 1,
      "variations": [
        {
          "name": "Red",
          "variation_value_id": 5,
          "sub_sku": "TS-001-RED",
          "default_purchase_price": 8.00,
          "dpp_inc_tax": 8.80,
          "profit_percent": 150,
          "default_sell_price": 18.00,
          "sell_price_inc_tax": 20.00
        },
        {
          "name": "Blue",
          "variation_value_id": 6,
          "sub_sku": "TS-001-BLU",
          "default_purchase_price": 8.00,
          "dpp_inc_tax": 8.80,
          "profit_percent": 150,
          "default_sell_price": 18.00,
          "sell_price_inc_tax": 20.00
        }
      ]
    }
  ]
}
```

**Field Reference**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | **Yes** | Product name |
| `sku` | string | **Yes** | Must be unique per business |
| `type` | string | **Yes** | `single` \| `variable` \| `modifier` \| `combo` |
| `unit_id` | integer | **Yes** | Primary unit of measurement |
| `secondary_unit_id` | integer | No | Secondary unit for conversion |
| `brand_id` | integer | No | Brand ID |
| `category_id` | integer | No | Category ID |
| `sub_category_id` | integer | No | Sub-category ID |
| `tax` | integer | No | Tax rate ID |
| `tax_type` | string | No | `inclusive` \| `exclusive` (default: `exclusive`) |
| `enable_stock` | boolean | No | Enable stock tracking (default: `false`) |
| `alert_quantity` | number | No | Low-stock alert threshold |
| `barcode_type` | string | No | `C39` \| `C128` \| `EAN-13` \| `EAN-8` \| `UPC-A` \| `UPC-E` \| `ITF-14` |
| `warranty_id` | integer | No | Warranty ID |
| `weight` | string | No | Product weight e.g. `"1.5kg"` |
| `product_description` | string | No | Full description |
| `is_inactive` | boolean | No | `true` = inactive (default: `false`) |
| `not_for_selling` | boolean | No | `true` = exclude from sales (default: `false`) |
| `expiry_period` | number | No | Expiry duration value |
| `expiry_period_type` | string | No | `days` \| `months` |
| `enable_sr_no` | boolean | No | Enable serial number tracking |
| `location_ids` | integer[] | No | Business location IDs |
| `product_custom_field1..10` | string | No | Custom fields |
| `variations` | array | Required for `single`/`modifier` | Array of 1 variation object |
| `product_variations` | array | Required for `variable` | Array of variation groups |

**Response `201`**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": { ...full product object... }
}
```

---

### 1.3 Get Product Detail
```
GET /v1/procurement/products/{id}
```

Returns full product with all relations loaded: `product_variations`, `variations`, `stock` per location, `group_prices`, `rack_details`, `locations`.

**Response `200`**
```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Widget Pro",
    "sku": "WGT-001",
    "type": "single",
    "brand": { "id": 2, "name": "BrandX" },
    "unit": { "id": 1, "actual_name": "Piece", "short_name": "pc" },
    "product_variations": [
      {
        "id": 1,
        "name": "DUMMY",
        "is_dummy": true,
        "variations": [
          {
            "id": 1,
            "name": "DUMMY",
            "sub_sku": "WGT-001-D",
            "default_purchase_price": 10.0,
            "dpp_inc_tax": 11.0,
            "profit_percent": 50.0,
            "default_sell_price": 15.0,
            "sell_price_inc_tax": 16.5,
            "stock": [
              { "id": 1, "location_id": 1, "qty_available": 100.0 }
            ],
            "group_prices": [
              {
                "id": 1,
                "price_group_id": 1,
                "price_group_name": "Wholesale",
                "price_inc_tax": 14.0,
                "price_type": "fixed",
                "calculated_price": 14.0
              }
            ]
          }
        ]
      }
    ],
    "locations": [1, 2],
    "rack_details": [
      { "id": 1, "location_id": 1, "rack": "A", "row": "3", "position": "5" }
    ],
    "custom_fields": { "product_custom_field1": "Custom Value" }
  }
}
```

---

### 1.4 Update Product
```
PUT /v1/procurement/products/{id}
```
or
```
PATCH /v1/procurement/products/{id}
```

All fields are optional (partial update). Same fields as Create, except `type` and `sku` are not re-validated.

**Response `200`** — Returns updated product object.

---

### 1.5 Delete Product
```
DELETE /v1/procurement/products/{id}
```

**Response `200`**
```json
{
  "success": true,
  "message": "Product deleted successfully",
  "data": null
}
```

---

### 1.6 Get Product Stock
```
GET /v1/procurement/products/{id}/stock
```

Returns stock quantities per location for all variations.

**Response `200`**
```json
{
  "success": true,
  "message": "Stock retrieved successfully",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "product_variation_id": 1,
      "variation_id": 1,
      "location_id": 1,
      "qty_available": 100.0,
      "variation": { "id": 1, "name": "DUMMY", "sub_sku": "WGT-001-D" }
    }
  ]
}
```

---

### 1.7 Get Product Variations
```
GET /v1/procurement/products/{id}/variations
```

Returns all variation rows with their stock and group prices.

**Response `200`**
```json
{
  "success": true,
  "message": "Variations retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "DUMMY",
      "sub_sku": "WGT-001-D",
      "default_purchase_price": 10.0,
      "sell_price_inc_tax": 16.5,
      "location_details": [...],
      "group_prices": [...]
    }
  ]
}
```

---

### 1.8 Get Product Group Prices
```
GET /v1/procurement/products/{id}/group-prices
```

**Response `200`**
```json
{
  "success": true,
  "message": "Group prices retrieved successfully",
  "data": [
    {
      "id": 1,
      "variation_id": 1,
      "price_group_id": 1,
      "price_group_name": "Wholesale",
      "price_inc_tax": 14.0,
      "price_type": "fixed",
      "calculated_price": 14.0
    }
  ]
}
```

---

### 1.9 Update Product Group Prices
```
POST /v1/procurement/products/{id}/group-prices
```

**Request Body**
```json
{
  "group_prices": [
    {
      "variation_id": 1,
      "price_group_id": 1,
      "price_inc_tax": 14.00,
      "price_type": "fixed"
    },
    {
      "variation_id": 1,
      "price_group_id": 2,
      "price_inc_tax": 80,
      "price_type": "percentage"
    }
  ]
}
```

> `price_type`:
> - `fixed` → customer pays `price_inc_tax` directly
> - `percentage` → customer pays `sell_price_inc_tax × (price_inc_tax / 100)`

**Response `200`**
```json
{
  "success": true,
  "message": "Group prices updated successfully",
  "data": null
}
```

---

### 1.10 Activate Product
```
PATCH /v1/procurement/products/{id}/activate
```

Sets `is_inactive = false`.

**Response `200`**
```json
{
  "success": true,
  "message": "Product activated successfully",
  "data": null
}
```

---

### 1.11 Bulk Deactivate Products
```
POST /v1/procurement/products/mass-deactivate
```

**Request Body**
```json
{
  "ids": [1, 2, 3]
}
```

**Response `200`**
```json
{
  "success": true,
  "message": "3 products deactivated",
  "data": { "affected": 3 }
}
```

---

### 1.12 Bulk Delete Products
```
POST /v1/procurement/products/mass-delete
```

**Request Body**
```json
{
  "ids": [1, 2, 3]
}
```

**Response `200`**
```json
{
  "success": true,
  "message": "3 products deleted",
  "data": { "affected": 3 }
}
```

---

### 1.13 Check SKU Uniqueness
```
POST /v1/procurement/products/check-sku
```

**Request Body**
```json
{
  "sku": "WGT-002",
  "product_id": null
}
```

> Pass `product_id` when editing an existing product to exclude it from the uniqueness check.

**Response `200`**
```json
{
  "success": true,
  "message": "SKU is available",
  "data": { "is_unique": true }
}
```

---

## 2. Variation Templates

Reusable templates for variable product attributes (e.g., Color → Red, Blue, Green).

---

### 2.1 List Variation Templates
```
GET /v1/procurement/variation-templates
```

**Query Params:** `page`, `per_page`

**Response `200`**
```json
{
  "success": true,
  "message": "Variation templates retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Color",
        "business_id": 1,
        "values": [
          { "id": 1, "name": "Red" },
          { "id": 2, "name": "Blue" },
          { "id": 3, "name": "Green" }
        ],
        "created_at": "2026-01-01T00:00:00.000000Z",
        "updated_at": "2026-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

---

### 2.2 Create Variation Template
```
POST /v1/procurement/variation-templates
```

**Request Body**
```json
{
  "name": "Color",
  "values": [
    { "name": "Red" },
    { "name": "Blue" },
    { "name": "Green" }
  ]
}
```

**Response `201`** — Returns created template with values.

---

### 2.3 Get Variation Template
```
GET /v1/procurement/variation-templates/{id}
```

**Response `200`** — Returns template object with all values.

---

### 2.4 Update Variation Template
```
PUT /v1/procurement/variation-templates/{id}
```

**Request Body**
```json
{
  "name": "Color",
  "values": [
    { "id": 1, "name": "Red" },
    { "id": 2, "name": "Dark Blue" },
    { "name": "Yellow" }
  ]
}
```

> Include `id` to update existing values. Omit `id` to add new values. Values not listed are deleted.

---

### 2.5 Delete Variation Template
```
DELETE /v1/procurement/variation-templates/{id}
```

Deletes the template and all its values.

---

### 2.6 Add Value to Template
```
POST /v1/procurement/variation-templates/{id}/values
```

**Request Body**
```json
{
  "name": "Purple"
}
```

**Response `201`**
```json
{
  "success": true,
  "message": "Value added successfully",
  "data": { "id": 4, "name": "Purple" }
}
```

---

### 2.7 Remove Value from Template
```
DELETE /v1/procurement/variation-templates/{id}/values/{valueId}
```

**Response `200`**
```json
{
  "success": true,
  "message": "Value removed successfully",
  "data": null
}
```

---

## 3. Brands

---

### 3.1 List Brands
```
GET /v1/procurement/brands
```

**Query Params:** `page`, `per_page`

**Response `200`**
```json
{
  "success": true,
  "message": "Brands retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "BrandX",
        "description": "A premium brand",
        "use_for_repair": false,
        "created_at": "2026-01-01T00:00:00.000000Z",
        "updated_at": "2026-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

---

### 3.2 Create Brand
```
POST /v1/procurement/brands
```

**Request Body**
```json
{
  "name": "BrandX",
  "description": "A premium brand"
}
```

| Field | Type | Required |
|-------|------|----------|
| `name` | string | **Yes** |
| `description` | string | No |

**Response `201`** — Returns created brand object.

---

### 3.3 Get Brand
```
GET /v1/procurement/brands/{id}
```

---

### 3.4 Update Brand
```
PUT /v1/procurement/brands/{id}
```

**Request Body** — Same as create.

---

### 3.5 Delete Brand
```
DELETE /v1/procurement/brands/{id}
```

---

## 4. Units

Units of measurement (e.g., Kilogram, Piece). Supports sub-units with conversion.

---

### 4.1 List Units
```
GET /v1/procurement/units
```

**Response `200`**
```json
{
  "success": true,
  "message": "Units retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "actual_name": "Kilogram",
        "short_name": "kg",
        "allow_decimal": true,
        "base_unit_id": null,
        "base_unit_multiplier": null,
        "display_name": "Kilogram",
        "sub_units": [
          {
            "id": 2,
            "actual_name": "Gram",
            "short_name": "g",
            "allow_decimal": false,
            "base_unit_id": 1,
            "base_unit_multiplier": 0.001,
            "display_name": "Gram (0.001 g)"
          }
        ]
      }
    ]
  }
}
```

---

### 4.2 Create Unit
```
POST /v1/procurement/units
```

**Request Body**
```json
{
  "actual_name": "Kilogram",
  "short_name": "kg",
  "allow_decimal": true,
  "base_unit_id": null,
  "base_unit_multiplier": null
}
```

**Sub-unit example (Gram as sub-unit of Kilogram):**
```json
{
  "actual_name": "Gram",
  "short_name": "g",
  "allow_decimal": false,
  "base_unit_id": 1,
  "base_unit_multiplier": 0.001
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `actual_name` | string | **Yes** | Full name e.g. `"Kilogram"` |
| `short_name` | string | **Yes** | Abbreviation e.g. `"kg"` |
| `allow_decimal` | boolean | No | Allow fractional qty |
| `base_unit_id` | integer | No | Parent unit ID for sub-unit conversion |
| `base_unit_multiplier` | number | No | Conversion factor to base unit |

---

### 4.3 Get Unit
```
GET /v1/procurement/units/{id}
```

Returns unit with `base_unit` and `sub_units` loaded.

---

### 4.4 Update Unit
```
PUT /v1/procurement/units/{id}
```

---

### 4.5 Delete Unit
```
DELETE /v1/procurement/units/{id}
```

---

## 5. Categories

Supports 2-level hierarchy: main category → sub-category.

---

### 5.1 List Categories
```
GET /v1/procurement/categories
```

Returns main product categories with their sub-categories.

**Response `200`**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Electronics",
        "short_code": "ELEC",
        "description": null,
        "category_type": "product",
        "parent_id": 0,
        "sub_categories": [
          {
            "id": 2,
            "name": "Gadgets",
            "parent_id": 1,
            "category_type": "product"
          }
        ]
      }
    ]
  }
}
```

---

### 5.2 Create Category
```
POST /v1/procurement/categories
```

**Request Body (Main Category)**
```json
{
  "name": "Electronics",
  "short_code": "ELEC",
  "description": "Electronic products",
  "parent_id": 0,
  "category_type": "product"
}
```

**Request Body (Sub-category)**
```json
{
  "name": "Gadgets",
  "parent_id": 1,
  "category_type": "product"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | **Yes** | Category name |
| `short_code` | string | No | Short identifier |
| `description` | string | No | Description |
| `parent_id` | integer | No | `0` = main category (default: `0`) |
| `category_type` | string | No | `product` \| `expense` (default: `product`) |

---

### 5.3 Get Category
```
GET /v1/procurement/categories/{id}
```

---

### 5.4 Update Category
```
PUT /v1/procurement/categories/{id}
```

---

### 5.5 Delete Category
```
DELETE /v1/procurement/categories/{id}
```

---

## 6. Warranties

---

### 6.1 List Warranties
```
GET /v1/procurement/warranties
```

**Response `200`**
```json
{
  "success": true,
  "message": "Warranties retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Standard Warranty",
        "description": "Standard 1 year warranty",
        "duration": 12,
        "duration_type": "months",
        "display_name": "Standard Warranty (12 months)",
        "created_at": "2026-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

---

### 6.2 Create Warranty
```
POST /v1/procurement/warranties
```

**Request Body**
```json
{
  "name": "Standard Warranty",
  "description": "Covers manufacturing defects",
  "duration": 12,
  "duration_type": "months"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | **Yes** | Warranty name |
| `description` | string | No | Description |
| `duration` | integer | **Yes** | Duration value (min: 1) |
| `duration_type` | string | **Yes** | `days` \| `months` \| `years` |

---

### 6.3 Get Warranty
```
GET /v1/procurement/warranties/{id}
```

---

### 6.4 Update Warranty
```
PUT /v1/procurement/warranties/{id}
```

---

### 6.5 Delete Warranty
```
DELETE /v1/procurement/warranties/{id}
```

---

## 7. Selling Price Groups

Customer price tiers (e.g., Wholesale, Retail, VIP). Each active group can override product prices.

---

### 7.1 List Selling Price Groups
```
GET /v1/procurement/selling-price-groups
```

**Response `200`**
```json
{
  "success": true,
  "message": "Selling price groups retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Wholesale",
        "description": "Bulk buyer pricing",
        "is_active": true,
        "created_at": "2026-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

---

### 7.2 Create Selling Price Group
```
POST /v1/procurement/selling-price-groups
```

**Request Body**
```json
{
  "name": "Wholesale",
  "description": "Pricing for bulk buyers"
}
```

| Field | Type | Required |
|-------|------|----------|
| `name` | string | **Yes** |
| `description` | string | No |

**Response `201`** — Returns created price group with `is_active: true`.

---

### 7.3 Get Selling Price Group
```
GET /v1/procurement/selling-price-groups/{id}
```

---

### 7.4 Update Selling Price Group
```
PUT /v1/procurement/selling-price-groups/{id}
```

---

### 7.5 Delete Selling Price Group
```
DELETE /v1/procurement/selling-price-groups/{id}
```

---

### 7.6 Toggle Active State
```
PATCH /v1/procurement/selling-price-groups/{id}/toggle-active
```

No request body needed. Flips `is_active` between `true` and `false`.

**Response `200`**
```json
{
  "success": true,
  "message": "Price group activated successfully",
  "data": {
    "id": 1,
    "name": "Wholesale",
    "is_active": true
  }
}
```

---

## Next.js Integration Guide

### 1. Create an API client utility

```typescript
// lib/api/procurement.ts

const BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/api';

async function request<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const token = localStorage.getItem('access_token');

  const res = await fetch(`${BASE_URL}${endpoint}`, {
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    ...options,
  });

  const data = await res.json();

  if (!res.ok || !data.success) {
    throw new Error(data.message || 'API request failed');
  }

  return data;
}

// ─── Products ───────────────────────────────────────────────
export const productApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/products${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/products/${id}`),
  create: (body: object) =>
    request('/v1/procurement/products', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/products/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/products/${id}`, { method: 'DELETE' }),
  getStock: (id: number) =>
    request(`/v1/procurement/products/${id}/stock`),
  getVariations: (id: number) =>
    request(`/v1/procurement/products/${id}/variations`),
  getGroupPrices: (id: number) =>
    request(`/v1/procurement/products/${id}/group-prices`),
  updateGroupPrices: (id: number, groupPrices: object[]) =>
    request(`/v1/procurement/products/${id}/group-prices`, {
      method: 'POST',
      body: JSON.stringify({ group_prices: groupPrices }),
    }),
  activate: (id: number) =>
    request(`/v1/procurement/products/${id}/activate`, { method: 'PATCH' }),
  massDeactivate: (ids: number[]) =>
    request('/v1/procurement/products/mass-deactivate', { method: 'POST', body: JSON.stringify({ ids }) }),
  massDelete: (ids: number[]) =>
    request('/v1/procurement/products/mass-delete', { method: 'POST', body: JSON.stringify({ ids }) }),
  checkSku: (sku: string, productId?: number) =>
    request('/v1/procurement/products/check-sku', {
      method: 'POST',
      body: JSON.stringify({ sku, product_id: productId }),
    }),
};

// ─── Variation Templates ─────────────────────────────────────
export const variationTemplateApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/variation-templates${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/variation-templates/${id}`),
  create: (body: object) =>
    request('/v1/procurement/variation-templates', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/variation-templates/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/variation-templates/${id}`, { method: 'DELETE' }),
  addValue: (id: number, name: string) =>
    request(`/v1/procurement/variation-templates/${id}/values`, {
      method: 'POST',
      body: JSON.stringify({ name }),
    }),
  removeValue: (id: number, valueId: number) =>
    request(`/v1/procurement/variation-templates/${id}/values/${valueId}`, { method: 'DELETE' }),
};

// ─── Brands ──────────────────────────────────────────────────
export const brandApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/brands${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/brands/${id}`),
  create: (body: object) =>
    request('/v1/procurement/brands', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/brands/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/brands/${id}`, { method: 'DELETE' }),
};

// ─── Units ───────────────────────────────────────────────────
export const unitApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/units${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/units/${id}`),
  create: (body: object) =>
    request('/v1/procurement/units', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/units/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/units/${id}`, { method: 'DELETE' }),
};

// ─── Categories ──────────────────────────────────────────────
export const categoryApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/categories${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/categories/${id}`),
  create: (body: object) =>
    request('/v1/procurement/categories', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/categories/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/categories/${id}`, { method: 'DELETE' }),
};

// ─── Warranties ──────────────────────────────────────────────
export const warrantyApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/warranties${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/warranties/${id}`),
  create: (body: object) =>
    request('/v1/procurement/warranties', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/warranties/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/warranties/${id}`, { method: 'DELETE' }),
};

// ─── Selling Price Groups ─────────────────────────────────────
export const sellingPriceGroupApi = {
  list: (params?: Record<string, any>) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return request(`/v1/procurement/selling-price-groups${qs}`);
  },
  get: (id: number) => request(`/v1/procurement/selling-price-groups/${id}`),
  create: (body: object) =>
    request('/v1/procurement/selling-price-groups', { method: 'POST', body: JSON.stringify(body) }),
  update: (id: number, body: object) =>
    request(`/v1/procurement/selling-price-groups/${id}`, { method: 'PUT', body: JSON.stringify(body) }),
  delete: (id: number) =>
    request(`/v1/procurement/selling-price-groups/${id}`, { method: 'DELETE' }),
  toggleActive: (id: number) =>
    request(`/v1/procurement/selling-price-groups/${id}/toggle-active`, { method: 'PATCH' }),
};
```

---

### 2. Usage Examples in Components

```typescript
// app/products/page.tsx — List products with Next.js

'use client';

import { useEffect, useState } from 'react';
import { productApi } from '@/lib/api/procurement';

export default function ProductsPage() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    productApi.list({ per_page: 20, type: 'single' })
      .then((res: any) => setProducts(res.data.data))
      .catch(console.error)
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p>Loading...</p>;

  return (
    <ul>
      {products.map((p: any) => (
        <li key={p.id}>{p.name} — {p.sku}</li>
      ))}
    </ul>
  );
}
```

```typescript
// Create a product
async function createProduct(formData: object) {
  try {
    const res = await productApi.create(formData);
    console.log('Created:', res.data);
  } catch (err) {
    console.error('Error:', err.message);
  }
}

// Check SKU before submitting form
async function validateSku(sku: string) {
  const res = await productApi.checkSku(sku) as any;
  return res.data.is_unique;
}
```

---

### 3. Environment Variable

```env
# .env.local
NEXT_PUBLIC_API_URL=http://localhost/api
```

---

### 4. CORS Note

Ensure the Laravel backend has the following origins allowed in `config/cors.php`:

```php
'allowed_origins' => ['http://localhost:3000'],
```

---

*Generated: 2026-02-23 | Procurement Module v1.0 | FlawMind ERP*
