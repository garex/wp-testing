ARG DEVELOPER_ID=1000

FROM node:12

WORKDIR /home/mocha

ENV PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/home/mocha/node_modules/.bin/

ARG DEVELOPER_ID

RUN userdel node && addgroup --gid $DEVELOPER_ID developer && adduser --uid $DEVELOPER_ID --gid $DEVELOPER_ID --disabled-password --gecos "" developer

RUN npm config set user 0

RUN npm config set unsafe-perm true

RUN mkdir /tmp/phantomjs && cd /tmp/phantomjs && wget https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-1.9.8-linux-x86_64.tar.bz2

# RUN npm install -g phantomjs@1.9.18

# RUN npm install -g casperjs mocha chai mocha-casperjs mocha-phantomjs casper-chai

# RUN npm install -g phantomjs-prebuilt@2 casperjs@1 mocha@2 chai@2 mocha-casperjs casper-chai

# RUN npm install -g phantomjs-prebuilt@2 casperjs@1 mocha@2 chai@2 mocha-casperjs casper-chai

# "phantomjs": "^1.9.18"
# "casperjs": "^1.1.0",
# "mocha": "^2.2.4",
# "chai": "^2.2.0",
# "mocha-casperjs": "^0.5.3",
# "mocha-phantomjs": "^3.5.3",
# "casper-chai": "^0.2.1",