ARG DEVELOPER_ID=1000

FROM node:14.15.3-alpine3.12

WORKDIR /home/developer

ARG DEVELOPER_ID

RUN deluser --remove-home node && addgroup --gid $DEVELOPER_ID developer && adduser -u $DEVELOPER_ID -G developer -S -g "" developer

COPY --chown=developer:developer package.json package.json
COPY --chown=developer:developer package-lock.json package-lock.json

RUN npm ci
