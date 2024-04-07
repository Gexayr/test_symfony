# Updated Dockerfile
FROM php:8.3-cli-alpine as sio_test

# Install necessary packages
RUN apk add --no-cache git zip bash

# Copy Composer binary from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup php app user
ARG USER_ID=1000
RUN adduser -u 1000 -H -D app

# Switch to the app user
USER app

# Copy the application files
COPY --chown=app . /app
WORKDIR /app

# Expose port 8337
EXPOSE 8337

# Start the PHP server
CMD ["php", "-S", "0.0.0.0:8337", "-t", "public"]
