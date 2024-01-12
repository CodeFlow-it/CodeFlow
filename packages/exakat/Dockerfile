FROM php:8.2

ENV EXAKAT_VERSION 2.4.0
ENV GREMLIN_VERSION 3.3.0
ENV TZ=Europe/Paris
ENV NODE_VERSION 18.16.0

ENV PATH="/app/exakat/:${PATH}"

WORKDIR /app

# create exakat user
RUN \
    mkdir exakat && \
    cd exakat

# copy exakat config
COPY config/exakat.ini /app/exakat/config/

# change php config 
RUN \
    echo "memory_limit=-1" >> /usr/local/etc/php/php.ini && \
    echo "zend.assertions=-1" >> /usr/local/etc/php/php.ini

# install dependencies and java
RUN \
    mkdir -p /usr/share/man/man1 && \
    apt-get update && \
    apt-get install -y default-jre && \
    \
    apt-get install -y --no-install-recommends procps git bzr lsof unzip zip wget

# install composer
RUN \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    HASH="$(curl --silent -o - https://composer.github.io/installer.sig)" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN cd /app/exakat && \
    mkdir tinkergraph && \
    cd tinkergraph && \
    wget https://archive.apache.org/dist/tinkerpop/${GREMLIN_VERSION}/apache-tinkerpop-gremlin-server-${GREMLIN_VERSION}-bin.zip && \
    unzip apache-tinkerpop-gremlin-server-${GREMLIN_VERSION}-bin.zip && \
    mv apache-tinkerpop-gremlin-server-${GREMLIN_VERSION}/* ./ && \
    rm -rf apache-tinkerpop-gremlin-server-${GREMLIN_VERSION}-bin.zip apache-tinkerpop-gremlin-server-${GREMLIN_VERSION} && \
    bin/gremlin-server.sh install org.apache.tinkerpop neo4j-gremlin $GREMLIN_VERSION && \
    rm -rf javadocs && \
    rm -rf data && \
    rm -rf docs && \
    rm -rf licences && \
    bin/gremlin-server.sh start

RUN \
    cd /app/exakat && \
    curl --silent https://www.exakat.io/versions/index.php?file=exakat-$EXAKAT_VERSION.phar -o exakat.phar && \
    chmod a+x /app/exakat/exakat.phar

RUN \
    php /app/exakat/exakat.phar install -v

RUN \
    apt-get clean && \
    rm -rf /var/cache/oracle-jdk8-installer  && \
    rm -rf /var/lib/apt/lists/* && \
    /app/exakat/exakat.phar doctor

RUN curl -fsSL https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz | tar -xJ -C /usr/local --strip-components=1 --exclude="README.md" --exclude="LICENSE" --exclude="ChangeLog"

RUN npm install -g yarn

COPY package.json yarn.lock ./

RUN yarn install

COPY . .

RUN yarn build

CMD [ "yarn", "start" ]