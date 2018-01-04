#!/usr/bin/env bash

sudo pip install --upgrade pip
sudo pip install awscli
aws configure set AWS_ACCESS_KEY_ID $AWS_ACCESS_KEY_ID
aws configure set AWS_SECRET_ACCESS_KEY $AWS_SECRET_ACCESS_KEY
aws configure set default.region eu-central-1
