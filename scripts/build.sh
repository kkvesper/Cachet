#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

if [ -z "$HOST_USER_ID" ]; then
    echo "HOST_USER_ID variable not set"
    exit 1
fi

if [ -z "$HOST_USER_GID" ]; then
    echo "HOST_USER_GID variable not set"
    exit 1
fi

BUILD_DIR="/tmp/build"

ARTIFACT_DEST_DIR="/dist"
ARTIFACT_NAME="cachet.tar.gz"
ARTIFACT_PATH="$ARTIFACT_DEST_DIR/$ARTIFACT_NAME"

echo "Preparing build directory..."
if [ ! -d $ARTIFACT_DEST_DIR ]; then mkdir -p $ARTIFACT_DEST_DIR; fi
if [ ! -d $ARTIFACT_PATH ]; then rm -rf $ARTIFACT_PATH; fi

cp -R /code $BUILD_DIR

echo "Cleaning up cache files..."
if [ -d $BUILD_DIR/vendor ]; then rm -rf $BUILD_DIR/vendor; fi
rm -rf $BUILD_DIR/bootstrap/cache/*
rm -rf $BUILD_DIR/bootstrap/cachet/*

pushd $BUILD_DIR >/dev/null

echo "Installing dependencies..."
composer install --no-dev -o

echo "Archiving..."
tar czf "$ARTIFACT_PATH" . --exclude=dist --exclude=.git
chown ${HOST_USER_ID}:${HOST_USER_GID} "$ARTIFACT_PATH"

echo "Cleaning up..."
popd >/dev/null
rm -rf $BUILD_DIR

echo "OK!"
