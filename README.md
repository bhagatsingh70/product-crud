
## Steps to set project

Run below commands

git clone https://github.com/bhagatsingh70/product-crud.git

composer install

php artisan migrate

php artisan db:seed

php artisan serve

For now i am not using attributes table, but when we implement these api in App or web then we need to use this table and store ids in product_attributes table
