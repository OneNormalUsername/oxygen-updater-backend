# Oxygen Updater (API)
Oxygen Updater API for Web servers.

This is the server-level API. It is used for hosting:
- available devices
- available update methods
- available update data
- server information (status, latest app version)
- server messages
- device registrations for notifications

##How to develop?

###Prerequisites:
This api requires:
- Web server (VPS or Hosting)
- PHP 5.4 or later
- MySQL 5.5 or later

###Obtaining the code:
The code can be obtained by cloning the project:
```
git clone https://arjan1995@bitbucket.org/arjan1995/oxygen-updater-api.git
```

###Setting up the database:
- Add the MySQL username and password in `Repository/DatabaseConnector.php` 
- Execute the MySQL database creation script from Git (`database.sql`):

####VPS:
- Log in using SSH and execute `mysql -u [user_name] -p[root_password] [database_name] < database.sql`

####Hosting:
- Use PHPMyAdmin and restore `database.sql`


###Deploying the API
- On your server, create a folder called `api` and place all the various folders (v1.0 for now) in there.

- Your server will have the following lay-out:
    - /api/ -> this project.
    - /test/api/ -> a copy of this project with its DatabaseConnector set to the test database.

###Testing the API
- You should be able to navigate to `<your_domain_name>/api/v1/devices` and see a list of all the devices.
- If this does not work or if you are not using Apache, then navigate to `<your_domain_name>/api/v1/get_devices.php`.


###Version Data
- You can use the bundled version data or a version data API from OnePlus if it exists.
- The app will automatically read the `update_data` that is specified in the database.

