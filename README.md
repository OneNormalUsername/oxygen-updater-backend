# Oxygen Updater (backend)

Backend for the oxygen updater app.

This repository contains the following:
- All APIs used by the app
- The FAQ pages (as shown on oxygenupdater.com and within the app)
- Website (what you'll see when you go to oxygenupdater.com)
- Database schema as used by the test server (mariaDB), contains sample devices and update data.

This repository does *not* contain:
- Admin portal
- Automatic update fetching scripts which were used to grab updates from OnePlus.

## Running / building it

### Docker
If you have Docker, run `docker-compose up` to launch everything at once.
That will launch the following:

- The website is available at `localhost:8000`
- The FAQ pages are available at `localhost:8000/faq` and `localhost:8000/inappfaq`
- The APIs are available at `localhost:8000/api` (current is /v2.3 but all previous versions are available as well)

Also, a PHPMyAdmin is spawned at `localhost:8183` so you can perform operations on the database.

Note: Windows users may need to use Docker Machine or Docker Toolbox, as the regular Docker requires Hyper-V and therfore cannot be used on the non-Pro Windows 10. 
Also, the regular Docker for Windows does not work together with the Android emulator.

### No Docker
If you do *not* have Docker, you'll need a "LAMP server" or "WAMP server" application.
That contains a webserver, PHP and a mysql / mariadb database. You'll have to load `database.sql` in there and place all contents of the repository in the web root folder.

## Contributing
If you send a PR, I will review it and if it is good it can be deployed to the official application server.



