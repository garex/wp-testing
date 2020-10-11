ARG DEVELOPER_ID=1000

FROM node:12

WORKDIR /home/mocha

ENV PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/home/mocha/node_modules/.bin/

ARG DEVELOPER_ID

RUN userdel node && addgroup --gid $DEVELOPER_ID developer && adduser --no-create-home --uid $DEVELOPER_ID --gid $DEVELOPER_ID --disabled-password --gecos "" developer
