#!/usr/bin/env bash

git fetch --tags

LAST_TAG=$(git describe --tags `git rev-list --tags --max-count=1`)

STRING_REPLACE="${LAST_TAG//[v|V]/}"
STRING_REPLACE="${STRING_REPLACE//-release/}"

LOCAL=./.env
SEARCH='\(APP_VERSION=\).*'
REPLACE="\1${STRING_REPLACE}"

echo "Last TAG: $LAST_TAG"
echo "String Replace Last TAG: $STRING_REPLACE"

#ubuntu
sed -i "s/${SEARCH}/${REPLACE}/g" "$LOCAL"

#mac
#sed -i -E "s/${SEARCH}/${REPLACE}/g" "$LOCAL"