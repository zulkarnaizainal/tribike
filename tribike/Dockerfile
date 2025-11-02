version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=tribike
      - DB_USER=tribike_user
      - DB_PASS=tribike_pass

  db:
    image: mysql:8.0
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: tribike
      MYSQL_USER: tribike_user
      MYSQL_PASSWORD: tribike_pass
    volumes:
      - db_data:/var/lib/mysql
      - ./tribike_db.sql:/docker-entrypoint-initdb.d/init.sql

volumes:
  db_data:


