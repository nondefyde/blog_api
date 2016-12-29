## Laravel Blog Api
Run the following command
* php artisan migrate
* php artisan db:seed

After running commands a user will be created which is the admin and is the only
one who can post content..

The response result follows a pattern
* Check for 'error' if true means there is error, if false its a success.
* If error true check for message to get why and 'messages' for validation error results.
* If error false check the 'data', all results will be there.