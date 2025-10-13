FROM php:8.2-cli
WORKDIR /code
EXPOSE 5000
COPY . .
CMD [ "php", "-S", "localhost:8000" ]