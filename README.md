phpBB2 for Kubernetes
=====================

Released in 2002 and last patched in 2008, [phpBB](https://www.phpbb.com/) version 2 is a perfect piece of software for cloud migration. This repository contains a few patches to make it ready, containing entry point script that installs default MySQL tables if necessary, together with an `admin` user (password is also `admin`, please change it after install).

The container is configured via following environment variables:

* `PHPBB_DB_HOST` – MySQL database host (port is always 3306)
* `PHPBB_DB_NAME` – MySQL database name
* `PHPBB_DB_USER`, `PHPBB_DB_PASSWORD` – database login
* `PHPBB_SERVER_NAME`, `PHPBB_SERVER_PORT` – server name and port for internal redirects
* `PHPBB_USE_HTTPS` – set to `1` to use https in internal redirect and cookies, otherwise set `0`
