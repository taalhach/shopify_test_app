#shopify_test_app
This is very simple application that is developed to understand the php and shopify [REST API.](https://shopify.dev/docs/admin-api/rest/reference) 
This application is installed in a store at shopify.com. This application does following
- Installs app to given shop 
- Generates token and stores in database.
- Subscribes to app/uninstall webhook. 
- Add products and its variants in database.
- Adds orders in database.
- Adds metafields to products.
- Tags orders
- Tags customers.

## How to run.
After cloning repository type following url in browser and hit enter.
```
http://localhost/shopify_test_app/install.php?shop={YOUR_SHOP_NAME}
```
 