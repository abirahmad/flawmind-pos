# Sales System - Complete Technical Documentation

This document provides a comprehensive overview of all sales-related features in the ERP system. Use this as a reference to build a similar sales system for another API project.

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Database Schema](#database-schema)
3. [Models & Relationships](#models--relationships)
4. [API Endpoints & Routes](#api-endpoints--routes)
5. [Controllers & Business Logic](#controllers--business-logic)
6. [Payment System](#payment-system)
7. [POS (Point of Sale) System](#pos-system)
8. [Invoice Management](#invoice-management)
9. [Sales Returns & Refunds](#sales-returns--refunds)
10. [Pricing & Discounts](#pricing--discounts)
11. [Customer Management](#customer-management)
12. [Reports & Analytics](#reports--analytics)
13. [Events & Notifications](#events--notifications)

---

## System Overview

### Transaction Types
The system handles multiple transaction types:
- `sell` - Regular sales
- `sell_return` - Sales returns/refunds
- `purchase` - Purchases from suppliers
- `purchase_return` - Purchase returns
- `expense` - Business expenses
- `stock_adjustment` - Stock adjustments
- `sell_transfer` / `purchase_transfer` - Stock transfers
- `opening_stock` - Initial stock
- `opening_balance` - Opening balance
- `payroll` - Payroll transactions
- `expense_refund` - Expense refunds
- `sales_order` - Sales orders
- `purchase_order` - Purchase orders

### Transaction Statuses
- `received` - Received
- `pending` - Pending
- `ordered` - Ordered
- `draft` - Draft (not finalized)
- `final` - Finalized
- `in_transit` - In transit (for transfers)
- `completed` - Completed

### Payment Statuses
- `paid` - Fully paid
- `due` - Payment due
- `partial` - Partially paid
- `overdue` - Overdue payment

### Sell Statuses (Sub-types)
- `final` - Finalized sale
- `draft` - Draft sale
- `quotation` - Price quotation
- `proforma` - Proforma invoice

---

## Database Schema

### 1. transactions (Main Sales Table)

```sql
CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    location_id INT UNSIGNED,
    type ENUM('purchase', 'sell', 'expense', 'stock_adjustment', 'sell_transfer', 'purchase_transfer', 'opening_stock', 'sell_return', 'opening_balance', 'purchase_return', 'payroll', 'expense_refund', 'sales_order', 'purchase_order'),
    status ENUM('received', 'pending', 'ordered', 'draft', 'final', 'in_transit', 'completed'),
    sub_status VARCHAR(255) NULL, -- quotation, proforma, etc.
    payment_status ENUM('paid', 'due', 'partial'),
    is_quotation TINYINT(1) DEFAULT 0,

    -- Contact Information
    contact_id INT UNSIGNED NOT NULL,
    customer_group_id INT UNSIGNED NULL,

    -- Invoice Details
    invoice_no VARCHAR(255),
    ref_no VARCHAR(255),
    source VARCHAR(255) NULL, -- Source of the sale (web, pos, api, woocommerce)

    -- Dates
    transaction_date DATETIME NOT NULL,

    -- Pricing
    total_before_tax DECIMAL(22, 4) DEFAULT 0,
    tax_id INT UNSIGNED NULL,
    tax_amount DECIMAL(22, 4) DEFAULT 0,
    discount_type ENUM('fixed', 'percentage'),
    discount_amount DECIMAL(22, 4) DEFAULT 0,
    final_total DECIMAL(22, 4) DEFAULT 0,

    -- Shipping
    shipping_details TEXT NULL,
    shipping_address TEXT NULL,
    shipping_status ENUM('ordered', 'packed', 'shipped', 'delivered', 'cancelled'),
    shipping_charges DECIMAL(22, 4) DEFAULT 0,
    delivered_to VARCHAR(255) NULL,
    delivery_person INT UNSIGNED NULL,
    shipping_custom_field_1 VARCHAR(255) NULL,
    shipping_custom_field_2 VARCHAR(255) NULL,
    shipping_custom_field_3 VARCHAR(255) NULL,
    shipping_custom_field_4 VARCHAR(255) NULL,
    shipping_custom_field_5 VARCHAR(255) NULL,

    -- Notes
    additional_notes TEXT NULL, -- Customer-visible notes
    staff_note TEXT NULL, -- Internal notes

    -- Custom Fields
    custom_field_1 VARCHAR(255) NULL,
    custom_field_2 VARCHAR(255) NULL,
    custom_field_3 VARCHAR(255) NULL,
    custom_field_4 VARCHAR(255) NULL,

    -- Commission & Sales Rep
    commission_agent INT UNSIGNED NULL,
    created_by INT UNSIGNED NOT NULL,

    -- Type Flags
    is_direct_sale TINYINT(1) DEFAULT 0,
    is_suspend TINYINT(1) DEFAULT 0, -- Suspended/hold sales

    -- Recurring Invoice Settings
    is_recurring TINYINT(1) DEFAULT 0,
    recur_interval INT DEFAULT 1,
    recur_interval_type ENUM('days', 'months', 'years'),
    recur_repetitions INT DEFAULT 0,
    recur_parent_id INT UNSIGNED NULL,
    subscription_no VARCHAR(255) NULL,
    subscription_repeat_on INT NULL,

    -- Payment Terms
    pay_term_number INT NULL,
    pay_term_type ENUM('days', 'months'),

    -- Selling Price Group
    selling_price_group_id INT UNSIGNED NULL,

    -- Exchange Rate (Multi-currency)
    exchange_rate DECIMAL(20, 3) DEFAULT 1,

    -- Document Attachment
    document VARCHAR(255) NULL,

    -- Reward Points
    rp_earned INT DEFAULT 0,
    rp_redeemed INT DEFAULT 0,
    rp_redeemed_amount DECIMAL(22, 4) DEFAULT 0,

    -- Types of Service (Restaurant)
    types_of_service_id INT UNSIGNED NULL,
    packing_charge DECIMAL(22, 4) DEFAULT 0,
    packing_charge_type ENUM('fixed', 'percent'),
    service_custom_field_1 to 6 VARCHAR(255) NULL,

    -- Restaurant Features
    res_table_id INT UNSIGNED NULL,
    res_waiter_id INT UNSIGNED NULL,
    is_kitchen_order TINYINT(1) DEFAULT 0,

    -- Additional Expenses
    additional_expense_key_1 to 4 VARCHAR(255) NULL,
    additional_expense_value_1 to 4 DECIMAL(22, 4) DEFAULT 0,

    -- Round Off
    round_off_amount DECIMAL(22, 4) DEFAULT 0,

    -- Order Addresses (JSON)
    order_addresses TEXT NULL,

    -- Sales Order Reference
    sales_order_ids JSON NULL,
    purchase_order_ids JSON NULL,

    -- Return Reference
    return_parent_id INT UNSIGNED NULL,

    -- Export Sales
    is_export TINYINT(1) DEFAULT 0,
    export_custom_fields_info JSON NULL,

    -- API/Import Tracking
    is_created_from_api TINYINT(1) DEFAULT 0,
    import_batch INT NULL,
    import_time DATETIME NULL,

    -- Preferred Payment
    prefer_payment_method VARCHAR(255) NULL,
    prefer_payment_account INT UNSIGNED NULL,

    -- Invoice Token (for public invoice URL)
    invoice_token VARCHAR(255) UNIQUE NULL,

    -- WooCommerce Integration
    woocommerce_order_id INT NULL,

    -- CRM
    crm_is_order_request TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    -- Indexes
    INDEX idx_business (business_id),
    INDEX idx_type (type),
    INDEX idx_contact (contact_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_created_by (created_by),
    INDEX idx_location (location_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
);
```

### 2. transaction_sell_lines (Line Items)

```sql
CREATE TABLE transaction_sell_lines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    variation_id INT UNSIGNED NOT NULL,
    quantity DECIMAL(22, 4) DEFAULT 0,
    secondary_unit_quantity DECIMAL(22, 4) DEFAULT 0,

    -- Pricing
    unit_price_before_discount DECIMAL(22, 4),
    unit_price DECIMAL(22, 4), -- Sell price excluding tax
    unit_price_inc_tax DECIMAL(22, 4), -- Sell price including tax

    -- Line Level Discount
    line_discount_type ENUM('fixed', 'percentage'),
    line_discount_amount DECIMAL(22, 4) DEFAULT 0,

    -- Tax
    item_tax DECIMAL(22, 4) DEFAULT 0, -- Tax for one quantity
    tax_id INT UNSIGNED NULL,

    -- Unit
    sub_unit_id INT UNSIGNED NULL,

    -- Lot/Batch Number
    lot_no_line_id INT UNSIGNED NULL,

    -- Notes
    sell_line_note TEXT NULL,

    -- For Combo/Modifier products
    parent_sell_line_id INT UNSIGNED NULL,
    children_type ENUM('modifier', 'combo'),

    -- Discount Reference
    discount_id INT UNSIGNED NULL,

    -- Restaurant Service Staff
    res_service_staff_id INT UNSIGNED NULL,
    res_line_order_status ENUM('received', 'cooked', 'served'),

    -- Sales Order Line Reference
    so_line_id INT UNSIGNED NULL,
    so_quantity_invoiced DECIMAL(22, 4) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variation_id) REFERENCES variations(id) ON DELETE CASCADE
);
```

### 3. transaction_payments (Payments)

```sql
CREATE TABLE transaction_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NULL, -- NULL for advance payments
    business_id INT UNSIGNED NOT NULL,

    -- Payment Details
    amount DECIMAL(22, 4) DEFAULT 0,
    method ENUM('cash', 'card', 'cheque', 'bank_transfer', 'other', 'custom_pay_1', 'custom_pay_2', 'custom_pay_3', 'custom_pay_4', 'custom_pay_5', 'custom_pay_6', 'custom_pay_7', 'advance'),
    payment_for INT UNSIGNED NULL, -- Contact ID for advance payments

    -- Card Payment Details
    card_transaction_number VARCHAR(255) NULL,
    card_number VARCHAR(255) NULL,
    card_type ENUM('visa', 'master', 'amex', 'discover', 'other'),
    card_holder_name VARCHAR(255) NULL,
    card_month VARCHAR(255) NULL,
    card_year VARCHAR(255) NULL,
    card_security VARCHAR(5) NULL,

    -- Cheque Details
    cheque_number VARCHAR(255) NULL,

    -- Bank Transfer Details
    bank_account_number VARCHAR(255) NULL,

    -- Transaction Reference
    transaction_no VARCHAR(255) NULL,

    -- Payment Reference
    payment_ref_no VARCHAR(255) NULL,

    -- Account Integration
    account_id INT UNSIGNED NULL,

    -- Parent Payment (for split payments)
    parent_id INT UNSIGNED NULL,

    -- Return Flag
    is_return TINYINT(1) DEFAULT 0, -- For change return

    -- Document
    document VARCHAR(255) NULL,

    -- Note
    note TEXT NULL,

    -- Payment Date
    paid_on DATETIME,

    -- Creator
    created_by INT UNSIGNED,

    -- For advance payment allocation
    is_advance TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);
```

### 4. transaction_sell_lines_purchase_lines (Stock Mapping)

```sql
CREATE TABLE transaction_sell_lines_purchase_lines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sell_line_id INT UNSIGNED NOT NULL,
    purchase_line_id INT UNSIGNED NOT NULL,
    quantity DECIMAL(22, 4) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (sell_line_id) REFERENCES transaction_sell_lines(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_line_id) REFERENCES purchase_lines(id) ON DELETE CASCADE
);
```

### 5. contacts (Customers/Suppliers)

```sql
CREATE TABLE contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    type ENUM('supplier', 'customer', 'both', 'lead'),
    supplier_business_name VARCHAR(255) NULL,

    -- Name
    prefix VARCHAR(25) NULL,
    first_name VARCHAR(255),
    middle_name VARCHAR(255) NULL,
    last_name VARCHAR(255) NULL,
    name VARCHAR(255), -- Full name

    -- Contact Info
    email VARCHAR(255) NULL,
    contact_id VARCHAR(255) NULL, -- Custom contact ID
    mobile VARCHAR(255),
    landline VARCHAR(255) NULL,
    alternate_number VARCHAR(255) NULL,

    -- Address
    address_line_1 VARCHAR(255) NULL,
    address_line_2 VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    state VARCHAR(255) NULL,
    country VARCHAR(255) NULL,
    zip_code VARCHAR(255) NULL,

    -- Tax Info
    tax_number VARCHAR(255) NULL,

    -- Credit Settings
    credit_limit DECIMAL(22, 4) NULL,
    pay_term_number INT NULL,
    pay_term_type ENUM('days', 'months'),

    -- Customer Group
    customer_group_id INT UNSIGNED NULL,

    -- Default Status
    is_default TINYINT(1) DEFAULT 0,
    contact_status ENUM('active', 'inactive') DEFAULT 'active',

    -- Shipping Address (JSON)
    shipping_address TEXT NULL,
    shipping_custom_field_details JSON NULL,

    -- Position/Department
    position VARCHAR(255) NULL,
    department VARCHAR(255) NULL,

    -- Opening Balance
    opening_balance DECIMAL(22, 4) DEFAULT 0,
    opening_balance_paid DECIMAL(22, 4) DEFAULT 0,

    -- Reward Points
    total_rp INT DEFAULT 0,
    total_rp_used INT DEFAULT 0,
    total_rp_expired INT DEFAULT 0,

    -- Custom Fields
    custom_field1 to 10 VARCHAR(255) NULL,

    -- DOB/Anniversary
    dob DATE NULL,

    -- Password (for customer login)
    password VARCHAR(255) NULL,

    -- Creator
    created_by INT UNSIGNED,

    -- Conversion Info
    converted_by INT UNSIGNED NULL,
    converted_on DATETIME NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_business (business_id),
    INDEX idx_type (type),
    INDEX idx_name (name),
    INDEX idx_contact_id (contact_id)
);
```

### 6. products

```sql
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('single', 'variable', 'combo', 'modifier'),

    -- Identification
    sku VARCHAR(255) NOT NULL,
    barcode_type ENUM('C128', 'C39', 'EAN13', 'EAN8', 'UPCA', 'UPCE'),

    -- Classification
    brand_id INT UNSIGNED NULL,
    category_id INT UNSIGNED NULL,
    sub_category_id INT UNSIGNED NULL,
    unit_id INT UNSIGNED NOT NULL,
    secondary_unit_id INT UNSIGNED NULL,
    sub_unit_ids JSON NULL,

    -- Tax
    tax INT UNSIGNED NULL,
    tax_type ENUM('inclusive', 'exclusive'),

    -- Stock Management
    enable_stock TINYINT(1) DEFAULT 0,
    alert_quantity DECIMAL(22, 4) DEFAULT 0,

    -- Serial Number
    enable_sr_no TINYINT(1) DEFAULT 0,

    -- Expiry
    expiry_period DECIMAL(4, 2) NULL,
    expiry_period_type ENUM('days', 'months'),

    -- Flags
    is_inactive TINYINT(1) DEFAULT 0,
    not_for_selling TINYINT(1) DEFAULT 0,

    -- Image
    image VARCHAR(255) NULL,

    -- Description
    product_description TEXT NULL,

    -- Weight (for shipping)
    weight VARCHAR(255) NULL,

    -- Warranty
    warranty_id INT UNSIGNED NULL,

    -- Custom Fields
    product_custom_field1 to 4 VARCHAR(255) NULL,

    -- Preparation Time (Restaurant)
    preparation_time_in_minutes INT NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_business (business_id),
    INDEX idx_name (name),
    INDEX idx_sku (sku),
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id)
);
```

### 7. variations

```sql
CREATE TABLE variations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_variation_id INT UNSIGNED NOT NULL,
    sub_sku VARCHAR(255) NULL,

    -- Pricing
    default_purchase_price DECIMAL(22, 4),
    dpp_inc_tax DECIMAL(22, 4), -- Default purchase price inc tax
    profit_percent DECIMAL(22, 4) DEFAULT 0,
    default_sell_price DECIMAL(22, 4),
    sell_price_inc_tax DECIMAL(22, 4),

    -- Combo Product Variations (JSON)
    combo_variations JSON NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

### 8. discounts

```sql
CREATE TABLE discounts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,

    -- Discount Details
    discount_type ENUM('fixed', 'percentage'),
    discount_amount DECIMAL(22, 4),

    -- Validity Period
    starts_at DATETIME NULL,
    ends_at DATETIME NULL,

    -- Status
    is_active TINYINT(1) DEFAULT 1,

    -- Application Rules
    applicable_in_pos TINYINT(1) DEFAULT 1,
    applicable_in_categories TINYINT(1) DEFAULT 0,
    applicable_in_cg TINYINT(1) DEFAULT 0, -- Customer Groups

    -- Priority
    priority INT DEFAULT 0,

    -- Location
    location_id INT UNSIGNED NULL,

    -- Brand Restriction
    brand_id INT UNSIGNED NULL,

    -- Category Restriction
    category_id INT UNSIGNED NULL,

    -- Minimum Spend
    min_order_amount DECIMAL(22, 4) NULL,
    max_discount_amount DECIMAL(22, 4) NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 9. selling_price_groups

```sql
CREATE TABLE selling_price_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### 10. customer_groups

```sql
CREATE TABLE customer_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    amount DECIMAL(5, 2) DEFAULT 0, -- Discount percentage
    price_calculation_type ENUM('percentage', 'selling_price_group') DEFAULT 'percentage',
    selling_price_group_id INT UNSIGNED NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### 11. invoice_schemes

```sql
CREATE TABLE invoice_schemes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    scheme_type ENUM('blank', 'year'),
    prefix VARCHAR(255) NULL,
    start_number INT,
    invoice_count INT DEFAULT 0,
    total_digits INT,
    is_default TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 12. invoice_layouts

```sql
CREATE TABLE invoice_layouts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,

    -- Header
    header_text TEXT NULL,
    logo VARCHAR(255) NULL,
    show_business_name TINYINT(1) DEFAULT 1,
    show_location_name TINYINT(1) DEFAULT 1,
    show_landmark TINYINT(1) DEFAULT 1,
    show_city TINYINT(1) DEFAULT 1,
    show_state TINYINT(1) DEFAULT 1,
    show_zip_code TINYINT(1) DEFAULT 1,
    show_country TINYINT(1) DEFAULT 1,
    show_mobile_number TINYINT(1) DEFAULT 1,
    show_alternate_number TINYINT(1) DEFAULT 0,
    show_email TINYINT(1) DEFAULT 0,
    show_tax_1 TINYINT(1) DEFAULT 1,
    show_tax_2 TINYINT(1) DEFAULT 0,

    -- Invoice Info
    invoice_no_prefix VARCHAR(255) NULL,
    quotation_no_prefix VARCHAR(255) NULL,
    invoice_heading VARCHAR(255) NULL,
    sub_heading_line1 to 5 VARCHAR(255) NULL,
    invoice_heading_not_paid VARCHAR(255) NULL,
    invoice_heading_paid VARCHAR(255) NULL,

    -- Customer Info
    show_client_id TINYINT(1) DEFAULT 0,
    client_id_label VARCHAR(255) NULL,
    client_tax_label VARCHAR(255) NULL,

    -- Product Info
    show_brand TINYINT(1) DEFAULT 0,
    show_sku TINYINT(1) DEFAULT 1,
    show_cat_code TINYINT(1) DEFAULT 0,
    show_expiry TINYINT(1) DEFAULT 0,
    show_lot TINYINT(1) DEFAULT 0,
    show_image TINYINT(1) DEFAULT 0,
    show_sale_description TINYINT(1) DEFAULT 0,

    -- Price Display
    show_previous_bal TINYINT(1) DEFAULT 0,
    prev_bal_label VARCHAR(255) NULL,

    -- Tax Display
    highlight_service_staff TINYINT(1) DEFAULT 0,
    show_tax_row TINYINT(1) DEFAULT 1,
    tax_position ENUM('inline', 'bottom', 'none'),

    -- Totals
    show_payments TINYINT(1) DEFAULT 1,
    show_customer TINYINT(1) DEFAULT 1,

    -- Footer
    footer_text TEXT NULL,

    -- Design
    design ENUM('classic', 'elegant', 'detailed', 'columnar', 'slim') DEFAULT 'classic',

    -- Module Specific
    cn_heading VARCHAR(255) NULL,
    cn_no_label VARCHAR(255) NULL,
    cn_amount_label VARCHAR(255) NULL,

    -- Table Columns
    table_product_label VARCHAR(255) NULL,
    table_qty_label VARCHAR(255) NULL,
    table_unit_price_label VARCHAR(255) NULL,
    table_subtotal_label VARCHAR(255) NULL,

    -- Custom Labels
    -- ... many more label customization fields

    is_default TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 13. cash_registers

```sql
CREATE TABLE cash_registers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    location_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    status ENUM('open', 'close'),
    closed_at DATETIME NULL,
    closing_amount DECIMAL(22, 4) DEFAULT 0,
    total_card_slips INT DEFAULT 0,
    total_cheques INT DEFAULT 0,
    closing_note TEXT NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 14. cash_register_transactions

```sql
CREATE TABLE cash_register_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cash_register_id INT UNSIGNED NOT NULL,
    amount DECIMAL(22, 4) DEFAULT 0,
    pay_method ENUM('cash', 'card', 'cheque', 'bank_transfer', 'other', 'advance', 'custom_pay_1', 'custom_pay_2', 'custom_pay_3'),
    type ENUM('debit', 'credit'),
    transaction_type ENUM('initial', 'sell', 'transfer', 'refund'),
    transaction_id INT UNSIGNED NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE CASCADE
);
```

---

## Models & Relationships

### Transaction Model

**File:** `app/Transaction.php`

```php
class Transaction extends Model
{
    // Relationships
    public function purchase_lines()      // HasMany PurchaseLine
    public function sell_lines()          // HasMany TransactionSellLine
    public function contact()             // BelongsTo Contact
    public function payment_lines()       // HasMany TransactionPayment
    public function location()            // BelongsTo BusinessLocation
    public function business()            // BelongsTo Business
    public function tax()                 // BelongsTo TaxRate
    public function sales_person()        // BelongsTo User (created_by)
    public function sale_commission_agent() // BelongsTo User (commission_agent)
    public function return_parent()       // HasOne Transaction
    public function return_parent_sell()  // BelongsTo Transaction
    public function table()               // BelongsTo ResTable
    public function service_staff()       // BelongsTo User
    public function recurring_invoices()  // HasMany Transaction
    public function recurring_parent()    // HasOne Transaction
    public function price_group()         // BelongsTo SellingPriceGroup
    public function types_of_service()    // BelongsTo TypesOfService
    public function cash_register_payments() // HasMany CashRegisterTransaction
    public function media()               // MorphMany Media
    public function salesOrders()         // Custom method to get linked sales orders

    // Scopes
    public function scopeOverDue($query)  // Filter overdue transactions

    // Static Methods
    public static function discountTypes()       // Returns discount types
    public static function transactionTypes()    // Returns transaction types
    public static function getPaymentStatus($transaction) // Calculate payment status
    public static function getSellStatuses()     // Returns sell statuses
    public static function sales_order_statuses() // Returns sales order statuses

    // Accessors
    public function getDueDateAttribute()        // Calculate due date
    public function getDocumentPathAttribute()   // Get document path
    public function getDocumentNameAttribute()   // Get document name
    public function shipping_address($array)     // Get shipping address
    public function billing_address($array)      // Get billing address
}
```

### TransactionSellLine Model

**File:** `app/TransactionSellLine.php`

```php
class TransactionSellLine extends Model
{
    // Relationships
    public function transaction()         // BelongsTo Transaction
    public function product()             // BelongsTo Product
    public function variations()          // BelongsTo Variation
    public function modifiers()           // HasMany TransactionSellLine (children)
    public function sell_line_purchase_lines() // HasMany TransactionSellLinesPurchaseLines
    public function lot_details()         // BelongsTo PurchaseLine
    public function sub_unit()            // BelongsTo Unit
    public function service_staff()       // BelongsTo User
    public function warranties()          // BelongsToMany Warranty
    public function line_tax()            // BelongsTo TaxRate
    public function so_line()             // BelongsTo TransactionSellLine

    // Methods
    public function get_discount_amount() // Calculate line discount amount
}
```

### TransactionPayment Model

**File:** `app/TransactionPayment.php`

```php
class TransactionPayment extends Model
{
    // Relationships
    public function payment_account()     // BelongsTo Account
    public function transaction()         // BelongsTo Transaction
    public function created_user()        // BelongsTo User
    public function child_payments()      // HasMany TransactionPayment
    public function denominations()       // MorphMany CashDenomination

    // Static Methods
    public static function deletePayment($payment) // Delete payment and update related records
}
```

### Contact Model (Customer/Supplier)

**File:** `app/Contact.php`

```php
class Contact extends Authenticatable
{
    // Scopes
    public function scopeActive($query)
    public function scopeOnlySuppliers($query)
    public function scopeOnlyCustomers($query)
    public function scopeOnlyOwnContact($query)

    // Relationships
    public function business()            // BelongsTo Business
    public function documentsAndnote()    // MorphMany DocumentAndNote
    public function userHavingAccess()    // BelongsToMany User

    // Static Methods
    public static function contactDropdown()
    public static function suppliersDropdown()
    public static function customersDropdown()
    public static function typeDropdown()
    public static function getContactTypes()

    // Accessors
    public function getContactAddressAttribute()
    public function getFullNameAttribute()
    public function getFullNameWithBusinessAttribute()
    public function getContactAddressArrayAttribute()
}
```

### Product Model

**File:** `app/Product.php`

```php
class Product extends Model
{
    // Relationships
    public function product_variations()  // HasMany ProductVariation
    public function brand()               // BelongsTo Brands
    public function unit()                // BelongsTo Unit
    public function second_unit()         // BelongsTo Unit
    public function category()            // BelongsTo Category
    public function sub_category()        // BelongsTo Category
    public function product_tax()         // BelongsTo TaxRate
    public function variations()          // HasMany Variation
    public function modifier_products()   // BelongsToMany Product
    public function modifier_sets()       // BelongsToMany Product
    public function purchase_lines()      // HasMany PurchaseLine
    public function product_locations()   // BelongsToMany BusinessLocation
    public function warranty()            // BelongsTo Warranty
    public function media()               // MorphMany Media
    public function rack_details()        // HasMany ProductRack

    // Scopes
    public function scopeActive($query)
    public function scopeInactive($query)
    public function scopeProductForSales($query)
    public function scopeProductNotForSales($query)
    public function scopeForLocation($query, $location_id)

    // Accessors
    public function getImageUrlAttribute()
    public function getImagePathAttribute()
}
```

---

## API Endpoints & Routes

### Sales Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/sells` | SellController@index | List all sales |
| GET | `/sells/create` | SellController@create | Create sale form |
| POST | `/sells` | SellController@store | Store new sale |
| GET | `/sells/{id}` | SellController@show | View sale details |
| GET | `/sells/{id}/edit` | SellController@edit | Edit sale form |
| PUT | `/sells/{id}` | SellController@update | Update sale |
| DELETE | `/sells/{id}` | SellPosController@destroy | Delete sale |
| GET | `/sells/drafts` | SellController@getDrafts | List draft sales |
| GET | `/sells/quotations` | SellController@getQuotations | List quotations |
| GET | `/sells/duplicate/{id}` | SellController@duplicateSell | Duplicate sale |
| GET | `/sells/convert-to-draft/{id}` | SellPosController@convertToInvoice | Convert to invoice |
| GET | `/sells/convert-to-proforma/{id}` | SellPosController@convertToProforma | Convert to proforma |

### POS Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/pos` | SellPosController@index | POS list |
| GET | `/pos/create` | SellPosController@create | POS screen |
| POST | `/pos` | SellPosController@store | Create POS sale |
| GET | `/pos/{id}/edit` | SellPosController@edit | Edit POS sale |
| PUT | `/pos/{id}` | SellPosController@update | Update POS sale |
| GET | `/sells/pos/get_product_row/{variation_id}/{location_id}` | SellPosController@getProductRow | Get product for POS |
| POST | `/sells/pos/get_payment_row` | SellPosController@getPaymentRow | Get payment row |
| POST | `/sells/pos/get-reward-details` | SellPosController@getRewardDetails | Get reward points |
| GET | `/sells/pos/get-recent-transactions` | SellPosController@getRecentTransactions | Recent transactions |
| GET | `/sells/pos/get-product-suggestion` | SellPosController@getProductSuggestion | Product search |
| GET | `/sells/pos/get-featured-products/{location_id}` | SellPosController@getFeaturedProducts | Featured products |

### Sales Order Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/sales-order` | SalesOrderController@index | List sales orders |
| GET | `/get-sales-orders/{customer_id}` | SalesOrderController@getSalesOrders | Get customer's orders |
| GET | `/get-sales-order-lines` | SellPosController@getSalesOrderLines | Get order lines |
| GET | `/edit-sales-orders/{id}/status` | SalesOrderController@getEditSalesOrderStatus | Edit order status |
| PUT | `/update-sales-orders/{id}/status` | SalesOrderController@postEditSalesOrderStatus | Update order status |

### Payment Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/payments/add_payment/{transaction_id}` | TransactionPaymentController@addPayment | Add payment form |
| POST | `/payments` | TransactionPaymentController@store | Store payment |
| GET | `/payments/{id}` | TransactionPaymentController@show | View payments |
| PUT | `/payments/{id}` | TransactionPaymentController@update | Update payment |
| DELETE | `/payments/{id}` | TransactionPaymentController@destroy | Delete payment |
| GET | `/payments/pay-contact-due/{contact_id}` | TransactionPaymentController@getPayContactDue | Pay contact due |
| POST | `/payments/pay-contact-due` | TransactionPaymentController@postPayContactDue | Process contact payment |
| GET | `/payments/view-payment/{payment_id}` | TransactionPaymentController@viewPayment | View single payment |

### Sell Return Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/sell-return` | SellReturnController@index | List returns |
| GET | `/sell-return/add/{id}` | SellReturnController@add | Add return form |
| POST | `/sell-return` | SellReturnController@store | Store return |
| GET | `/sell-return/{id}` | SellReturnController@show | View return |
| GET | `/sell-return/print/{id}` | SellReturnController@printInvoice | Print return |
| GET | `/validate-invoice-to-return/{invoice_no}` | SellReturnController@validateInvoiceToReturn | Validate for return |

### Invoice & Print Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/sells/{transaction_id}/print` | SellPosController@printInvoice | Print invoice |
| GET | `/download-sells/{transaction_id}/pdf` | SellPosController@downloadPdf | Download PDF |
| GET | `/download-quotation/{id}/pdf` | SellPosController@downloadQuotationPdf | Download quotation |
| GET | `/download-packing-list/{id}/pdf` | SellPosController@downloadPackingListPdf | Download packing list |
| GET | `/sells/invoice-url/{id}` | SellPosController@showInvoiceUrl | Get invoice URL |
| GET | `/invoice/{token}` | SellPosController@showInvoice | Public invoice view |
| GET | `/quote/{token}` | SellPosController@showInvoice | Public quote view |

### Shipping Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/sells/edit-shipping/{id}` | SellController@editShipping | Edit shipping form |
| PUT | `/sells/update-shipping/{id}` | SellController@updateShipping | Update shipping |
| GET | `/shipments` | SellController@shipments | List shipments |

### Cash Register Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/cash-register` | CashRegisterController@index | List registers |
| GET | `/cash-register/create` | CashRegisterController@create | Open register |
| POST | `/cash-register` | CashRegisterController@store | Store register |
| GET | `/cash-register/register-details` | CashRegisterController@getRegisterDetails | Register details |
| GET | `/cash-register/close-register/{id}` | CashRegisterController@getCloseRegister | Close register form |
| POST | `/cash-register/close-register` | CashRegisterController@postCloseRegister | Close register |

---

## Controllers & Business Logic

### SellController

**File:** `app/Http/Controllers/SellController.php`

**Key Methods:**

1. **index()** - Display sales listing with filters
   - Filters: location, customer, date range, payment status, shipping status
   - DataTables server-side processing
   - Permission checks

2. **create()** - Show create sale form
   - Load business locations, taxes, payment types
   - Check subscription/quota limits
   - Load customer groups, price groups, invoice schemes

3. **edit($id)** - Edit sale
   - Check edit permission and time limit
   - Check if return exists
   - Load existing sell lines with details

4. **show($id)** - View sale details
   - Load transaction with all relationships
   - Calculate taxes, show payment history
   - Display activity logs

5. **getDrafts()** - List draft sales

6. **getQuotations()** - List quotations

7. **duplicateSell($id)** - Create copy of sale

8. **editShipping($id)** - Edit shipping details

9. **updateShipping($id)** - Update shipping

10. **shipments()** - List shipments

### SellPosController

**File:** `app/Http/Controllers/SellPosController.php`

**Key Methods:**

1. **create()** - POS Screen
   - Check cash register status
   - Load products, categories, brands
   - Load payment types, accounts
   - Handle keyboard shortcuts

2. **store(Request $request)** - Create Sale
   ```php
   // Key steps:
   1. Validate input
   2. Check customer credit limit
   3. Handle quotation/proforma status
   4. Calculate invoice total
   5. Create transaction record
   6. Create sell lines
   7. Process payments
   8. Update stock (if final)
   9. Map purchase-sell lines (FIFO/LIFO)
   10. Update reward points
   11. Send notifications
   12. Generate receipt
   ```

3. **update($id)** - Update Sale
   - Similar to store but handles existing records
   - Manages stock reversal and re-allocation

4. **destroy($id)** - Delete Sale
   - Reverse stock changes
   - Delete payments
   - Handle returns if exist

5. **getProductRow()** - Get product for POS
   - Return product details, pricing, stock

6. **getPaymentRow()** - Get payment form row

7. **receiptContent()** - Generate receipt HTML

8. **printInvoice()** - Print invoice

9. **downloadPdf()** - Generate PDF invoice

10. **showInvoice($token)** - Public invoice view

11. **toggleRecurringInvoices()** - Enable/disable recurring

12. **convertToInvoice($id)** - Convert draft to invoice

### TransactionUtil

**File:** `app/Utils/TransactionUtil.php`

**Key Methods:**

1. **createSellTransaction()** - Create transaction record

2. **updateSellTransaction()** - Update transaction record

3. **createOrUpdateSellLines()** - Handle sell line items

4. **createOrUpdatePaymentLines()** - Handle payments

5. **updatePaymentStatus()** - Calculate and update payment status

6. **mapPurchaseSell()** - Map sell lines to purchase lines (FIFO/LIFO)

7. **getInvoiceNumber()** - Generate invoice number

8. **calculateInvoiceTotal()** - Calculate totals with tax/discount

9. **getReceiptDetails()** - Get all receipt data

10. **payment_types()** - Get available payment methods

11. **shipping_statuses()** - Get shipping status options

12. **isCustomerCreditLimitExeeded()** - Check credit limit

13. **getContactDue()** - Get customer/supplier balance

14. **calculateRewardPoints()** - Calculate reward points

15. **updateCustomerRewardPoints()** - Update customer points

---

## Payment System

### Payment Methods

```php
$payment_methods = [
    'cash' => 'Cash',
    'card' => 'Card',
    'cheque' => 'Cheque',
    'bank_transfer' => 'Bank Transfer',
    'other' => 'Other',
    'advance' => 'Advance Payment',
    'custom_pay_1' => 'Custom Payment 1',  // Configurable
    'custom_pay_2' => 'Custom Payment 2',
    'custom_pay_3' => 'Custom Payment 3',
    'custom_pay_4' => 'Custom Payment 4',
    'custom_pay_5' => 'Custom Payment 5',
    'custom_pay_6' => 'Custom Payment 6',
    'custom_pay_7' => 'Custom Payment 7',
];
```

### Payment Processing Flow

```php
// 1. Create Payment
$payment = TransactionPayment::create([
    'transaction_id' => $transaction_id,
    'business_id' => $business_id,
    'amount' => $amount,
    'method' => $method,
    'paid_on' => $paid_on,
    'created_by' => $user_id,
    // Method specific fields...
]);

// 2. Update Transaction Payment Status
$payment_status = $this->updatePaymentStatus($transaction_id);
// Returns: 'paid', 'due', or 'partial'

// 3. Add to Cash Register (if POS)
$this->cashRegisterUtil->addSellPayments($transaction, $payments);

// 4. Trigger Account Transaction (if accounting enabled)
event(new TransactionPaymentAdded($payment));
```

### Payment Status Calculation

```php
public function updatePaymentStatus($transaction_id, $final_total = null)
{
    $total_paid = TransactionPayment::where('transaction_id', $transaction_id)
        ->where('is_return', 0)
        ->sum('amount');

    $total_return = TransactionPayment::where('transaction_id', $transaction_id)
        ->where('is_return', 1)
        ->sum('amount');

    $total_paid = $total_paid - $total_return;

    if ($total_paid >= $final_total) {
        return 'paid';
    } elseif ($total_paid > 0) {
        return 'partial';
    } else {
        return 'due';
    }
}
```

### Card Payment Fields

```php
$card_payment = [
    'card_transaction_number' => 'Transaction reference',
    'card_number' => 'Last 4 digits',
    'card_type' => 'visa|master|amex|discover|other',
    'card_holder_name' => 'Card holder name',
    'card_month' => 'Expiry month',
    'card_year' => 'Expiry year',
    'card_security' => 'CVV (not stored)',
];
```

### Online Payment Integration

The system supports:
- **Stripe** - For card payments
- **Razorpay** - For Indian payments
- **PayPal** - Via external integration
- **Pesapal** - For African markets
- **MyFatoorah** - For Middle East

---

## POS System

### POS Features

1. **Cash Register Management**
   - Open/Close register with initial cash
   - Track all transactions per register
   - End-of-day reconciliation

2. **Product Search**
   - Barcode scanning
   - Name/SKU search
   - Category/Brand filtering

3. **Quick Buttons**
   - Featured products
   - Recent products
   - Custom shortcuts

4. **Suspend/Resume Sales**
   - Park transactions
   - Resume later
   - Multiple suspended sales

5. **Keyboard Shortcuts**
   ```json
   {
     "pos": {
       "express_checkout": "shift+e",
       "pay_n_ckeckout": "shift+p",
       "draft": "shift+d",
       "cancel": "shift+c",
       "recent_product_quantity": "f2",
       "weighing_scale": null,
       "edit_discount": "shift+i",
       "edit_order_tax": "shift+t",
       "add_payment_row": "shift+r",
       "finalize_payment": "shift+f",
       "add_new_product": "f4"
     }
   }
   ```

6. **Receipt Printing**
   - Browser printing
   - Direct printer support (ESC/POS)
   - Multiple receipt designs

### POS Settings

```php
$pos_settings = [
    'amount_rounding_method' => 'none|round|floor|ceil',
    'disable_pay_checkout' => 0,
    'disable_draft' => 0,
    'disable_express_checkout' => 0,
    'hide_product_suggestion' => 0,
    'hide_recent_trans' => 0,
    'disable_discount' => 0,
    'disable_order_tax' => 0,
    'is_pos_subtotal_editable' => 0,
    'print_on_suspend' => 0,
    'show_invoice_scheme' => 0,
    'enable_sales_order' => 0,
    'cmmsn_calculation_type' => 'invoice_value|payment_received',
    'inline_service_staff' => 0,
    'enable_weighing_scale' => 0,
    'weighing_scale_setting' => [...],
];
```

### Stock Management in POS

```php
// Decrease stock on final sale
if ($product['enable_stock']) {
    $this->productUtil->decreaseProductQuantity(
        $product['product_id'],
        $product['variation_id'],
        $location_id,
        $quantity
    );
}

// Map purchase to sell (FIFO/LIFO)
$this->transactionUtil->mapPurchaseSell($business, $sell_lines, 'purchase');
```

---

## Invoice Management

### Invoice Number Generation

```php
public function getInvoiceNumber($business_id, $status, $location_id, $invoice_scheme_id = null, $sale_type = 'sell')
{
    // Get invoice scheme
    $scheme = InvoiceScheme::find($invoice_scheme_id);

    // Build invoice number
    $prefix = $scheme->prefix ?? '';

    if ($scheme->scheme_type == 'year') {
        $prefix .= date('Y');
    }

    $number = str_pad(
        $scheme->start_number + $scheme->invoice_count,
        $scheme->total_digits,
        '0',
        STR_PAD_LEFT
    );

    // Increment counter
    $scheme->invoice_count++;
    $scheme->save();

    return $prefix . $number;
}
```

### Invoice Layout Features

- **Header**: Logo, business name, address, contact
- **Customer Info**: Name, address, tax number
- **Product Table**: SKU, description, qty, price, tax, total
- **Summary**: Subtotal, discount, tax, shipping, total
- **Payment Info**: Method, reference, amount
- **Footer**: Terms, notes, signature

### Receipt Designs

1. **Classic** - Standard receipt format
2. **Elegant** - Modern design with borders
3. **Detailed** - Full product details
4. **Columnar** - Multi-column layout
5. **Slim** - Thermal printer optimized

---

## Sales Returns & Refunds

### Return Process

1. **Validate Original Invoice**
   ```php
   public function validateInvoiceToReturn($invoice_no)
   {
       $transaction = Transaction::where('invoice_no', $invoice_no)
           ->where('type', 'sell')
           ->where('status', 'final')
           ->first();

       // Check if already returned
       $return_exists = Transaction::where('return_parent_id', $transaction->id)->exists();

       return $transaction;
   }
   ```

2. **Create Return Transaction**
   ```php
   $return = Transaction::create([
       'type' => 'sell_return',
       'status' => 'final',
       'return_parent_id' => $original_transaction_id,
       // Other fields...
   ]);
   ```

3. **Return Stock**
   ```php
   $this->productUtil->updateProductQuantity(
       $location_id,
       $product_id,
       $variation_id,
       $quantity,
       0,  // No purchase line mapping
       null,
       false
   );
   ```

4. **Process Refund**
   ```php
   TransactionPayment::create([
       'transaction_id' => $return->id,
       'amount' => $refund_amount,
       'method' => $refund_method,
       'is_return' => 0,
   ]);
   ```

---

## Pricing & Discounts

### Selling Price Groups

```php
// Define multiple price levels
$price_groups = [
    'Retail',      // Standard pricing
    'Wholesale',   // Bulk buyers
    'VIP',         // Loyal customers
    'Distributor', // Business partners
];

// Price per variation per group
variation_group_prices (
    variation_id,
    price_group_id,
    price_inc_tax
)
```

### Discount Types

1. **Invoice Level Discount**
   - Fixed amount
   - Percentage

2. **Line Level Discount**
   - Per product discount
   - Fixed or percentage

3. **Customer Group Discount**
   - Automatic discount for group members
   - Percentage based

4. **Promotional Discounts**
   ```php
   $discount = [
       'name' => 'Summer Sale',
       'discount_type' => 'percentage',
       'discount_amount' => 10,
       'starts_at' => '2024-06-01',
       'ends_at' => '2024-08-31',
       'applicable_in_pos' => true,
       'brand_id' => null,  // All brands
       'category_id' => null,  // All categories
       'min_order_amount' => 100,
       'max_discount_amount' => 50,
   ];
   ```

### Tax Calculation

```php
// Tax can be:
$tax_type = 'inclusive';  // Price includes tax
$tax_type = 'exclusive';  // Tax added to price

// Tax groups supported (multiple taxes)
$tax_group = [
    ['name' => 'CGST', 'rate' => 9],
    ['name' => 'SGST', 'rate' => 9],
];
```

---

## Customer Management

### Customer Features

1. **Customer Types**
   - Customer only
   - Supplier only
   - Both (customer & supplier)
   - Lead (potential customer)

2. **Customer Groups**
   - Auto discount application
   - Price group assignment

3. **Credit Management**
   - Credit limit setting
   - Payment terms
   - Balance tracking

4. **Reward Points**
   ```php
   // Earn points
   $points_earned = floor($final_total / $reward_rate);

   // Redeem points
   $redeem_value = $points_redeemed * $redeem_rate;
   ```

5. **Ledger/Statement**
   - All transactions history
   - Running balance
   - Export to PDF/Excel

### Walk-in Customer

```php
// Default customer for quick sales
$walk_in = Contact::where('business_id', $business_id)
    ->where('is_default', 1)
    ->where('type', 'customer')
    ->first();
```

---

## Reports & Analytics

### Sales Reports

1. **Sale Report**
   - Daily/Weekly/Monthly/Yearly
   - By location, customer, category
   - Payment status breakdown

2. **Profit/Loss Report**
   - Gross profit
   - Net profit
   - By product, category, brand

3. **Product Sell Report**
   - Top selling products
   - Quantity and revenue
   - Stock vs sales

4. **Customer Report**
   - Sales by customer
   - Due amounts
   - Purchase history

5. **Sales Representative Report**
   - Sales by salesperson
   - Commission calculation
   - Target vs achievement

6. **Register Report**
   - Cash register summary
   - Payment method breakdown
   - Discrepancy tracking

### Report Filters

```php
$filters = [
    'location_id',
    'customer_id',
    'category_id',
    'brand_id',
    'product_id',
    'start_date',
    'end_date',
    'payment_status',
    'created_by',
    'commission_agent',
];
```

---

## Events & Notifications

### Events

```php
// Sales Events
SellCreatedOrModified::dispatch($transaction);

// Payment Events
TransactionPaymentAdded::dispatch($payment);
TransactionPaymentUpdated::dispatch($payment, $old_payment);
TransactionPaymentDeleted::dispatch($payment);

// Contact Events
ContactCreatedOrModified::dispatch($contact);
```

### Notification Templates

1. **New Sale** - Customer notification
2. **Payment Received** - Receipt
3. **Payment Reminder** - Due payment alert
4. **New Quotation** - Quote notification
5. **Shipment Status** - Delivery updates

### Notification Channels

- Email (SMTP, Mailgun, etc.)
- SMS (Twilio, Nexmo, etc.)
- WhatsApp (via API)

---

## API Response Formats

### Success Response

```json
{
    "success": 1,
    "msg": "Sale added successfully",
    "receipt": "<html>...</html>",
    "transaction_id": 123,
    "invoice_no": "INV-2024-0001"
}
```

### Error Response

```json
{
    "success": 0,
    "msg": "Error message here"
}
```

### Listing Response (DataTables)

```json
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 50,
    "data": [
        {
            "id": 1,
            "invoice_no": "INV-001",
            "transaction_date": "2024-01-15 10:30:00",
            "final_total": 1500.00,
            "payment_status": "paid",
            "action": "<button>...</button>"
        }
    ]
}
```

---

## Security & Permissions

### Permission List

```php
$permissions = [
    // Sales
    'sell.view',
    'sell.create',
    'sell.update',
    'sell.delete',
    'direct_sell.access',
    'direct_sell.view',
    'direct_sell.update',
    'direct_sell.delete',

    // POS
    'sell.payments',
    'edit_product_price_from_pos_screen',
    'edit_product_discount_from_pos_screen',
    'edit_pos_payment',
    'print_invoice',

    // Sales Orders
    'so.view_all',
    'so.view_own',
    'so.create',
    'so.update',
    'so.delete',

    // Drafts & Quotations
    'draft.view_all',
    'draft.view_own',
    'draft.update',
    'draft.delete',
    'quotation.view_all',
    'quotation.view_own',
    'quotation.update',
    'quotation.delete',

    // View Restrictions
    'view_own_sell_only',
    'view_paid_sells_only',
    'view_due_sells_only',
    'view_partial_sells_only',
    'view_overdue_sells_only',
    'view_commission_agent_sell',

    // Shipping
    'access_shipping',
    'access_own_shipping',
    'access_commission_agent_shipping',
    'access_pending_shipments_only',

    // Price Groups
    'access_default_selling_price',
    'selling_price_group.{id}',
];
```

---

## Configuration Options

### Business Settings Related to Sales

```php
$business_settings = [
    // Tax
    'default_sales_tax' => null,
    'enable_inline_tax' => 1,

    // Pricing
    'default_profit_percent' => 25,
    'item_addition_method' => 1,  // 1=FIFO, 2=LIFO

    // Invoice
    'transaction_edit_days' => 30,
    'default_sale_discount' => null,

    // Commission
    'sales_cmsn_agnt' => null,  // null|user|cmsn_agnt|logged_in_user

    // Stock
    'enable_product_expiry' => 0,
    'enable_lot_number' => 0,

    // Keyboard Shortcuts
    'keyboard_shortcuts' => '{"pos":{...}}',

    // POS Settings
    'pos_settings' => '{...}',

    // Reward Points
    'enable_rp' => 0,
    'rp_earning_percentage' => 1,
    'rp_redemption_rate' => 1,
    'min_order_value_for_rp' => 0,
    'rp_expiry_period' => null,
    'rp_expiry_type' => 'month',
];
```

---

## Implementation Checklist

### Core Features
- [ ] Transaction management (CRUD)
- [ ] Sell line items handling
- [ ] Multiple payment methods
- [ ] Tax calculation (inclusive/exclusive)
- [ ] Discount application (line/invoice level)
- [ ] Invoice number generation
- [ ] Receipt/Invoice printing

### POS Features
- [ ] Cash register management
- [ ] Product search/barcode scan
- [ ] Suspend/Resume sales
- [ ] Quick checkout
- [ ] Keyboard shortcuts
- [ ] Multiple payment splitting

### Stock Management
- [ ] Stock decrease on sale
- [ ] FIFO/LIFO mapping
- [ ] Location-wise stock
- [ ] Lot/Batch tracking

### Customer Features
- [ ] Customer management
- [ ] Customer groups
- [ ] Credit limit
- [ ] Payment terms
- [ ] Reward points

### Returns & Refunds
- [ ] Sell return processing
- [ ] Stock reversal
- [ ] Refund handling

### Reporting
- [ ] Sales reports
- [ ] Profit/Loss
- [ ] Customer ledger
- [ ] Cash register reports

### Integration
- [ ] Payment gateways
- [ ] Email/SMS notifications
- [ ] Accounting integration
- [ ] E-commerce sync

---

## Notes for API Implementation

1. **Authentication**: Use Laravel Sanctum or Passport for API authentication
2. **Validation**: Implement form request validation classes
3. **Response Format**: Use API resources for consistent JSON responses
4. **Pagination**: Implement cursor-based pagination for large datasets
5. **Caching**: Cache product data, tax rates, and settings
6. **Queue**: Use queues for notifications, PDF generation, stock updates
7. **Events**: Implement event-driven architecture for extensibility
8. **Logging**: Log all transactions for audit trail

---

*This documentation was generated from the ERP system codebase analysis.*
*Version: 1.0*
*Generated: January 2026*
