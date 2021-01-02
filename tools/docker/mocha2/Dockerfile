ARG DEVELOPER_ID=1000

FROM node:14.15.3-alpine3.12

WORKDIR /home/developer

ARG DEVELOPER_ID

RUN userdel node && addgroup --gid $DEVELOPER_ID developer && adduser --uid $DEVELOPER_ID --gid $DEVELOPER_ID --disabled-password --gecos "" developer
