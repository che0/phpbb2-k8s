FROM debian:jessie

LABEL maintainer="petr.novak2@firma.seznam.cz"

RUN apt-get update \
    && apt-get install -y \
        apache2 \
        libapache2-mod-php5 \
        php5-mysql \
        patch \
      --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /src/*.deb

# apache config
ADD apache2.conf /etc/apache2/apache2.conf

# fake sendmail for phpBB
RUN ln -s /bin/true /usr/sbin/sendmail

# install phpBB with our special config and patches
ADD --chown=www-data:www-data phpBB-2.0.23.tar.gz /var/www/
ADD --chown=www-data:www-data config.php /var/www/phpBB2/
ADD common.php.patch /tmp/
RUN patch /var/www/phpBB2/common.php < /tmp/common.php.patch && rm /tmp/common.php.patch

# custom script for install / initial setup
ADD setup.php /setup.php

EXPOSE 80

ADD entrypoint.sh /entrypoint.sh
CMD ["/entrypoint.sh"]
