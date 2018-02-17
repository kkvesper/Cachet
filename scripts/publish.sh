#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

PROJECT="cachet"
GIT_TAG=$(echo ${GIT_BRANCH} | cut -d'/' -f3)
BUILD_ARTIFACT_PATH="dist/${PROJECT}.tar.gz"

if [ ! -f ${BUILD_ARTIFACT_PATH} ]; then
    echo "${BUILD_ARTIFACT_PATH} does not exist. Please run make build and try again."
    exit 1
fi

if [ -z "$PUBLISH_BUCKET" ]; then
    echo "PUBLISH_BUCKET variable not set"
    exit 1
fi

if [ -z "$SNOWBALL_URL" ]; then
    echo "SNOWBALL_URL variable not set"
    exit 1
fi

echo "Git branch: ${GIT_BRANCH}
Git tag: ${GIT_TAG}
Git commit: ${GIT_COMMIT}
Build number: ${BUILD_NUMBER}" | tee ./REVISION

echo "Publish bucket: ${PUBLISH_BUCKET}"
echo "Snowball url: ${SNOWBALL_URL}"
umask 007

url="s3://${PUBLISH_BUCKET}/${PROJECT}/${JOB_NAME}/${BUILD_NUMBER}.tar.gz"
aws s3 cp --sse -- "${BUILD_ARTIFACT_PATH}" "${url}"

echo "Build uploaded to ${url}"

echo "Adding build to Snowball"
/usr/local/bin/snowball publish \
    -p "${PROJECT}" \
    -e "staging" \
    -e "production" \
    -u "${url}" \
    -s "${SNOWBALL_URL}"

echo "Done!"
