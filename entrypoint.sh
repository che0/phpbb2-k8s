#!/bin/bash
set -e

# setup (and install if necessary)
php /setup.php

# clean install directories
rm -r /var/www/phpBB2/{contrib,install}

# pass control to apache
exec /usr/sbin/apache2 -DFOREGROUND
