FROM mysql:8.0.26
RUN chown -R mysql:root /var/lib/mysql/

ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD
ARG MYSQL_ROOT_PASSWORD

ENV MYSQL_DATABASE=feedservice
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=secret
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_ROOT_HOST =  "%"
# RUN apt-get install mysql-client -y
ADD dfeedservice.sql /etc/mysql/dfeedservice.sql
ADD ditms.sql /etc/mysql/ditms.sql

RUN sed -i 's/MYSQL_DATABASE/'$MYSQL_DATABASE'/g' /etc/mysql/dfeedservice.sql
# RUN cp /etc/mysql/data.sql /docker-entrypoint-initdb.d

ENTRYPOINT [ "docker-entrypoint.sh" ]
EXPOSE 3306
CMD [ "mysql" ]