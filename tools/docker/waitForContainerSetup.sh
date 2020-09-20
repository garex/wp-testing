#!/usr/bin/env bash

# Fork from https://www.marksayson.com/blog/wait-until-docker-containers-initialized/

# Max query attempts before consider setup failed
MAX_TRIES=10

function waitUntilServiceIsReady() {
    local attempt=1
    local containerName="$1"
    local upMarker="$2"

    while [ $attempt -le $MAX_TRIES ]; do
        docker-compose logs $containerName | grep "$upMarker"
        if [ $? -eq 0 ]; then
            echo "$containerName container is up!"
            return
        fi
        echo "Waiting for $containerName container, attempt: $attempt ..."
        sleep 1
        ((attempt++))
    done

    echo "Error: $containerName not responding, cancelling set up"
    docker-compose logs $containerName
    exit 1
}

waitUntilServiceIsReady back "Development Server started"
waitUntilServiceIsReady db "port: 3306  MySQL Community Server (GPL)"
waitUntilServiceIsReady mocha2 "DevTools listening"
