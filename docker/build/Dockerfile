FROM php:8.2-cli

# composer
RUN apt-get update && apt-get install -y --no-install-recommends git curl zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install ngrok
RUN curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc \
  | tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null && echo "deb https://ngrok-agent.s3.amazonaws.com buster main" \
  | tee /etc/apt/sources.list.d/ngrok.list && apt update && apt install ngrok

# uncomment if you need xdebug
# RUN pecl install xdebug && docker-php-ext-enable xdebug

# uncomment if you need standard tools in container
# RUN apt-get update && apt-get install -y --no-install-recommends procps make less lsof vim htop dnsutils iputils-ping sudo curl nmap iproute2 procps gpg software-properties-common wget build-essential autoconf libtool git

WORKDIR /app/xsolla-example