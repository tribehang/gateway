#!/usr/bin/env bash

if [ "${TRAVIS_PULL_REQUEST}" = "false"  ]; then
    docker tag tribehang/gateway tribehang/gateway:$TRAVIS_BRANCH.$TRAVIS_BUILD_NUMBER
    docker push tribehang/gateway:$TRAVIS_BRANCH.$TRAVIS_BUILD_NUMBER
    docker tag tribehang/gateway tribehang/gateway:$TRAVIS_BRANCH
    docker push tribehang/gateway:$TRAVIS_BRANCH
fi
