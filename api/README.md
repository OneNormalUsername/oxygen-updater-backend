# Oxygen Updater (API)
Oxygen Updater API for Web servers.

## Usage
This is the server-level API. It is used for hosting:
- supported devices
- supported update methods (for a device)
- available update for a given device / update method / base version (update data)
- latest available system update for a given device / update method

### Version 2.5 (current)
The current version is `v2.5`. This version is deployed at `https://oxygenupdater.com/api/v2.5`
Available requests:

#### GET /devices/{filter}
Returns a list of all supported devices.

Request parameters: 
  - `filter`: One of 'all', 'enabled', or 'disabled'

Response: <Array of>
  - id: Database ID of the device
  - name: Name of the device
  - product_names: internal names of the device (to do device detection on a physical device in the app)

#### GET /updateMethods/{deviceId}
Returns a list of all update methods which are supported for a device.

Request parameters: 
  - `deviceId`: Database ID of the device

Response: <Array of>
  - `id`: Database ID of the update method
  - `english_name`: English name of the update method
  - `dutch_name`: Dutch name of the update method
  - `recommended_for_non_rooted_device`: Whether or not you should use this update method on a *non* rooted device
    - Value: `"0"` when not recommended, `"1"` when recommended
  - `recommended_for_rooted_device`: Whether or not you should use this update method on a *rooted* device. 
    - Value: `"0"` when not recommended, `"1"` when recommended
  - `supports_rooted_device`: Whether or not this update method *can* actually be used on a rooted device (e.g. incremental cannot as it fails when installing the update package when rooted). 
    - Value: `"0"` when not possible, `"1"` when possible
    
    
#### GET /updateData/{deviceId}/{updateMethodId}/{currentVersion}
Returns whether a new system update is available for a given device, using a given update method and using a `currentVersion`

Request parameters: 
  - `deviceId`: Database ID of the device
  - `updateMethodId`: Database ID of the update method to check for
  - `currentVersion`: OS version currently installed on the phone (e.g. `OnePlus6TOxygen_34.O.11_GLO_011_1811032137`).
  
Response:

If no new system update is available: 
  - `information`: `unable to find a more recent build` (default message)
  - `update_information_available`: Whether any update information is available at all (as in: is it possible to call `/mostRecentUpdateData` for this device / update method combo)
  - `system_is_up_to_date`: `true` (control logic for the app, shouldn't have been in here...)
  
  
If a new system update is available:  
  - `id`: Database ID of the update details
  - `device_id`: Database ID of the device (same as in request)
  - `update_method_id`: Database ID of the update method (same as in request)
  - `version_number`: Internal version number of the update
  - `ota_version_number`: Internal version number of the update 
  - `description`: Changelog of the update. Formatted using OnePlus' markdown variant, needs parsing to be useful. Also contains version number of the update. See [https://github.com/oxygen-updater/oxygen-updater/blob/master/app/src/main/java/com/arjanvlek/oxygenupdater/updateinformation/UpdateDescriptionParser.java](https://github.com/oxygen-updater/oxygen-updater/blob/master/app/src/main/java/com/arjanvlek/oxygenupdater/updateinformation/UpdateDescriptionParser.java) for an implementation
  - `download_url`: Direct link to download the update from
  - `download_size`: Size of the update file (in bytes, approx. because it is currently input in megabytes)
  - `md5sum`: MD5 Checksum of the update file
  - `parent_version_number`: Base version of this update (same as in the request)
  - `filename`: File name of the update package
  - `inserted_by_automator`: Whether this update was imported from the OnePlus API (`'1'`) or manually added by the community (`'0'`)
  - `is_latest_version`: Whether this update is the latest version (when `"1"`, it gets returned when calling `/mostRecentUpdateData`. see below)
  - `update_information_available`: `true`
  - `system_is_up_to_date`: `false`
  

#### GET /mostRecentUpdateData/{deviceId}/{updateMethodId}
Returns information about the most recent system update for a given device and update method

Request parameters: 
  - `deviceId`: Database ID of the device
  - `updateMethodId`: Database ID of the update method
  
Response:

When **no** update data is present for the given device / update method:
  - `error`: `unable to find most recent update data` (standard error code)
  - `update_information_available`: `false` (control logic for app...)
  - `system_is_up_to_date`: `false` (control logic for app...)

When some update data is present for the given device / update method:
  - `id`: Database ID of the update details
  - `device_id`: Database ID of the device (same as in request)
  - `update_method_id`: Database ID of the update method (same as in request)
  - `version_number`: Internal version number of the update
  - `ota_version_number`: Internal version number of the update 
  - `description`: Changelog of the update. Formatted using OnePlus' markdown variant, needs parsing to be useful. Also contains version number of the update. See [https://github.com/oxygen-updater/oxygen-updater/blob/master/app/src/main/java/com/arjanvlek/oxygenupdater/updateinformation/UpdateDescriptionParser.java](https://github.com/oxygen-updater/oxygen-updater/blob/master/app/src/main/java/com/arjanvlek/oxygenupdater/updateinformation/UpdateDescriptionParser.java) for an implementation
  - `download_url`: Direct link to download the update from
  - `download_size`: Size of the update file (in bytes, approx. because it is currently input in megabytes)
  - `md5sum`: MD5 Checksum of the update file
  - `parent_version_number`: Previous version of this update (**not useful within this request**)
  - `filename`: File name of the update package
  - `inserted_by_automator`: Whether this update was imported from the OnePlus API (`'1'`) or manually added by the community (`'0'`)
  - `is_latest_version`: Whether this update is the latest version (when `"1"`, it gets returned when calling `/mostRecentUpdateData`. see below)
  - `update_information_available`: `true` (control logic for app...)
  - `system_is_up_to_date`: `true` (control logic for app...)
  
## Developing

### Prerequisites:
The backend of Oxygen Updater requires:
- Docker or Web server (either VPS or Hosting)
- If no Docker: PHP 7.0 or later
- If no Docker: MariaDB 10.2 or later

### Obtaining the code:
The code can be obtained by cloning the `oxygen-updater-backend` project:
```
git clone https://github.com/oxygen-updater/oxygen-updater-backend.git
```

### Setting up the database credentials:
- Add the MySQL username and password in `Repository/DatabaseConnector.php` 
- Execute the MySQL database creation script from the root of this Git repository (`database.sql`):

#### VPS:
- Log in using SSH and execute `mysql -u [user_name] -p[root_password] [database_name] < database.sql`

#### Hosting:
- Use PHPMyAdmin and restore `database.sql`

### Deploying the API (non-Docker only)
- On your server, create a folder called `api` and place all the various folders (v1.0 for now) in there.

- Your server will have the following lay-out:
    - /api/ -> this project.
    - /test/api/ -> a copy of this project with its DatabaseConnector set to the test database.

### Testing the API
- You should be able to navigate to `<your_domain_name>/api/v1/devices` and see a list of all the devices.
- If this does not work or if you are not using Apache, then navigate to `<your_domain_name>/api/v1/get_devices.php`.
