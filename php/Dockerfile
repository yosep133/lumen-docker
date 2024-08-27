FROM php:8.2-fpm

RUN apt-get update

RUN apt-get install -y unzip \
    libaio1 \
    # unixodbc-dev \
    # unixodbc \
    # msodbcsql18 \
    # mssql-tools18 \
    # libgssapi-krb5-2 \
    gnupg2 
    # libodbc1 \
    # odbcinst \
    # odbcinst1debian2

RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
# RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list 
    
RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list
ENV ACCEPT_EULA=Y
RUN apt-get update
RUN apt-get install -y unixodbc-dev 
RUN apt-get install -y unixodbc 
RUN apt-get install -y msodbcsql17
RUN apt-get install -y mssql-tools
RUN apt-get install -y libgssapi-krb5-2

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN pecl install sqlsrv pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv
ENV composer_hash 544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061


# RUN curl -sS https://getcomposer.org/installer | php -- \ --install-dir=/usr/bin --filename=composer
# RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# ENV COMPOSER_ALLOW_SUPERUSER=1 
# COPY . /var/www/html

# WORKDIR /var/www/html
# RUN cd "/var/www/html"
# RUN cp .env .env.production 
# RUN composer selfupdate
# RUN echo compose -v
# EXPOSE 8087

# CMD [ "php -S 0.0.0:8087 -t public" ]


ADD instantclient-basic-linux.x64-11.2.0.4.0.zip /tmp/
ADD instantclient-sdk-linux.x64-11.2.0.4.0.zip /tmp/
RUN unzip /tmp/instantclient-basic-linux.x64-11.2.0.4.0.zip -d /usr/local/
RUN unzip /tmp/instantclient-sdk-linux.x64-11.2.0.4.0.zip -d /usr/local/
RUN mv /usr/local/instantclient_11_2 /usr/local/instantclient
RUN ln -s /usr/local/instantclient/libclntsh.so.11.1 /usr/local/instantclient/libclntsh.so
RUN ln -s /usr/local/instantclient/libocci.so.11.1 /usr/local/instantclient/libocci.so

ENV LD_LIBRARY_PATH=/usr/local/instantclient
RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8

RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient
RUN docker-php-ext-install pdo_oci
RUN docker-php-ext-enable oci8
RUN docker-php-ext-install sockets

# Composer
# WORKDIR /var/www/html
# RUN cd "/var/www/html"
# # COPY /src /var/www/html
# CMD [ "php -S 0.0.0:8087 -t public" ]
# RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# # RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
# RUN php composer-setup.php
# RUN php -r "unlink('composer-setup.php');"
# RUN mv composer.phar /usr/local/bin/composer
# RUN composer -V
# RUN composer install

# COPY /src/feed/ /var/www/html/
# WORKDIR /var/www/html
# RUN cd "/var/www/html"
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php — install-dir=/usr/local/bin — filename=composer && php -r "unlink('composer-setup.php');"
RUN cp composer.phar /usr/local/bin/composer
# RUN composer install 

# if downgrade version openssl 
# WORKDIR /usr/local/src
# # RUN cd /usr/local/src
# RUN curl https://www.openssl.org/source/old/1.0.1/openssl-1.0.1k.tar.gz --output /usr/local/src/openssl-1.0.1k.tar.gz
# RUN tar -xvf /usr/local/src/openssl-1.0.1k.tar.gz
# # RUN cd /usr/local/src/openssl-1.0.1k
# WORKDIR /usr/local/src/openssl-1.0.1k
# RUN ./config --prefix=/usr/local/ --openssldir=/usr/local/openssl
# RUN make
# RUN make test
# RUN make install
# RUN mv /usr/bin/openssl /usr/bin/openssl-bak
# RUN cp -r /usr/local/openssl /usr/bin
# RUN ls -ld /usr/bin/openssl

COPY openssl.cnf /etc/ssl/openssl.cnf