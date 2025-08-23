<a href="../README.md" style="display: inline-block; padding: 5px 10px; background-color: #22272e; color: #adbac7; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 10px; border: 1px solid #373e47; box-shadow: 0 1px 0 #373e47;">Go Back to README</a>
# Tutorial: ecommercePHP

The `ecommercePHP` project is a comprehensive **e-commerce platform** designed for both customers and administrators. It allows users to browse *products*, add items to a *shopping cart*, and place *orders*. Payments can be processed securely through the **Khalti digital wallet** integration, and administrators have tools to manage *product inventory*, track *sales*, and update *order statuses*.


## Visual Overview

```mermaid
flowchart TD
    A0["Database Connection & Operations
"]
    A1["User Session & Access Control
"]
    A2["Khalti Payment Gateway Integration
"]
    A3["Product & Inventory Management
"]
    A4["Shopping Cart Logic
"]
    A5["Order Processing Lifecycle
"]
    A6["Flash Message System
"]
    A1 -- "Accesses User Data" --> A0
    A1 -- "Communicates Status" --> A6
    A3 -- "Manages Product Data" --> A0
    A3 -- "Communicates Actions" --> A6
    A4 -- "Manages Cart Data" --> A0
    A4 -- "Checks Stock Levels" --> A3
    A4 -- "Communicates Actions" --> A6
    A5 -- "Manages Order Data" --> A0
    A5 -- "Retrieves & Clears Items" --> A4
    A5 -- "Initiates Payment" --> A2
    A5 -- "Adjusts Stock" --> A3
    A5 -- "Communicates Status" --> A6
    A2 -- "Interacts with Order Data" --> A0
    A2 -- "Updates Product Stock" --> A3
    A2 -- "Updates Order Status" --> A5
    A2 -- "Communicates Payment Status" --> A6
```

## Chapters

1. [Flash Message System
](01_flash_message_system_.md)
2. [Database Connection & Operations
](02_database_connection___operations_.md)
3. [User Session & Access Control
](03_user_session___access_control_.md)
4. [Product & Inventory Management
](04_product___inventory_management_.md)
5. [Shopping Cart Logic
](05_shopping_cart_logic_.md)
6. [Order Processing Lifecycle
](06_order_processing_lifecycle_.md)
7. [Khalti Payment Gateway Integration
](07_khalti_payment_gateway_integration_.md)
