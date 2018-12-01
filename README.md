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
The easiest way to develop or host this project, is by using Docker. It launches everything that's needed to work on the full backend of the Oxygen Updater app.

#### MacOS / Linux
Mac: Install Docker from `https://www.docker.com/products/docker-desktop`
Linux: Install Docker for your distro from `https://docs.docker.com/install/linux/docker-ce/ubuntu/` (use the side menu to select other distro than Ubuntu).

Once you've got Docker installed and running, just run `docker-compose up` from the folder you've cloned this repo to launch everything at once.
That will launch the following:

- The website is available at `localhost:8000`
- The FAQ pages are available at `localhost:8000/faq` and `localhost:8000/inappfaq`
- The APIs are available at `localhost:8000/api` (current is /v2.3 but all previous versions are available as well)

Also, a PHPMyAdmin is spawned at `localhost:8183` so you can perform operations on the database.

#### Windows
Windows users have a disadvantage when using Docker. If you somehow have access to a Mac or Linux box, it is preferred to use that instead.

Most Windows developers will have to use Docker Machine, as the regular Docker requires Hyper-V and therfore cannot be used on the non-Pro Windows 10. Also, the regular Docker for Windows does not work together with the Android emulator. If you only plan to run the Android app on a real device then you can use the regular Docker for Windows.

You can get Docker Machine by following the instructions found on https://docs.docker.com/toolbox/. Docker Toolbox contains Docker Machine. Make sure that Docker Compose for Windows is checked during setup.

After you've installed it, click on `Docker Quickstart terminal` on your desktop. Also open the Device Manager as well (see below).

It will now create a network adaptor. The first time, this failed for me. To fix this, RESTART your computer after it has been creating a network adaptor for a short while. When the Device Manager shows a VirtualBox network adaptor with a yellow "!" symbol next to it, then restart your PC and force restart when it waits for programs to exit. After the restart, open the Quickstart terminal again and it should fully configure / startup.

The terminal should show that it created a machine with an IP address. Remember this IP address, you'll need it to access the spawned services later on (`localhost` access does not work!).
Now, `cd` to this project and type `docker-compose up`. You should get the website / backend at port `8000` of the IP address mentioned above, and PHPMyAdmin at port `8183`. See above which services are hosted where.

If you plan to run the Android app as well, edit its `build.gradle` file to adjust the IP address of the server to the IP address mentioned above. 

### No Docker
If you do *not* have Docker, you'll need a "LAMP server" or "WAMP server" application.
That contains a webserver, PHP and a mysql / mariadb database. You'll have to load `database.sql` in there and place all contents of the repository in the web root folder. Also make sure the Apache "Headers" and "Rewrite" modules are active, and that the PHP "JSON", "PDO" and "mysqli" modules are installed.

## Contributing
If you send a PR, I will review it and if it is good it can be deployed to the official application server.



