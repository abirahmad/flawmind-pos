# ERP Product Management - Complete Feature Documentation
> Generated: 2026-02-23 | For API Development Reference

---

## TABLE OF CONTENTS

1. [Product Module Overview](#1-product-module-overview)
2. [Database Schema](#2-database-schema)
   - 2.1 Products Table
   - 2.2 Product Variations Table
   - 2.3 Variations Table
   - 2.4 Variation Templates Table
   - 2.5 Variation Value Templates Table
   - 2.6 Variation Location Details Table
   - 2.7 Selling Price Groups Table
   - 2.8 Variation Group Prices Table
   - 2.9 Product Locations Table
   - 2.10 Product Racks Table
   - 2.11 Discount Variations Table
3. [Supporting Lookup Tables](#3-supporting-lookup-tables)
   - 3.1 Brands
   - 3.2 Units
   - 3.3 Categories
   - 3.4 Warranties
4. [API Endpoints Reference](#4-api-endpoints-reference)
5. [Business Logic & Rules](#5-business-logic--rules)
6. [Relationships Map](#6-relationships-map)
7. [Import / Export Features](#7-import--export-features)
8. [Label Printing System](#8-label-printing-system)
9. [Permissions & Security](#9-permissions--security)
10. [Proposed REST API Design](#10-proposed-rest-api-design)

---

## 1. PRODUCT MODULE OVERVIEW

### Product Types
| Type | Description |
|------|-------------|
| `single` | Individual product with one SKU and one price |
| `variable` | Product with multiple variations (e.g., different sizes, colors) |
| `modifier` | Add-on/modifier that attaches to other products |
| `combo` | Bundle product composed of multiple product variations |

### Menu Features (from UI)
| Menu Item | Description |
|-----------|-------------|
| List Products | Browse, filter, and manage all products |
| Add Product | Create new single/variable/modifier/combo product |
| Update Price | Bulk price update across products or price groups |
| Print Labels | Print barcode labels for products |
| Variations | Manage reusable variation templates (Color, Size, etc.) |
| Import Products | Bulk import products via CSV/Excel |
| Import Opening Stock | Bulk import initial inventory quantities |
| Selling Price Group | Manage customer price tiers |
| Units | Manage units of measurement |
| Categories | Manage product categories and sub-categories |
| Brands | Manage product brands |
| Warranties | Manage warranty definitions |

---

## 2. DATABASE SCHEMA

### 2.1 Products Table
**Table**: `products`
**Model**: `app/Product.php`
**Migration**: `database/migrations/2017_08_08_115903_create_products_table.php`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK, auto-increment) | No | — | Primary key |
| `name` | string | No | — | Product name |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `type` | enum('single','variable','modifier','combo') | No | — | Product type |
| `unit_id` | int (FK → units) | No | — | Primary unit of measurement |
| `secondary_unit_id` | int (FK → units) | Yes | null | Secondary unit for conversion |
| `brand_id` | int (FK → brands) | Yes | null | Associated brand |
| `category_id` | int (FK → categories) | Yes | null | Main category |
| `sub_category_id` | int (FK → categories) | Yes | null | Sub-category |
| `tax` | int (FK → tax_rates) | Yes | null | Tax rate ID |
| `tax_type` | enum('inclusive','exclusive') | No | 'exclusive' | Tax calculation method |
| `enable_stock` | tinyint(1) | No | 0 | Enable stock tracking |
| `alert_quantity` | decimal(22,4) | Yes | 0 | Low stock alert threshold |
| `sku` | string | No | — | Stock Keeping Unit (unique per business) |
| `barcode_type` | enum | No | — | Barcode format: `C39`, `C128`, `EAN-13`, `EAN-8`, `UPC-A`, `UPC-E`, `ITF-14` |
| `warranty_id` | int (FK → warranties) | Yes | null | Associated warranty |
| `weight` | string | Yes | null | Product weight |
| `image` | string | Yes | null | Product image filename |
| `product_description` | text | Yes | null | Detailed product description |
| `is_inactive` | tinyint(1) | No | 0 | 1 = inactive, 0 = active |
| `not_for_selling` | tinyint(1) | No | 0 | 1 = exclude from sales |
| `expiry_period` | decimal(4,2) | Yes | null | Expiry duration value |
| `expiry_period_type` | enum('days','months') | Yes | null | Expiry period unit |
| `enable_sr_no` | tinyint(1) | No | 0 | Enable serial number tracking |
| `woocommerce_disable_sync` | tinyint(1) | No | 0 | Disable WooCommerce sync |
| `created_by` | int (FK → users) | No | — | Creator user ID |
| `product_custom_field1` | string | Yes | null | Custom field 1 |
| `product_custom_field2` | string | Yes | null | Custom field 2 |
| `product_custom_field3` | string | Yes | null | Custom field 3 |
| `product_custom_field4` | string | Yes | null | Custom field 4 |
| `product_custom_field5` | string | Yes | null | Custom field 5 |
| `product_custom_field6` | string | Yes | null | Custom field 6 |
| `product_custom_field7` | string | Yes | null | Custom field 7 |
| `product_custom_field8` | string | Yes | null | Custom field 8 |
| `product_custom_field9` | string | Yes | null | Custom field 9 |
| `product_custom_field10` | string | Yes | null | Custom field 10 |
| `product_custom_field11` | string | Yes | null | Custom field 11 |
| `product_custom_field12` | string | Yes | null | Custom field 12 |
| `product_custom_field13` | string | Yes | null | Custom field 13 |
| `product_custom_field14` | string | Yes | null | Custom field 14 |
| `product_custom_field15` | string | Yes | null | Custom field 15 |
| `product_custom_field16` | string | Yes | null | Custom field 16 |
| `product_custom_field17` | string | Yes | null | Custom field 17 |
| `product_custom_field18` | string | Yes | null | Custom field 18 |
| `product_custom_field19` | string | Yes | null | Custom field 19 |
| `product_custom_field20` | string | Yes | null | Custom field 20 |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Indexes**: `name`, `business_id`, `unit_id`, `created_by`, `warranty_id`

**Model Scopes**:
- `active()` — WHERE is_inactive = 0
- `inactive()` — WHERE is_inactive = 1
- `productForSales()` — WHERE not_for_selling = 0
- `productNotForSales()` — WHERE not_for_selling = 1
- `forLocation($location_id)` — Filter by business location

**Model Relationships**:
```
product_variations()     → HasMany ProductVariation
variations()             → HasMany Variation (through product_variations)
brand()                  → BelongsTo Brands
unit()                   → BelongsTo Unit
second_unit()            → BelongsTo Unit (secondary_unit_id)
category()               → BelongsTo Category
sub_category()           → BelongsTo Category (sub_category_id)
product_tax()            → BelongsTo TaxRate
modifier_products()      → BelongsToMany Product
modifier_sets()          → BelongsToMany Product
purchase_lines()         → HasMany PurchaseLine
warranty()               → BelongsTo Warranty
product_locations()      → BelongsToMany BusinessLocation
media()                  → MorphMany Media
rack_details()           → HasMany ProductRack
```

---

### 2.2 Product Variations Table
**Table**: `product_variations`
**Model**: `app/ProductVariation.php`
**Migration**: `database/migrations/2017_08_10_061146_create_product_variations_table.php`

> Groups variation attributes for variable products (e.g., "Color", "Size").
> Single products always have ONE dummy product_variation.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Variation group name (e.g., "Color") |
| `product_id` | int (FK → products) | No | — | Parent product |
| `variation_template_id` | int (FK → variation_templates) | Yes | null | Linked reusable template |
| `is_dummy` | tinyint(1) | No | 1 | 1 = auto-created for single products |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Indexes**: `name`, `product_id`

**Model Relationships**:
```
variations()           → HasMany Variation
variation_template()   → BelongsTo VariationTemplate
```

---

### 2.3 Variations Table
**Table**: `variations`
**Model**: `app/Variation.php`
**Migration**: `database/migrations/2017_08_10_061216_create_variations_table.php`

> Actual sellable units with unique SKU, price, and stock.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Variation value (e.g., "Red", "Large") |
| `product_id` | int (FK → products) | No | — | Parent product |
| `product_variation_id` | int (FK → product_variations) | No | — | Parent product_variation |
| `variation_value_id` | int (FK → variation_value_templates) | Yes | null | Linked template value |
| `sub_sku` | string | Yes | null | Unique SKU for this variation |
| `default_purchase_price` | decimal(22,4) | Yes | null | Base cost price (excl. tax) |
| `dpp_inc_tax` | decimal(22,4) | No | 0 | Default purchase price incl. tax |
| `profit_percent` | decimal(22,4) | No | 0 | Profit margin percentage |
| `default_sell_price` | decimal(22,4) | Yes | null | Base selling price (excl. tax) |
| `sell_price_inc_tax` | decimal(22,4) | Yes | null | Selling price incl. tax |
| `combo_variations` | JSON | Yes | null | Child variation IDs for combo products |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |
| `deleted_at` | timestamp | Yes | null | Soft delete |

**Indexes**: `name`, `sub_sku`

**Model Relationships**:
```
product_variation()          → BelongsTo ProductVariation
product()                    → BelongsTo Product
sell_lines()                 → HasMany TransactionSellLine
variation_location_details() → HasMany VariationLocationDetails
group_prices()               → HasMany VariationGroupPrice
media()                      → MorphMany Media
```

---

### 2.4 Variation Templates Table
**Table**: `variation_templates`
**Model**: `app/VariationTemplate.php`
**Migration**: `database/migrations/2017_08_09_061616_create_variation_templates_table.php`

> Reusable variation templates shared across products.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Template name (e.g., "Color", "Size") |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Model Relationships**:
```
values() → HasMany VariationValueTemplate
```

---

### 2.5 Variation Value Templates Table
**Table**: `variation_value_templates`
**Model**: `app/VariationValueTemplate.php`
**Migration**: `database/migrations/2017_08_09_061638_create_variation_value_templates_table.php`

> Predefined values for reusable variation templates.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Value name (e.g., "Red", "Large") |
| `variation_template_id` | int (FK → variation_templates) | No | — | Parent template |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Indexes**: `name`, `variation_template_id`

---

### 2.6 Variation Location Details Table
**Table**: `variation_location_details`
**Model**: `app/VariationLocationDetails.php`
**Migration**: `database/migrations/2017_12_25_163227_create_variation_location_details_table.php`

> Tracks stock quantity per variation per business location.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `product_id` | int | No | — | Product reference |
| `product_variation_id` | int | No | — | Product variation reference |
| `variation_id` | int (FK → variations) | No | — | Variation reference |
| `location_id` | int (FK → business_locations) | No | — | Business location |
| `qty_available` | decimal(22,4) | No | 0 | Current available quantity |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Indexes**: `product_id`, `product_variation_id`, `variation_id`

---

### 2.7 Selling Price Groups Table
**Table**: `selling_price_groups`
**Model**: `app/SellingPriceGroup.php`
**Migration**: `database/migrations/2018_09_06_114438_create_selling_price_groups_table.php`

> Define customer-segment-based price tiers (e.g., Wholesale, Retail, VIP).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Group name (e.g., "Wholesale") |
| `description` | text | Yes | null | Description |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `is_active` | tinyint(1) | No | 1 | Active flag |
| `deleted_at` | timestamp | Yes | null | Soft delete |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Model Scopes**:
- `active()` — WHERE is_active = 1

**Notable Methods**:
- `forDropdown($business_id, $with_default)` — Returns dropdown-ready list
- `countSellingPriceGroups($business_id)` — Count active groups

---

### 2.8 Variation Group Prices Table
**Table**: `variation_group_prices`
**Model**: `app/VariationGroupPrice.php`
**Migration**: `database/migrations/2018_09_06_154057_create_variation_group_prices_table.php`

> Maps price overrides from selling price groups to specific variations.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `variation_id` | int (FK → variations) | No | — | Target variation |
| `price_group_id` | int (FK → selling_price_groups) | No | — | Price group |
| `price_inc_tax` | decimal(22,4) | No | — | Price including tax |
| `price_type` | string | No | 'fixed' | `fixed` or `percentage` |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Accessor Logic** (`getCalculatedPriceAttribute`):
- If `price_type = 'percentage'`: calculated price = variation's sell_price_inc_tax × (price_inc_tax / 100)
- If `price_type = 'fixed'`: calculated price = price_inc_tax

---

### 2.9 Product Locations Table
**Table**: `product_locations`
**Migration**: `database/migrations/2019_09_12_105616_create_product_locations_table.php`

> Many-to-many mapping: which products are available at which locations.

| Column | Type | Indexed | Description |
|--------|------|---------|-------------|
| `product_id` | int | Yes | Product reference |
| `location_id` | int | Yes | Business location reference |

---

### 2.10 Product Racks Table
**Table**: `product_racks`
**Model**: `app/ProductRack.php`
**Migration**: `database/migrations/2018_04_17_160845_add_product_racks_table.php`

> Physical storage location within a warehouse/store (rack, row, position).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `business_id` | int | No | — | Owner business |
| `location_id` | int | No | — | Business location |
| `product_id` | int | No | — | Product reference |
| `rack` | string | Yes | null | Rack identifier |
| `row` | string | Yes | null | Row position |
| `position` | string | Yes | null | Position in rack |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Business Settings** (to enable rack features):
- `enable_racks` — boolean
- `enable_row` — boolean
- `enable_position` — boolean

---

### 2.11 Discount Variations Table
**Table**: `discount_variations`
**Migration**: `database/migrations/2020_09_22_121639_create_discount_variations_table.php`

> Many-to-many: apply discounts to specific product variations.

| Column | Type | Description |
|--------|------|-------------|
| `discount_id` | int | Discount reference |
| `variation_id` | int | Variation reference |

---

## 3. SUPPORTING LOOKUP TABLES

### 3.1 Brands
**Table**: `brands`
**Model**: `app/Brands.php`
**Controller**: `app/Http/Controllers/BrandController.php`
**Migration**: `database/migrations/2017_07_23_113209_create_brands_table.php`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `name` | string | No | — | Brand name |
| `description` | text | Yes | null | Description |
| `use_for_repair` | tinyint(1) | Yes | null | Repair module integration flag |
| `created_by` | int (FK → users) | No | — | Creator |
| `deleted_at` | timestamp | Yes | null | Soft delete |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Operations**: Create, Read, Update, Delete (soft-delete)

---

### 3.2 Units
**Table**: `units`
**Model**: `app/Unit.php`
**Controller**: `app/Http/Controllers/UnitController.php`
**Migration**: `database/migrations/2017_07_26_122313_create_units_table.php`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `actual_name` | string | No | — | Full unit name (e.g., "Kilogram") |
| `short_name` | string | No | — | Abbreviation (e.g., "kg") |
| `allow_decimal` | tinyint(1) | No | 0 | Allow fractional quantities |
| `base_unit_id` | int (FK → units) | Yes | null | Parent unit for sub-unit |
| `base_unit_multiplier` | decimal(20,4) | Yes | null | Conversion factor to base unit |
| `created_by` | int (FK → users) | No | — | Creator |
| `deleted_at` | timestamp | Yes | null | Soft delete |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Model Relationships**:
```
sub_units()  → HasMany Unit (where base_unit_id = this.id)
base_unit()  → BelongsTo Unit (base_unit_id)
```

**Features**:
- Base units (e.g., Kilogram) and sub-units (e.g., Gram = 0.001 kg)
- Multi-unit support with conversion ratios
- Dropdown format: `"actual_name (base_unit_multiplier short_name)"`

---

### 3.3 Categories
**Table**: `categories`
**Model**: `app/Category.php`
**Controller**: `app/Http/Controllers/TaxonomyController.php`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `name` | string | No | — | Category name |
| `short_code` | string | Yes | null | Short code |
| `description` | text | Yes | null | Description |
| `category_type` | string | No | — | Type: `product`, `expense`, etc. |
| `parent_id` | int | No | 0 | 0 = main category; else parent's ID |
| `created_by` | int (FK → users) | No | — | Creator |
| `deleted_at` | timestamp | Yes | null | Soft delete |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Hierarchy**:
- `parent_id = 0` → Main Category
- `parent_id = {id}` → Sub-category of that parent

**Helper Methods**:
- `catAndSubCategories()` — Returns nested category array
- `forDropdown($business_id, $type)` — Filtered by type and parent_id = 0

---

### 3.4 Warranties
**Table**: `warranties`
**Model**: `app/Warranty.php`
**Controller**: `app/Http/Controllers/WarrantyController.php`
**Migration**: `database/migrations/2019_12_02_105025_create_warranties_table.php`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | int (PK) | No | — | Primary key |
| `name` | string | No | — | Warranty name |
| `business_id` | int (FK → businesses) | No | — | Owner business |
| `description` | text | Yes | null | Description |
| `duration` | int | No | — | Duration value |
| `duration_type` | enum('days','months','years') | No | — | Duration unit |
| `created_at` | timestamp | Yes | null | — |
| `updated_at` | timestamp | Yes | null | — |

**Model Methods**:
- `forDropdown($business_id)` — Returns `"name (duration duration_type)"` format
- `getDisplayNameAttribute()` — Formatted warranty name
- `getEndDate($date)` — Calculates expiry date from a start date

**Junction Table**: `sell_line_warranties` — Links warranties to specific sales lines

---

## 4. API ENDPOINTS REFERENCE

### 4.1 Products

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/products` | List products (with filters) |
| POST | `/products` | Create new product |
| GET | `/products/{id}` | View product detail |
| PUT/PATCH | `/products/{id}` | Update product |
| DELETE | `/products/{id}` | Delete product |
| GET | `/products/list` | Product list (AJAX/JSON) |
| GET | `/products/download-excel` | Export products as Excel |
| GET | `/products/stock-history/{id}` | Stock movement history |
| POST | `/products/mass-deactivate` | Bulk deactivate |
| POST | `/products/mass-delete` | Bulk delete |
| GET | `/products/activate/{id}` | Activate a product |
| GET | `/products/view-product-group-price/{id}` | View price group prices |
| GET | `/products/add-selling-prices/{id}` | Add/edit price group prices |
| POST | `/products/save-selling-prices` | Save price group prices |
| POST | `/products/get_sub_categories` | Fetch sub-categories |
| GET | `/products/get_sub_units` | Fetch sub-units for a unit |
| POST | `/products/product_form_part` | Get variation form fields (AJAX) |
| POST | `/products/get_product_variation_row` | Get variation row HTML |
| POST | `/products/get_variation_template` | Get template + values |
| POST | `/products/check_product_sku` | Validate SKU uniqueness |
| POST | `/products/validate_variation_skus` | Validate multiple variation SKUs |
| GET | `/products/quick_add` | Quick add product form |
| POST | `/products/save_quick_product` | Save quick product |
| GET | `/products/get-combo-product-entry-row` | Combo product row entry |
| POST | `/products/toggle-woocommerce-sync` | Toggle WooCommerce sync |

### 4.2 Variation Templates

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/variation-templates` | List all variation templates |
| POST | `/variation-templates` | Create variation template |
| GET | `/variation-templates/{id}/edit` | Edit variation template |
| PUT | `/variation-templates/{id}` | Update variation template |
| DELETE | `/variation-templates/{id}` | Delete variation template |

### 4.3 Brands

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/brands` | List brands |
| POST | `/brands` | Create brand |
| GET | `/brands/{id}/edit` | Edit brand |
| PUT | `/brands/{id}` | Update brand |
| DELETE | `/brands/{id}` | Delete brand |

### 4.4 Units

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/units` | List units |
| POST | `/units` | Create unit |
| GET | `/units/{id}/edit` | Edit unit |
| PUT | `/units/{id}` | Update unit |
| DELETE | `/units/{id}` | Delete unit |

### 4.5 Categories

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/taxonomy` | List categories |
| POST | `/taxonomy` | Create category |
| GET | `/taxonomy/{id}/edit` | Edit category |
| PUT | `/taxonomy/{id}` | Update category |
| DELETE | `/taxonomy/{id}` | Delete category |

### 4.6 Warranties

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/warranties` | List warranties |
| POST | `/warranties` | Create warranty |
| GET | `/warranties/{id}/edit` | Edit warranty |
| PUT | `/warranties/{id}` | Update warranty |
| DELETE | `/warranties/{id}` | Delete warranty |

### 4.7 Selling Price Groups

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/selling-price-group` | List price groups |
| POST | `/selling-price-group` | Create price group |
| GET | `/selling-price-group/{id}/edit` | Edit price group |
| PUT | `/selling-price-group/{id}` | Update price group |
| DELETE | `/selling-price-group/{id}` | Delete price group |
| GET | `/selling-price-group/activate-deactivate/{id}` | Toggle active state |
| GET | `/update-product-price` | Bulk price update UI |
| GET | `/export-product-price` | Export price group prices |
| POST | `/import-product-price` | Import price group prices |

### 4.8 Import

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/import-products` | Import products form |
| POST | `/import-products` | Upload & process product import |
| GET | `/import-opening-stock` | Import opening stock form |
| POST | `/import-opening-stock` | Upload & process opening stock |

### 4.9 Labels

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/labels` | Label printing UI |
| POST | `/labels/add-product-row` | Add product row (AJAX) |
| POST | `/labels/preview` | Preview labels |

---

## 5. BUSINESS LOGIC & RULES

### 5.1 Product Creation Rules
- Product `name` is required
- `sku` must be unique within the business
- `unit_id` is required
- Category may be required based on business settings
- `type` must be one of: `single`, `variable`, `modifier`, `combo`
- Brand and warranty are optional

### 5.2 Variation Structure Rules
- **Single products**: Always have exactly ONE dummy `product_variation` + ONE `variation`
- **Variable products**: Have MULTIPLE `product_variations`, each with one or more `variation` records
- Each `variation.sub_sku` should be unique within the business (recommended)
- Variations support independent pricing per variation value

### 5.3 Pricing Rules
| Price Field | Description |
|-------------|-------------|
| `default_purchase_price` | Cost price excluding tax |
| `dpp_inc_tax` | Cost price including tax |
| `default_sell_price` | Selling price excluding tax |
| `sell_price_inc_tax` | Selling price including tax (customer-facing) |
| `profit_percent` | Auto-calculated from purchase vs sell price |

**Price Group Calculation**:
- `fixed`: customer pays `price_inc_tax` directly
- `percentage`: customer pays `sell_price_inc_tax × (price_inc_tax / 100)`

### 5.4 Stock Rules
- `enable_stock = 1`: stock is tracked per variation per location
- `enable_stock = 0`: unlimited stock, no tracking
- `alert_quantity`: triggers low-stock alert when `qty_available ≤ alert_quantity`
- Multi-location: stock tracked in `variation_location_details`
- Secondary unit support (e.g., 1 kg = 1000 g) via `secondary_unit_id` + `base_unit_multiplier`

### 5.5 Tax Rules
| Setting | Behavior |
|---------|---------|
| `tax_type = 'inclusive'` | Tax is included in listed price |
| `tax_type = 'exclusive'` | Tax is added on top of listed price |

- Tax rate linked via `products.tax` → `tax_rates.id`
- Applied consistently to both purchase and selling prices

### 5.6 Expiry Management
- Optional: controlled by `expiry_period` and `expiry_period_type`
- Types: `days` or `months`
- Expiry date calculated as: purchase date + expiry period
- Feature enabled via business setting `enable_product_expiry`

### 5.7 Location Assignment
- Products assigned to locations via `product_locations` pivot table
- Unassigned = visible in all locations (default)
- Stock tracked separately per location in `variation_location_details`
- Users can only access locations they're permitted to

### 5.8 Image Management
- One primary image per product (`products.image` — filename only)
- Multiple images per variation via polymorphic `media` table
- Supports upload on create and edit

### 5.9 Serial Number Tracking
- `enable_sr_no = 1` enables serial number tracking per unit sold
- Used for warranty tracking and product traceability

### 5.10 WooCommerce Sync
- `woocommerce_disable_sync = 1` prevents syncing this product to WooCommerce
- Toggle via dedicated endpoint

### 5.11 Combo Products
- `type = 'combo'` bundles multiple variations together
- `variations.combo_variations` stores JSON array of child variation IDs + quantities

### 5.12 Rack / Storage Tracking
- Optional feature: enabled per business (`enable_racks`, `enable_row`, `enable_position`)
- Each product can have rack, row, and position per location

---

## 6. RELATIONSHIPS MAP

```
BUSINESS
└── HAS MANY ──────────────────────────── PRODUCTS
                                            │
        ┌───────────────────────────────────┤
        │                                   │
        ▼                                   ▼
   BRAND (optional)              PRODUCT_VARIATIONS (1..n)
   UNIT (required)                          │
   SECONDARY_UNIT (optional)                ▼
   CATEGORY (optional)            VARIATIONS (1..n per product_variation)
   SUB_CATEGORY (optional)                  │
   TAX_RATE (optional)           ┌──────────┼───────────────┐
   WARRANTY (optional)           │          │               │
                                 ▼          ▼               ▼
                    VARIATION_LOCATION  VARIATION_GROUP  SELL_LINES
                       _DETAILS          _PRICES
                    (stock per loc)   (price per group)

SELLING_PRICE_GROUP
└── HAS MANY → VARIATION_GROUP_PRICES → linked to VARIATIONS

VARIATION_TEMPLATE
└── HAS MANY → VARIATION_VALUE_TEMPLATES
└── HAS MANY → PRODUCT_VARIATIONS (via variation_template_id)

DISCOUNT
└── MANY-TO-MANY → VARIATIONS (via discount_variations)

PRODUCT
└── HAS MANY → PRODUCT_RACKS (rack, row, position per location)
└── BELONGS TO MANY → BUSINESS_LOCATIONS (via product_locations)
└── POLYMORPHIC MEDIA (product images)

VARIATION
└── POLYMORPHIC MEDIA (variation images)

WARRANTY
└── MANY-TO-MANY → SELL_LINES (via sell_line_warranties)
└── BELONGS TO ← PRODUCT (via products.warranty_id)
```

---

## 7. IMPORT / EXPORT FEATURES

### 7.1 Product Import (CSV/Excel)
**Controller**: `app/Http/Controllers/ImportProductsController.php`

**Minimum Required Columns**: 37

| # | Column | Description |
|---|--------|-------------|
| 1 | Product Name | Required |
| 2 | SKU | Must be unique per business |
| 3 | Brand | Brand name (matched by name) |
| 4 | Category | Category name |
| 5 | Sub-category | Sub-category name |
| 6 | Type | `single`, `variable`, `modifier`, `combo` |
| 7 | Unit | Unit name |
| 8 | Tax | Tax rate name |
| 9 | Tax Type | `inclusive` or `exclusive` |
| 10 | Enable Stock | `yes` / `no` |
| 11 | Alert Quantity | Numeric |
| 12 | Barcode Type | `C39`, `C128`, `EAN-13`, etc. |
| 13 | Expiry Period | Numeric |
| 14 | Expiry Period Type | `days` or `months` |
| 15–29 | Variation Details | Variation name, value, SKU, prices |
| 30 | Image | Filename or URL |
| 31–37 | Custom Fields | product_custom_field1–7 |

**Validation**:
- Rollback on any error; errors reported with row numbers
- Checks subscription quota before import
- Supports image URLs (downloaded) or local filenames

### 7.2 Opening Stock Import
**Controller**: `app/Http/Controllers/ImportOpeningStockController.php`

- Imports initial inventory quantities per variation per location
- Creates stock adjustment records
- Date format follows business settings

### 7.3 Product Export
**Class**: `app/Exports/ProductsExport.php`

- Excel format
- All product details + variations + pricing

### 7.4 Selling Price Group Export / Import

- Export: prices per price group per variation
- Import: bulk price update for a specific group
- Routes: `GET /export-product-price`, `POST /import-product-price`

---

## 8. LABEL PRINTING SYSTEM

### 8.1 Features
- Print barcode labels for any product or variation
- Select products from list or from a purchase order
- Choose barcode template (layout)
- Select price group for variable pricing display
- Specify quantity per label

### 8.2 Barcode Types Supported
| Code | Format |
|------|--------|
| `C39` | Code 39 |
| `C128` | Code 128 |
| `EAN-13` | EAN-13 |
| `EAN-8` | EAN-8 |
| `UPC-A` | UPC-A |
| `UPC-E` | UPC-E |
| `ITF-14` | ITF-14 |

### 8.3 Barcode Templates
- Multiple templates supported per business
- Default template configurable
- System-wide and business-specific templates
- Custom descriptions per template

### 8.4 Label Workflow
1. User selects products (individually or from purchase)
2. Selects label template
3. Selects price group (optional)
4. Previews labels
5. Prints final output

---

## 9. PERMISSIONS & SECURITY

### 9.1 Product Permissions
| Permission | Access |
|------------|--------|
| `product.view` | View products list and detail |
| `product.create` | Create and edit products |
| `product.delete` | Delete products |
| `product.opening_stock` | Import opening stock |

### 9.2 Category Permissions
| Permission | Access |
|------------|--------|
| `category.view` | View categories |
| `category.create` | Create categories |
| `category.update` | Edit categories |
| `category.delete` | Delete categories |

### 9.3 Other Lookup Permissions
- `brand.view`, `brand.create`, `brand.update`, `brand.delete`
- `unit.view`, `unit.create`, `unit.update`, `unit.delete`

### 9.4 Price Group Permissions
- Dynamic per-group: `selling_price_group.{id}`
- Users must be explicitly granted access to each price group

### 9.5 Location Permissions
- Users are restricted to their permitted business locations
- Product lists and stock data filtered by permitted locations

### 9.6 Business Scoping
- All data is isolated by `business_id`
- Cross-business data access is not possible

---

## 10. PROPOSED REST API DESIGN

### 10.1 Products

```
GET    /api/products                          List products (supports filters: location_id, type, category_id, brand_id, is_inactive)
POST   /api/products                          Create product
GET    /api/products/{id}                     Get product details (includes variations, stock)
PUT    /api/products/{id}                     Update product
DELETE /api/products/{id}                     Delete product
GET    /api/products/{id}/stock               Get stock per location
GET    /api/products/{id}/variations          Get all variations for a product
GET    /api/products/{id}/group-prices        Get selling price group prices for product
POST   /api/products/{id}/group-prices        Update selling price group prices
POST   /api/products/mass-deactivate          Bulk deactivate { ids: [] }
POST   /api/products/mass-delete              Bulk delete { ids: [] }
GET    /api/products/export                   Export as Excel
POST   /api/products/import                   Import from CSV/Excel
POST   /api/products/import-opening-stock     Import opening stock CSV
POST   /api/products/check-sku               Validate SKU uniqueness
```

### 10.2 Variations

```
GET    /api/variation-templates               List variation templates
POST   /api/variation-templates               Create template
GET    /api/variation-templates/{id}          Get template with values
PUT    /api/variation-templates/{id}          Update template
DELETE /api/variation-templates/{id}          Delete template
POST   /api/variation-templates/{id}/values   Add value to template
DELETE /api/variation-templates/{id}/values/{vid} Remove value
```

### 10.3 Supporting Lookups

```
# Brands
GET    /api/brands                List
POST   /api/brands                Create   { name, description }
PUT    /api/brands/{id}           Update
DELETE /api/brands/{id}           Delete

# Units
GET    /api/units                 List
POST   /api/units                 Create   { actual_name, short_name, allow_decimal, base_unit_id, base_unit_multiplier }
PUT    /api/units/{id}            Update
DELETE /api/units/{id}            Delete

# Categories
GET    /api/categories            List (product categories only)
POST   /api/categories            Create   { name, short_code, parent_id, description }
PUT    /api/categories/{id}       Update
DELETE /api/categories/{id}       Delete

# Warranties
GET    /api/warranties            List
POST   /api/warranties            Create   { name, description, duration, duration_type }
PUT    /api/warranties/{id}       Update
DELETE /api/warranties/{id}       Delete

# Selling Price Groups
GET    /api/selling-price-groups            List
POST   /api/selling-price-groups            Create   { name, description }
PUT    /api/selling-price-groups/{id}       Update
DELETE /api/selling-price-groups/{id}       Delete
PATCH  /api/selling-price-groups/{id}/toggle-active  Toggle active state
GET    /api/selling-price-groups/export     Export prices
POST   /api/selling-price-groups/import     Import prices
```

### 10.4 Labels

```
POST   /api/labels/preview        Preview label layout
POST   /api/labels/print          Print labels (returns PDF/HTML)
```

---

### 10.5 Example Request/Response Bodies

#### Create Product (POST /api/products)
```json
{
  "name": "Widget Pro",
  "sku": "WGT-001",
  "type": "single",
  "unit_id": 1,
  "brand_id": 2,
  "category_id": 3,
  "sub_category_id": 7,
  "tax": 1,
  "tax_type": "exclusive",
  "enable_stock": 1,
  "alert_quantity": 5,
  "barcode_type": "C128",
  "warranty_id": 1,
  "weight": "1.5kg",
  "product_description": "High quality widget",
  "is_inactive": 0,
  "not_for_selling": 0,
  "expiry_period": null,
  "expiry_period_type": null,
  "enable_sr_no": 0,
  "product_custom_field1": "Custom Value",
  "location_ids": [1, 2],
  "variations": [
    {
      "name": "Default",
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

#### Create Variable Product Variations
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
          "sell_price_inc_tax": 20.00
        },
        {
          "name": "Blue",
          "variation_value_id": 6,
          "sub_sku": "TS-001-BLU",
          "default_purchase_price": 8.00,
          "sell_price_inc_tax": 20.00
        }
      ]
    }
  ]
}
```

#### Product Response (GET /api/products/{id})
```json
{
  "id": 1,
  "name": "Widget Pro",
  "sku": "WGT-001",
  "type": "single",
  "is_inactive": false,
  "not_for_selling": false,
  "enable_stock": true,
  "alert_quantity": 5,
  "barcode_type": "C128",
  "tax_type": "exclusive",
  "brand": { "id": 2, "name": "BrandX" },
  "unit": { "id": 1, "actual_name": "Piece", "short_name": "pc" },
  "category": { "id": 3, "name": "Electronics" },
  "sub_category": { "id": 7, "name": "Gadgets" },
  "warranty": { "id": 1, "name": "Standard", "duration": 12, "duration_type": "months" },
  "image_url": "http://example.com/storage/products/img.jpg",
  "product_variations": [
    {
      "id": 1,
      "name": "DUMMY",
      "is_dummy": true,
      "variations": [
        {
          "id": 1,
          "name": "Default",
          "sub_sku": "WGT-001-D",
          "default_purchase_price": 10.00,
          "dpp_inc_tax": 11.00,
          "profit_percent": 50,
          "default_sell_price": 15.00,
          "sell_price_inc_tax": 16.50,
          "stock": [
            { "location_id": 1, "location_name": "Main Store", "qty_available": 100 }
          ],
          "group_prices": [
            { "price_group_id": 1, "price_group_name": "Wholesale", "price_inc_tax": 14.00, "price_type": "fixed" }
          ]
        }
      ]
    }
  ],
  "locations": [1, 2],
  "rack_details": [
    { "location_id": 1, "rack": "A", "row": "3", "position": "5" }
  ],
  "custom_fields": {
    "product_custom_field1": "Custom Value"
  },
  "created_at": "2026-01-01T00:00:00Z",
  "updated_at": "2026-01-15T00:00:00Z"
}
```

---

## KEY FILE LOCATIONS

| Purpose | Path |
|---------|------|
| Product Model | `app/Product.php` |
| ProductVariation Model | `app/ProductVariation.php` |
| Variation Model | `app/Variation.php` |
| VariationTemplate Model | `app/VariationTemplate.php` |
| VariationValueTemplate Model | `app/VariationValueTemplate.php` |
| VariationLocationDetails Model | `app/VariationLocationDetails.php` |
| VariationGroupPrice Model | `app/VariationGroupPrice.php` |
| SellingPriceGroup Model | `app/SellingPriceGroup.php` |
| Unit Model | `app/Unit.php` |
| Brands Model | `app/Brands.php` |
| Category Model | `app/Category.php` |
| Warranty Model | `app/Warranty.php` |
| ProductRack Model | `app/ProductRack.php` |
| Product Controller | `app/Http/Controllers/ProductController.php` |
| Variation Template Controller | `app/Http/Controllers/VariationTemplateController.php` |
| Brand Controller | `app/Http/Controllers/BrandController.php` |
| Unit Controller | `app/Http/Controllers/UnitController.php` |
| Category Controller | `app/Http/Controllers/TaxonomyController.php` |
| Warranty Controller | `app/Http/Controllers/WarrantyController.php` |
| Selling Price Group Controller | `app/Http/Controllers/SellingPriceGroupController.php` |
| Import Products Controller | `app/Http/Controllers/ImportProductsController.php` |
| Import Opening Stock Controller | `app/Http/Controllers/ImportOpeningStockController.php` |
| Labels Controller | `app/Http/Controllers/LabelsController.php` |
| Product Utility | `app/Utils/ProductUtil.php` |
| Products Export | `app/Exports/ProductsExport.php` |
| Routes File | `routes/web.php` |
| Migrations | `database/migrations/` (37+ product-related files) |
| Product Views | `resources/views/product/` |
| Brand Views | `resources/views/brand/` |
| Unit Views | `resources/views/unit/` |
| Selling Price Group Views | `resources/views/selling_price_group/` |
| Label Views | `resources/views/labels/` |

---

*End of Documentation — Generated from full codebase analysis of c:\laragon\www\erp*
