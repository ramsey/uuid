FROM benramsey/hhvm-dev:3.12.0

RUN apt-get update -y && apt-get install -y build-essential curl wget git uuid-dev libuuid1
RUN wget https://github.com/vipsoft/hhvm-ext-uuid/archive/ba0ebae0fe.tar.gz \
  && tar zxf ba0ebae0fe.tar.gz \
  && cd hhvm-ext-uuid-ba0ebae0fe123e8545ea02110db2f0102c08d637/ \
  && hphpize \
  && cmake . \
  && make \
  && cp uuid.so /etc/hhvm/ext/uuid.so \
  && printf "hhvm.dynamic_extensions[uuid] = uuid.so\n" >> /etc/hhvm/php.ini
RUN rm ba0ebae0fe.tar.gz && rm -rf hhvm-ext-uuid-ba0ebae0fe123e8545ea02110db2f0102c08d637/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
