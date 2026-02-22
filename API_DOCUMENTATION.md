# FlawMind ERP API Documentation

**For Next.js Integration**

---

## Table of Contents

1. [API Overview](#api-overview)
2. [Authentication](#authentication)
3. [Base URL & Configuration](#base-url--configuration)
4. [Error Handling](#error-handling)
5. [API Endpoints](#api-endpoints)
   - [Authentication Endpoints](#authentication-endpoints)
   - [Contacts Management](#contacts-management)
   - [Sales Transactions](#sales-transactions)
   - [Sales Returns](#sales-returns)
   - [Payments](#payments)

---

## API Overview

- **API Version**: 1.0.0
- **Project**: FlawMind ERP System
- **Description**: Comprehensive API for enterprise resource planning with focus on sales, contacts, and payment management
- **Authentication**: Bearer Token (JWT)
- **Response Format**: JSON

---

## Authentication

### Security Scheme

**Type**: Bearer Token (JWT)

**Header Format**:
```
Authorization: Bearer {token}
```

### Token Handling

- Access tokens are issued upon login/registration
- Tokens should be stored securely in the client
- Include the token in all authenticated requests
- Refresh tokens to maintain sessions
- Logout to revoke tokens

---

## Base URL & Configuration

### Development Environment

```
Base URL: http://localhost:8000/api
API Prefix: /v1
Full API Endpoint: http://localhost:8000/api/v1
```

### Production Configuration

Update the following in your Next.js `.env.local`:

```env
NEXT_PUBLIC_API_BASE_URL=https://your-production-domain.com/api
NEXT_PUBLIC_API_VERSION=v1
```

### Example API Client Setup (Next.js)

```typescript
// lib/apiClient.ts
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000/api';
const API_VERSION = process.env.NEXT_PUBLIC_API_VERSION || 'v1';

export const apiClient = {
  baseURL: `${API_BASE_URL}/${API_VERSION}`,
  
  async request(method: string, endpoint: string, data?: any, token?: string) {
    const headers: HeadersInit = {
      'Content-Type': 'application/json',
    };
    
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    
    const response = await fetch(`${this.baseURL}${endpoint}`, {
      method,
      headers,
      body: data ? JSON.stringify(data) : undefined,
    });
    
    return response.json();
  },
  
  get(endpoint: string, token?: string) {
    return this.request('GET', endpoint, undefined, token);
  },
  
  post(endpoint: string, data: any, token?: string) {
    return this.request('POST', endpoint, data, token);
  },
  
  put(endpoint: string, data: any, token?: string) {
    return this.request('PUT', endpoint, data, token);
  },
  
  delete(endpoint: string, token?: string) {
    return this.request('DELETE', endpoint, undefined, token);
  },
};
```

---

## Error Handling

### Common HTTP Status Codes

| Status Code | Meaning | Example Response |
|-------------|---------|------------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Missing or invalid token |
| 403 | Forbidden | Account not allowed or inactive |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation error |
| 500 | Server Error | Internal server error |

### Error Response Format

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": ["Error detail 1", "Error detail 2"]
  }
}
```

### Successful Response Format

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data here
  }
}
```

---

## API Endpoints

### Authentication Endpoints

#### 1. Register User

**Endpoint**: `POST /v1/auth/register`

**Description**: Create a new user account and return access token

**Request Body**:
```json
{
  "business_id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Parameters**:
- `business_id` (integer, required): Business/Company ID
- `first_name` (string, required): User's first name
- `last_name` (string, optional): User's last name
- `username` (string, optional): Username
- `email` (string, required): User's email address
- `password` (string, required): User's password (minimum 8 characters recommended)
- `password_confirmation` (string, required): Must match password

**Success Response (201)**:
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "business_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "created_at": "2024-01-25T10:30:00Z"
    },
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer"
  }
}
```

**Error Response (422)**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

#### 2. Login User

**Endpoint**: `POST /v1/auth/login`

**Description**: Authenticate user and return access token

**Request Body**:
```json
{
  "business_id": 1,
  "email": "john@example.com",
  "password": "password123"
}
```

**Parameters**:
- `business_id` (integer, required): Business/Company ID
- `email` (string, required): User's email address
- `password` (string, required): User's password

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "business_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com"
    },
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer"
  }
}
```

**Error Response (403)**:
```json
{
  "success": false,
  "message": "Account not allowed to login or inactive"
}
```

**Error Response (422)**:
```json
{
  "message": "Invalid credentials",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---

#### 3. Get Authenticated User

**Endpoint**: `GET /v1/auth/user`

**Description**: Get the currently authenticated user details

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "business_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "username": "johndoe",
      "created_at": "2024-01-25T10:30:00Z",
      "updated_at": "2024-01-25T10:30:00Z"
    }
  }
}
```

**Error Response (401)**:
```json
{
  "message": "Unauthenticated."
}
```

---

#### 4. Logout User

**Endpoint**: `POST /v1/auth/logout`

**Description**: Revoke the current access token

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

#### 5. Logout from All Devices

**Endpoint**: `POST /v1/auth/logout-all`

**Description**: Revoke all access tokens for the authenticated user

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Successfully logged out from all devices"
}
```

---

#### 6. Refresh Access Token

**Endpoint**: `POST /v1/auth/refresh`

**Description**: Revoke current token and generate a new one

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer"
  }
}
```

---

### Contacts Management

#### 1. List Contacts

**Endpoint**: `GET /v1/sales/contacts`

**Description**: Get paginated list of contacts

**Query Parameters**:
- `type` (string, optional): Filter by type - `customer`, `supplier`, or `both`
- `search` (string, optional): Search by name, email, or mobile
- `per_page` (integer, optional): Results per page (default: 15)
- `page` (integer, optional): Page number

**Headers**:
```
Authorization: Bearer {token}
```

**Example Request**:
```
GET /v1/sales/contacts?type=customer&search=john&per_page=20&page=1
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "contacts": [
      {
        "id": 1,
        "type": "customer",
        "name": "John Doe",
        "first_name": "John",
        "last_name": "Doe",
        "mobile": "0311234567",
        "email": "john@example.com",
        "tax_number": "12345678",
        "address_line_1": "123 Main Street",
        "city": "New York",
        "state": "NY",
        "country": "USA",
        "zip_code": "10001",
        "credit_limit": 5000,
        "pay_term_number": 30,
        "pay_term_type": "days",
        "customer_group_id": 1,
        "created_at": "2024-01-25T10:30:00Z"
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 20,
      "current_page": 1,
      "last_page": 3
    }
  }
}
```

---

#### 2. Create Contact

**Endpoint**: `POST /v1/sales/contacts`

**Description**: Create a new customer or supplier

**Request Body**:
```json
{
  "type": "customer",
  "name": "John Doe",
  "first_name": "John",
  "last_name": "Doe",
  "mobile": "0311234567",
  "email": "john@example.com",
  "tax_number": "12345678",
  "address_line_1": "123 Main Street",
  "city": "New York",
  "state": "NY",
  "country": "USA",
  "zip_code": "10001",
  "credit_limit": 5000,
  "pay_term_number": 30,
  "pay_term_type": "days",
  "customer_group_id": 1
}
```

**Required Parameters**:
- `type` (string): `customer`, `supplier`, or `both`
- `name` (string): Full name
- `mobile` (string): Mobile number

**Optional Parameters**:
- `first_name` (string): First name
- `last_name` (string): Last name
- `email` (string): Email address
- `tax_number` (string): Tax ID number
- `address_line_1` (string): Address
- `city` (string): City
- `state` (string): State/Province
- `country` (string): Country
- `zip_code` (string): Postal code
- `credit_limit` (number): Credit limit amount
- `pay_term_number` (integer): Payment term number
- `pay_term_type` (string): `days` or `months`
- `customer_group_id` (integer): Customer group ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (201)**:
```json
{
  "success": true,
  "message": "Contact created successfully",
  "data": {
    "id": 1,
    "type": "customer",
    "name": "John Doe",
    "mobile": "0311234567",
    "email": "john@example.com",
    "created_at": "2024-01-25T10:30:00Z"
  }
}
```

---

#### 3. Get Contact Details

**Endpoint**: `GET /v1/sales/contacts/{id}`

**Description**: Get details of a specific contact

**Path Parameters**:
- `id` (integer, required): Contact ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "type": "customer",
    "name": "John Doe",
    "first_name": "John",
    "last_name": "Doe",
    "mobile": "0311234567",
    "email": "john@example.com",
    "tax_number": "12345678",
    "address_line_1": "123 Main Street",
    "city": "New York",
    "state": "NY",
    "country": "USA",
    "zip_code": "10001",
    "credit_limit": 5000,
    "pay_term_number": 30,
    "pay_term_type": "days",
    "customer_group_id": 1,
    "created_at": "2024-01-25T10:30:00Z",
    "updated_at": "2024-01-25T10:30:00Z"
  }
}
```

**Error Response (404)**:
```json
{
  "success": false,
  "message": "Contact not found"
}
```

---

#### 4. Update Contact

**Endpoint**: `PUT /v1/sales/contacts/{id}`

**Description**: Update an existing contact

**Path Parameters**:
- `id` (integer, required): Contact ID

**Request Body**: Same as Create Contact (all fields optional)

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Contact updated successfully",
  "data": {
    "id": 1,
    "type": "customer",
    "name": "John Doe",
    "mobile": "0311234567",
    "updated_at": "2024-01-25T11:45:00Z"
  }
}
```

---

#### 5. Delete Contact

**Endpoint**: `DELETE /v1/sales/contacts/{id}`

**Description**: Delete a contact (soft delete)

**Path Parameters**:
- `id` (integer, required): Contact ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Contact deleted successfully"
}
```

**Error Response (422)**:
```json
{
  "success": false,
  "message": "Cannot delete contact with transactions"
}
```

---

#### 6. Get Contact Transactions

**Endpoint**: `GET /v1/sales/contacts/{id}/transactions`

**Description**: Get all transactions for a contact

**Path Parameters**:
- `id` (integer, required): Contact ID

**Query Parameters**:
- `type` (string, optional): Filter by transaction type - `sell` or `sell_return`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "transactions": [
      {
        "id": 1,
        "type": "sell",
        "invoice_no": "INV-2024-001",
        "total_amount": 1500.00,
        "paid_amount": 1000.00,
        "due_amount": 500.00,
        "payment_status": "partial",
        "transaction_date": "2024-01-25T10:30:00Z"
      }
    ]
  }
}
```

---

#### 7. Get Contact Types

**Endpoint**: `GET /v1/sales/contacts/types`

**Description**: Get available contact types

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "types": [
      {
        "value": "customer",
        "label": "Customer"
      },
      {
        "value": "supplier",
        "label": "Supplier"
      },
      {
        "value": "both",
        "label": "Both Customer & Supplier"
      }
    ]
  }
}
```

---

### Sales Transactions

#### 1. List All Sales

**Endpoint**: `GET /v1/sales/sells`

**Description**: Get paginated list of all sales transactions

**Query Parameters**:
- `page` (integer, optional): Page number
- `per_page` (integer, optional): Results per page (default: 15)
- `contact_id` (integer, optional): Filter by contact
- `location_id` (integer, optional): Filter by location
- `payment_status` (string, optional): `paid`, `due`, or `partial`
- `start_date` (string, optional): Filter from date (YYYY-MM-DD)
- `end_date` (string, optional): Filter to date (YYYY-MM-DD)
- `search` (string, optional): Search by invoice number or contact name

**Headers**:
```
Authorization: Bearer {token}
```

**Example Request**:
```
GET /v1/sales/sells?payment_status=due&start_date=2024-01-01&per_page=20
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "sales": [
      {
        "id": 1,
        "invoice_no": "INV-2024-001",
        "contact_id": 1,
        "contact_name": "John Doe",
        "location_id": 1,
        "status": "final",
        "transaction_date": "2024-01-25T10:30:00Z",
        "total_items": 5,
        "subtotal": 1000.00,
        "discount_type": "percentage",
        "discount_amount": 50.00,
        "tax_amount": 150.00,
        "shipping_charges": 25.00,
        "total_amount": 1125.00,
        "paid_amount": 500.00,
        "due_amount": 625.00,
        "payment_status": "partial",
        "created_at": "2024-01-25T10:30:00Z"
      }
    ],
    "pagination": {
      "total": 150,
      "per_page": 20,
      "current_page": 1,
      "last_page": 8
    }
  }
}
```

---

#### 2. Create Sale

**Endpoint**: `POST /v1/sales/sells`

**Description**: Create a new sales transaction

**Request Body**:
```json
{
  "contact_id": 1,
  "location_id": 1,
  "status": "final",
  "transaction_date": "2024-01-25T10:30:00Z",
  "discount_type": "percentage",
  "discount_amount": 50,
  "tax_id": 1,
  "shipping_charges": 25,
  "sell_lines": [
    {
      "product_id": 1,
      "variation_id": 1,
      "quantity": 2,
      "unit_price": 500,
      "line_discount_type": "fixed",
      "line_discount_amount": 10
    }
  ],
  "payments": [
    {
      "amount": 500,
      "method": "cash"
    }
  ]
}
```

**Required Parameters**:
- `contact_id` (integer): Customer/Supplier contact ID
- `sell_lines` (array): Array of line items

**Sell Line Parameters**:
- `product_id` (integer, required): Product ID
- `variation_id` (integer, optional): Product variation ID
- `quantity` (number, required): Quantity
- `unit_price` (number, required): Price per unit
- `line_discount_type` (string, optional): `fixed` or `percentage`
- `line_discount_amount` (number, optional): Discount amount

**Optional Parameters**:
- `location_id` (integer): Location/Warehouse ID
- `status` (string): `draft` or `final` (default: `final`)
- `transaction_date` (string): Transaction date (ISO 8601)
- `discount_type` (string): `fixed` or `percentage`
- `discount_amount` (number): Total discount
- `tax_id` (integer): Tax configuration ID
- `shipping_charges` (number): Shipping cost
- `payments` (array): Initial payments

**Payment Parameters**:
- `amount` (number, required): Payment amount
- `method` (string, required): `cash`, `card`, `cheque`, `bank_transfer`, `other`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (201)**:
```json
{
  "success": true,
  "message": "Sale created successfully",
  "data": {
    "id": 1,
    "invoice_no": "INV-2024-001",
    "contact_id": 1,
    "status": "final",
    "total_amount": 1115.00,
    "paid_amount": 500.00,
    "due_amount": 615.00,
    "payment_status": "partial",
    "created_at": "2024-01-25T10:30:00Z"
  }
}
```

---

#### 3. Get Sale Details

**Endpoint**: `GET /v1/sales/sells/{id}`

**Description**: Get details of a specific sale

**Path Parameters**:
- `id` (integer, required): Sale ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "invoice_no": "INV-2024-001",
    "contact_id": 1,
    "contact_name": "John Doe",
    "location_id": 1,
    "status": "final",
    "transaction_date": "2024-01-25T10:30:00Z",
    "sell_lines": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Product Name",
        "variation_id": 1,
        "quantity": 2,
        "unit_price": 500,
        "line_total": 1000,
        "discount_type": "fixed",
        "discount_amount": 10,
        "line_total_after_discount": 990
      }
    ],
    "subtotal": 1000.00,
    "discount_type": "percentage",
    "discount_amount": 50.00,
    "tax_amount": 150.00,
    "shipping_charges": 25.00,
    "total_amount": 1125.00,
    "paid_amount": 500.00,
    "due_amount": 625.00,
    "payment_status": "partial",
    "payments": [
      {
        "id": 1,
        "amount": 500,
        "method": "cash",
        "paid_on": "2024-01-25T10:30:00Z"
      }
    ],
    "created_at": "2024-01-25T10:30:00Z",
    "updated_at": "2024-01-25T10:30:00Z"
  }
}
```

---

#### 4. Update Sale

**Endpoint**: `PUT /v1/sales/sells/{id}`

**Description**: Update an existing sales transaction

**Path Parameters**:
- `id` (integer, required): Sale ID

**Request Body**: Same as Create Sale (all fields optional)

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Sale updated successfully",
  "data": {
    "id": 1,
    "invoice_no": "INV-2024-001",
    "total_amount": 1125.00,
    "updated_at": "2024-01-25T11:45:00Z"
  }
}
```

---

#### 5. Delete Sale

**Endpoint**: `DELETE /v1/sales/sells/{id}`

**Description**: Delete a sales transaction

**Path Parameters**:
- `id` (integer, required): Sale ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Sale deleted successfully"
}
```

---

#### 6. Get Draft Sales

**Endpoint**: `GET /v1/sales/sells/drafts`

**Description**: Get list of draft sales

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "drafts": [
      {
        "id": 1,
        "invoice_no": "DRAFT-2024-001",
        "contact_id": 1,
        "status": "draft",
        "total_amount": 1125.00,
        "created_at": "2024-01-25T10:30:00Z"
      }
    ]
  }
}
```

---

#### 7. Get Quotations

**Endpoint**: `GET /v1/sales/sells/quotations`

**Description**: Get list of quotations

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "quotations": [
      {
        "id": 1,
        "quote_no": "QUOTE-2024-001",
        "contact_id": 1,
        "status": "draft",
        "total_amount": 1125.00,
        "created_at": "2024-01-25T10:30:00Z"
      }
    ]
  }
}
```

---

#### 8. Convert to Invoice

**Endpoint**: `POST /v1/sales/sells/{id}/convert-to-invoice`

**Description**: Convert a draft or quotation to final invoice

**Path Parameters**:
- `id` (integer, required): Sale ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Converted to invoice successfully",
  "data": {
    "id": 1,
    "invoice_no": "INV-2024-001",
    "status": "final",
    "converted_at": "2024-01-25T11:45:00Z"
  }
}
```

---

### Sales Returns

#### 1. List All Sell Returns

**Endpoint**: `GET /v1/sales/sell-returns`

**Description**: Get paginated list of all sell returns

**Query Parameters**:
- `page` (integer, optional): Page number
- `per_page` (integer, optional): Results per page (default: 15)

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "returns": [
      {
        "id": 1,
        "return_no": "RET-2024-001",
        "transaction_id": 1,
        "invoice_no": "INV-2024-001",
        "contact_id": 1,
        "contact_name": "John Doe",
        "transaction_date": "2024-01-25T10:30:00Z",
        "refund_amount": 250.00,
        "refund_method": "cash",
        "return_lines_count": 2,
        "created_at": "2024-01-25T10:30:00Z"
      }
    ],
    "pagination": {
      "total": 30,
      "per_page": 15,
      "current_page": 1,
      "last_page": 2
    }
  }
}
```

---

#### 2. Create Sell Return

**Endpoint**: `POST /v1/sales/sell-returns`

**Description**: Create a new sell return for a sale

**Request Body**:
```json
{
  "transaction_id": 1,
  "transaction_date": "2024-01-25T10:30:00Z",
  "additional_notes": "Customer returned damaged items",
  "return_lines": [
    {
      "sell_line_id": 1,
      "quantity": 1,
      "return_note": "Item was damaged"
    },
    {
      "sell_line_id": 2,
      "quantity": 2,
      "return_note": "Wrong color"
    }
  ],
  "refund_amount": 250.00,
  "refund_method": "cash"
}
```

**Required Parameters**:
- `transaction_id` (integer): Original sale transaction ID
- `return_lines` (array): Array of returned line items

**Return Line Parameters**:
- `sell_line_id` (integer, required): Original sale line ID
- `quantity` (number, required): Quantity returned
- `return_note` (string, optional): Reason for return

**Optional Parameters**:
- `transaction_date` (string): Return date (ISO 8601)
- `additional_notes` (string): Additional notes
- `refund_amount` (number): Refund amount
- `refund_method` (string): `cash`, `card`, `bank_transfer`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (201)**:
```json
{
  "success": true,
  "message": "Sell return created successfully",
  "data": {
    "id": 1,
    "return_no": "RET-2024-001",
    "transaction_id": 1,
    "refund_amount": 250.00,
    "created_at": "2024-01-25T10:30:00Z"
  }
}
```

**Error Response (404)**:
```json
{
  "success": false,
  "message": "Original sale not found"
}
```

---

#### 3. Get Sell Return Details

**Endpoint**: `GET /v1/sales/sell-returns/{id}`

**Description**: Get details of a specific sell return

**Path Parameters**:
- `id` (integer, required): Sell return ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "return_no": "RET-2024-001",
    "transaction_id": 1,
    "invoice_no": "INV-2024-001",
    "contact_id": 1,
    "contact_name": "John Doe",
    "transaction_date": "2024-01-25T10:30:00Z",
    "additional_notes": "Customer returned damaged items",
    "return_lines": [
      {
        "id": 1,
        "sell_line_id": 1,
        "product_id": 1,
        "product_name": "Product Name",
        "quantity_returned": 1,
        "original_quantity": 2,
        "unit_price": 500,
        "line_refund": 500,
        "return_note": "Item was damaged"
      }
    ],
    "refund_amount": 250.00,
    "refund_method": "cash",
    "created_at": "2024-01-25T10:30:00Z"
  }
}
```

---

#### 4. Validate Invoice for Return

**Endpoint**: `GET /v1/sales/sell-returns/validate/{invoice_no}`

**Description**: Check if an invoice can be returned

**Path Parameters**:
- `invoice_no` (string, required): Invoice number

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "can_return": true,
    "invoice_id": 1,
    "invoice_no": "INV-2024-001",
    "contact_id": 1,
    "contact_name": "John Doe",
    "transaction_date": "2024-01-25T10:30:00Z",
    "returnable_lines": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Product Name",
        "quantity_sold": 5,
        "quantity_returned": 2,
        "quantity_can_return": 3,
        "unit_price": 500
      }
    ]
  }
}
```

**Error Response (404)**:
```json
{
  "success": false,
  "message": "Invoice not found"
}
```

---

### Payments

#### 1. Get Payments for Transaction

**Endpoint**: `GET /v1/sales/payments/{transaction_id}`

**Description**: Get all payments for a specific transaction

**Path Parameters**:
- `transaction_id` (integer, required): Transaction ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "payments": [
      {
        "id": 1,
        "transaction_id": 1,
        "amount": 500.00,
        "method": "cash",
        "paid_on": "2024-01-25T10:30:00Z",
        "note": "First payment"
      },
      {
        "id": 2,
        "transaction_id": 1,
        "amount": 300.00,
        "method": "card",
        "paid_on": "2024-01-26T14:00:00Z",
        "note": "Final payment",
        "card_type": "Visa",
        "card_holder_name": "John Doe"
      }
    ],
    "total_paid": 800.00,
    "remaining_due": 325.00
  }
}
```

---

#### 2. Add Payment to Transaction

**Endpoint**: `POST /v1/sales/payments/{transaction_id}`

**Description**: Add a new payment to a transaction

**Path Parameters**:
- `transaction_id` (integer, required): Transaction ID

**Request Body**:
```json
{
  "amount": 500.00,
  "method": "cash",
  "paid_on": "2024-01-25T10:30:00Z",
  "note": "Payment received",
  "account_id": 1,
  "card_number": "4111111111111111",
  "card_type": "Visa",
  "card_holder_name": "John Doe",
  "cheque_number": "123456",
  "bank_account_number": "123456789"
}
```

**Required Parameters**:
- `amount` (number): Payment amount
- `method` (string): Payment method - `cash`, `card`, `cheque`, `bank_transfer`, `other`

**Optional Parameters**:
- `paid_on` (string): Payment date (ISO 8601)
- `note` (string): Payment note
- `account_id` (integer): Account ID
- `card_number` (string): Card number (for card payments)
- `card_type` (string): Card type (Visa, Mastercard, etc.)
- `card_holder_name` (string): Card holder name
- `cheque_number` (string): Cheque number (for cheque payments)
- `bank_account_number` (string): Bank account number

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (201)**:
```json
{
  "success": true,
  "message": "Payment added successfully",
  "data": {
    "id": 1,
    "transaction_id": 1,
    "amount": 500.00,
    "method": "cash",
    "paid_on": "2024-01-25T10:30:00Z",
    "created_at": "2024-01-25T10:30:00Z"
  }
}
```

**Error Response (404)**:
```json
{
  "success": false,
  "message": "Transaction not found"
}
```

---

#### 3. Get Payment Details

**Endpoint**: `GET /v1/sales/payments/view/{id}`

**Description**: Get details of a specific payment

**Path Parameters**:
- `id` (integer, required): Payment ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transaction_id": 1,
    "amount": 500.00,
    "method": "cash",
    "paid_on": "2024-01-25T10:30:00Z",
    "note": "Payment received",
    "account_id": 1,
    "created_at": "2024-01-25T10:30:00Z",
    "updated_at": "2024-01-25T10:30:00Z"
  }
}
```

---

#### 4. Update Payment

**Endpoint**: `PUT /v1/sales/payments/view/{id}`

**Description**: Update an existing payment

**Path Parameters**:
- `id` (integer, required): Payment ID

**Request Body**: Same as Add Payment (all fields optional)

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Payment updated successfully",
  "data": {
    "id": 1,
    "amount": 600.00,
    "updated_at": "2024-01-25T11:45:00Z"
  }
}
```

---

#### 5. Delete Payment

**Endpoint**: `DELETE /v1/sales/payments/view/{id}`

**Description**: Delete a payment

**Path Parameters**:
- `id` (integer, required): Payment ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Payment deleted successfully"
}
```

---

#### 6. Pay Contact Due

**Endpoint**: `POST /v1/sales/payments/pay-contact-due/{contact_id}`

**Description**: Pay outstanding dues for a contact

**Path Parameters**:
- `contact_id` (integer, required): Contact ID

**Request Body**:
```json
{
  "amount": 1000.00,
  "method": "cash",
  "note": "Partial payment for due amount"
}
```

**Required Parameters**:
- `amount` (number): Payment amount

**Optional Parameters**:
- `method` (string): Payment method
- `note` (string): Payment note

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Payment applied successfully",
  "data": {
    "amount_paid": 1000.00,
    "remaining_due": 500.00
  }
}
```

---

#### 7. Get Contact Due

**Endpoint**: `GET /v1/sales/payments/contact-due/{contact_id}`

**Description**: Get total due amount for a contact

**Path Parameters**:
- `contact_id` (integer, required): Contact ID

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "contact_id": 1,
    "contact_name": "John Doe",
    "total_sales": 5000.00,
    "total_paid": 3000.00,
    "total_due": 2000.00,
    "credit_limit": 5000.00,
    "available_credit": 3000.00,
    "transactions": [
      {
        "id": 1,
        "invoice_no": "INV-2024-001",
        "amount": 1000.00,
        "paid_amount": 500.00,
        "due_amount": 500.00,
        "transaction_date": "2024-01-25"
      }
    ]
  }
}
```

---

#### 8. Get Payment Methods

**Endpoint**: `GET /v1/sales/payments/methods`

**Description**: Get available payment methods

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "methods": [
      {
        "value": "cash",
        "label": "Cash",
        "requires_account": false
      },
      {
        "value": "card",
        "label": "Card",
        "requires_account": true,
        "fields": ["card_number", "card_type", "card_holder_name"]
      },
      {
        "value": "cheque",
        "label": "Cheque",
        "requires_account": true,
        "fields": ["cheque_number"]
      },
      {
        "value": "bank_transfer",
        "label": "Bank Transfer",
        "requires_account": true,
        "fields": ["bank_account_number"]
      },
      {
        "value": "other",
        "label": "Other",
        "requires_account": false
      }
    ]
  }
}
```

---

## Next.js Integration Example

### 1. Setup API Service

```typescript
// lib/api.ts
import axios from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000/api';
const API_VERSION = process.env.NEXT_PUBLIC_API_VERSION || 'v1';

export const apiClient = axios.create({
  baseURL: `${API_BASE_URL}/${API_VERSION}`,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to request headers
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Handle response errors
apiClient.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized - redirect to login
      localStorage.removeItem('access_token');
      window.location.href = '/login';
    }
    return Promise.reject(error.response?.data || error);
  }
);
```

### 2. Create API Hooks

```typescript
// hooks/useContacts.ts
import { useQuery, useMutation } from '@tanstack/react-query';
import { apiClient } from '@/lib/api';

export const useContacts = (type?: string, search?: string) => {
  return useQuery({
    queryKey: ['contacts', type, search],
    queryFn: async () => {
      const response = await apiClient.get('/sales/contacts', {
        params: { type, search },
      });
      return response.data;
    },
  });
};

export const useCreateContact = () => {
  return useMutation({
    mutationFn: (data) => apiClient.post('/sales/contacts', data),
  });
};

export const useContact = (id: number) => {
  return useQuery({
    queryKey: ['contact', id],
    queryFn: async () => {
      const response = await apiClient.get(`/sales/contacts/${id}`);
      return response.data;
    },
  });
};

export const useUpdateContact = (id: number) => {
  return useMutation({
    mutationFn: (data) => apiClient.put(`/sales/contacts/${id}`, data),
  });
};

export const useDeleteContact = (id: number) => {
  return useMutation({
    mutationFn: () => apiClient.delete(`/sales/contacts/${id}`),
  });
};
```

### 3. Authentication Context

```typescript
// context/AuthContext.tsx
import { createContext, useContext, useState, useEffect } from 'react';
import { apiClient } from '@/lib/api';

interface AuthContextType {
  user: any | null;
  login: (email: string, password: string, businessId: number) => Promise<void>;
  logout: () => Promise<void>;
  isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState(null);
  const [isLoading, setIsLoading] = useState(false);

  const login = async (email: string, password: string, businessId: number) => {
    setIsLoading(true);
    try {
      const response = await apiClient.post('/auth/login', {
        email,
        password,
        business_id: businessId,
      });
      localStorage.setItem('access_token', response.data.access_token);
      setUser(response.data.user);
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    await apiClient.post('/auth/logout');
    localStorage.removeItem('access_token');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, isLoading }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) throw new Error('useAuth must be used within AuthProvider');
  return context;
};
```

---

## Additional Resources

### Response Codes Summary

- **2xx Success**: Operation completed successfully
- **4xx Client Error**: Invalid request or authorization issue
- **5xx Server Error**: Server-side issue

### Rate Limiting

Currently, there are no rate limits. However, implement responsible usage practices.

### Versioning

API versioning is managed through the URL path (`/v1/`). Future versions will be available at `/v2/`, etc.

### CORS

CORS is enabled. The API accepts requests from any origin.

### Data Types

- Dates: ISO 8601 format (YYYY-MM-DDTHH:mm:ssZ)
- Numbers: Use integers for IDs, floats for currency
- Strings: UTF-8 encoded

---

## Support & Contact

- **Email**: support@flawmind.com
- **API Documentation**: Available at `/api/documentation`
- **Issues**: Report bugs and issues through your support channel

---

**Last Updated**: January 25, 2026
**API Version**: 1.0.0
