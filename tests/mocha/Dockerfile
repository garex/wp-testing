FROM node:12

WORKDIR /home/mocha

ENV PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/home/mocha/node_modules/.bin/

RUN userdel node && groupdel node && addgroup --gid=1000 developer && adduser --uid 1000 --ingroup developer developer

USER developer
