# Magento2 Module Sws CustomOrderProcessing

`sws/module-customorderprocessing`


## Main Functionalities
- API-Based Order Updates – Secure REST API with authentication for external order status updates.
- Event-Driven Logging – Uses sales_order_save_after observer to log status changes in a custom table.
- Email Notifications – Sends an email when an order is marked as shipped.

## Installation

### Zip file

 - Unzip the zip file in `app/code/Sws`
 - Enable the module by running `php bin/magento module:enable Sws_CustomOrderProcessing`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

### Install with Composer
- composer require sws/module-customorderprocessing
- php bin/magento module:enable Sws_CustomOrderProcessing
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile

## API Usage

### Endpoint:

`POST /V1/order/status/update`

### Request Format:
`{
    "incrementId": "100000001",
    "status": "complete"
}`

### Response:
- 200 OK: Order status updated successfully.
- 400 Bad Request: Invalid order ID or status.

## Database Schema

This module creates a custom table `sws_order_status_log` with the following columns:
-	log_id (Primary Key, Auto Increment)
-	order_id (Integer, Not Null)
-	old_status (Text, Not Null)
-	new_status (Text, Not Null)
-	created_at (Timestamp, Default Current Time)

Event Observer
-	Event: `sales_order_save_after`
- Functionality:
-	Logs order status changes into the database.
-	Sends an email notification when an order is marked as shipped.

Uninstallation
- bin/magento module:disable Sws_CustomOrderProcessing
- bin/magento setup:upgrade
- bin/magento cache:flush
