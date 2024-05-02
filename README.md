<p align="center">Cliq Connect</p>

## install composer which create vendor all packages will install related to laravel packages.

#composer install

## Important Note: only time you need to call key:generate is following a clone of a pre-created project.

If you use a version control system like git to manage your project for development, calling git push ... will push a copy of your project to wherever it is going, but will not include your .env file. Therefore, if someone clones your project using git clone ... they will have to manually enter key:generate for their app to function correctly.

#php artisan key:generate

## Create Database and Configure your database in .env file

copy the .env.example and rename the name with .env
open .env file and find the database configuration

For example.
DB_CONNECTION=mysql
DB_HOST=<Host>
DB_PORT=<PORT>
DB_DATABASE=<DatabaseName>
DB_USERNAME=<Username>
DB_PASSWORD=<Password>

## Once you have configured your database, you may run your application's database migrations, which will create your application's database tables.

#php artisan migrate

## Once all table is created add super admin to user table using seeder command.

execute specific seeder class which you have created.

#php artisan db:seed --class=SuperAdminSeeder
#php artisan db:seed --class=QuestionSeeder
#php artisan db:seed --class=InterestSeeder
#php artisan db:seed --class=StateSeeder
or
#php artisan db:seed ( which will be execute all seederclass)

## execute passport:install command. this command will create the encryption keys needed to generate secure access tokens.(allowed authenticated user's token).

#php artisan passport:install
#php artisan passport:install --force

## When deploying Passport to your application's servers for the first time, you will likely need to run the passport:keys command.(generates the encryption keys Passport needs in order to generate access tokens).

#php artisan passport:keys

## Configure Azure in .env file to access the azure driver (Azure Blob storage)

FILESYSTEM_DISK= azure

AZURE_STORAGE_NAME={StorageName}
AZURE_STORAGE_KEY="{StorageKey}"
AZURE_STORAGE_CONTAINER={StorageContainer}
AZURE_STORAGE_URL="{StorageUrl}"
AZURE_SIGNING_URL_PERMISSION=rw

## To verify mobile using the third party services Twilio configure details as below

TWILIO_ACCOUNT_SID={AccountSID}
TWILIO_AUTH_TOKEN={AuthToken}
TWILIO_CALLER_ID={CallerID}
TWILIO_VERIFY_SID={VerifyID} # which is need to be create service in twilio.com

## Mail Configurattion in .env file.

MAIL_MAILER=smtp
MAIL_HOST={MAIL_HOST}
MAIL_PORT={MAIL_PORT}
MAIL_USERNAME={USERNAME}
MAIL_PASSWORD={Password}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="{FROM_EMAIL_ADDRESS}"
MAIL_FROM_NAME="${APP_NAME}"

## Cliq-connect admin

## Install NPM & Node for AdminLTE theme packages

1. Install NPM & Node
2. Execute npm install
3. Execute npm run dev/prod based on the environment

## Algorithm points for friendship:

1. Age should between age range.
2. Interest match is not mandatory.
3. If show more people is true then distance will be discarded else distance range should match.
4. Interested-in should match (Everyone will show all the profiles).
5. CliQ-mode should be match.
6. User should not be disconnected by loggedIn user.
7. User should not be blocked by admin.

## Algorithm points for dating:

1. Age should between age range.
2. Atleast one interest should match.
3. If show more people is true then distance will be discarded else distance range should match.
4. Interested-in should match (Everyone will show all the profiles).
5. CliQ-mode should be match.
6. User should not be disconnected by loggedIn user.
7. User should not be blocked by admin.
