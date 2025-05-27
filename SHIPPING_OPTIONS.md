# Shipping Options Feature

## Overview
The checkout page now includes shipping method selection with dynamic cost calculation and free shipping thresholds.

## Features

### Available Shipping Methods
1. **Kurier** (Courier) - 15.99 PLN
   - Delivery time: 1-2 business days
   - Standard courier delivery

2. **InPost Paczkomaty** - 9.99 PLN
   - Delivery time: 1-3 business days
   - Delivery to InPost parcel lockers

3. **Odbiór osobisty** (Personal pickup) - FREE
   - Delivery time: Immediate
   - Pickup at physical store

4. **Kurier ekspresowy** (Express courier) - 25.99 PLN
   - Delivery time: 24 hours
   - Express delivery (not eligible for free shipping)

### Free Shipping
- Free shipping threshold: 200.00 PLN
- Applies to all methods except express courier
- Visual indicator shows how much more to spend for free shipping

## Configuration

### Shipping Configuration (`config/shipping.php`)
```php
return [
    'methods' => [
        'courier' => [
            'name' => 'Kurier',
            'description' => 'Dostawa kurierem w ciągu 1-2 dni roboczych',
            'cost' => 15.99,
            'delivery_time' => '1-2 dni robocze',
            'icon' => 'truck'
        ],
        // ... other methods
    ],
    'free_shipping_threshold' => 200.00,
    'default_method' => 'courier'
];
```

### ShippingService Class
The `App\Services\ShippingService` class handles:
- Retrieving shipping methods
- Calculating shipping costs
- Applying free shipping rules
- Validating shipping methods

## API Endpoints

### Get Shipping Methods
```
GET /api/shipping-methods
```
Returns available shipping methods and free shipping threshold.

### Test Shipping Calculations
```
GET /api/test-shipping/{total}
```
Test shipping cost calculations for a given cart total.

## Frontend Integration

### Checkout Page Updates
- Added shipping method selection with radio buttons
- Dynamic cost calculation based on cart total
- Visual indicators for free shipping
- Updated order summary with shipping costs

### Key Components
- Shipping method selection UI
- Free shipping progress indicator
- Dynamic total calculation including shipping

## Database Changes

### Orders Table
Added `shipping_method` column to store selected shipping method for each order.

## Usage

1. Customer adds products to cart
2. Goes to checkout page
3. Fills shipping information
4. Selects preferred shipping method
5. Sees updated total with shipping costs
6. Completes payment

The system automatically:
- Calculates shipping costs based on cart total
- Applies free shipping when threshold is met
- Validates shipping method selection
- Stores shipping method with order

## Testing

Test the feature by:
1. Adding products with different totals to cart
2. Going to checkout
3. Selecting different shipping methods
4. Verifying cost calculations
5. Testing free shipping threshold

Use the test endpoint: `/api/test-shipping/150` to verify calculations. 