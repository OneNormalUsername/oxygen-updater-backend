# Oxygen Updater (backend)

![PHP Composer][ci-badge]

Backend for the Oxygen Updater app.

This repository contains the following:
- All APIs used by the app
- The FAQ pages (as shown on oxygenupdater.com and within the app)
- Website (what you'll see when you go to oxygenupdater.com)
- Database schema as used by the test server (mariaDB), contains sample devices and update data.

This repository does *not* contain:
- Admin portal (coming soon w/Docker)
- News image uploader for admins (coming soon, needs a few security adjustments)
- Real-time installation dashboard (my React.js sample project, may be added or maybe not)

## Running / building it

### Docker
The easiest way to develop or host this project, is by using Docker. It launches everything that's needed to work on the full backend of the Oxygen Updater app. See below on how to install Docker for your platform.

Once you've got Docker installed and running, just run `docker-compose up` from the folder you've cloned this repo to launch everything at once.
That will launch the following:

- The website is available at `localhost:8000`
- The FAQ pages are available at `localhost:8000/faq` and `localhost:8000/inappfaq`
- The APIs are available at `localhost:8000/api` (current is /v2.6 but all previous versions are available as well)
- A page containing info about the latest OS versions in the database and missing OS versions is available at `localhost:8000/os-version-info`

Also, a PHPMyAdmin is spawned at `localhost:8183` so you can perform operations on the database.

#### Installing Docker on MacOS / Linux
Mac: Install Docker Desktop from `https://www.docker.com/products/docker-desktop`
Linux: Install a Docker package for your distro from `https://docs.docker.com/install/linux/docker-ce/ubuntu/` (use the side menu to select other distro than Ubuntu).

#### Installing Docker on Windows
##### Windows 10 Pro / Enterprise 1803+ with Hyper-V and Hypervisor Platform
If you are using the Pro or Enterprise edition of Windows 10, have the April 2018 update or newer (1803+) and have a CPU which supports Hyper-V, then you can use Docker almost the same way as on a Mac or Linux PC. The install procedure is then as following:
- Fully enable Hyper-V and the Hypervisor Platform under Windows Features (type "Turn Windows features on or off" in the start menu)
- Restart your computer
- Install Docker Desktop from `https://www.docker.com/products/docker-desktop`
- The Docker tray icon tells you if Docker is running properly
- See `https://android-developers.googleblog.com/2018/07/android-emulator-amd-processor-hyper-v.html` for more information on how to use the Android Emulator to run the app in combination with Hyper-V and the Docker-hosted backend

##### Windows 10 Home or CPU incompatible with Hyper-V
If you are using Windows 10 Home or have a CPU which does not support Hyper-V, you will have to use Docker Machine, as the regular Docker requires Hyper-V to run. Please note that Docker Machine is *much slower* than the regular Docker for Windows and *has been deprecated*, so only use this as a last resort.

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
See [CONTRIBUTING.md][contributing]

Note: In case you get an error saying `/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer.php` is not writeable, `chmod` it to 777; stop and think. 777-ing it is a very bad practice, as it basically grants the world full access to those files. `chown` it to `www-data` instead.

## License
This repository has no license. Default copyright laws apply, as mentioned in GitHub's ["Licensing a repository" page][github-licensing-info]:
> [...] Without a license, the default copyright laws apply, meaning that you retain all rights to your source code and no one may reproduce, distribute, or create derivative works from your work. [...].

For legal purposes, the owner of this organization (Adhiraj Singh Chauhan) is to be considered as the owner of this project, and all its associated files and build outputs (APKs, AABs, etc.). Contributors of this project agree to transfer copyrights to the owner of this organization, with some exceptions:
- Code owners share ownership of that file, and are entitled to the same copyright laws as the owner of the organization.  
  Code owners are usually marked with the `@author` annotation on a class/method/variable.
- If any source file within this repository has license information as part of the file, that license overrides.

This means that you are not permitted to redistribute and/or modify both the source code of this project, and the relevant build outputs without our explicit permission.
You can contact us on [our official Discord server][discord], or over [email][support-email].

You can still view/fork this repository, submit PRs, and/or raise issues. This is in accordance of GitHub's Terms of Service:
> [...] If you publish your source code in a public repository on GitHub, according to the [Terms of Service][github-tos], other GitHub users have the right to view and fork your repository within the GitHub site. [...]

[ci-badge]: https://github.com/oxygen-updater/oxygen-updater-backend/workflows/PHP%20Composer/badge.svg
[contributing]: ./CONTRIBUTING.md
[github-licensing-info]: https://help.github.com/en/github/creating-cloning-and-archiving-repositories/licensing-a-repository#choosing-the-right-license
[discord]: https://discord.gg/5TXdhKJ
[support-email]: mailto:support@oxygenupdater.com?subject=Legal%20request%20for%20Oxygen%20Updater
[github-tos]: https://help.github.com/en/articles/github-terms-of-service
