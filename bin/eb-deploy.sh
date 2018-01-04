#!/usr/bin/env bash

if [ "${TRAVIS_PULL_REQUEST}" != "false" ] ; then
    echo "Not deploying on pull request builds";
    exit 0
fi

EB_APPLICATION_NAME="tribehang-gateway"
EB_ENVIRONMENT_NAME="staging-gateway-tribehang"
EB_VERSION_LABEL="${TRAVIS_BRANCH}.${TRAVIS_BUILD_NUMBER}"
EB_RUNFILE="gateway-microservice.${EB_VERSION_LABEL}.zip"
EB_RUNFILE_S3KEY="${EB_APPLICATION_NAME}/${EB_RUNFILE}"
EB_BUCKET="tribehang-elasticbeanstalk"

sedRules=(
    "s/<TAG>/$EB_VERSION_LABEL/g"
    "s/<BRANCH>/$TRAVIS_BRANCH/g"
    "s/<BUCKET>/$EB_BUCKET/g"
)
sedRulesConcatenated=$(IFS=";" ; echo "${sedRules[*]}")

if [ "${TRAVIS_BRANCH}" = "master" ] || [ "${TRAVIS_BRANCH}" = "develop" ]; then
    mkdir -p build
    cat Dockerrun.aws.json | sed "$sedRulesConcatenated" > build/Dockerrun.aws.json
    cd build && zip -r ../$EB_RUNFILE . && cd ..
    aws s3 cp $EB_RUNFILE s3://$EB_BUCKET/$EB_RUNFILE_S3KEY
    aws elasticbeanstalk create-application-version --application-name $EB_APPLICATION_NAME --version-label $EB_VERSION_LABEL --source-bundle S3Bucket=$EB_BUCKET,S3Key=$EB_RUNFILE_S3KEY
fi

if [ "${TRAVIS_BRANCH}" = "develop" ]; then
    aws elasticbeanstalk update-environment --environment-name $EB_ENVIRONMENT_NAME --version-label $EB_VERSION_LABEL
fi
