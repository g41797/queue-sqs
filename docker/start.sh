#!/usr/bin/env bash
date
sudo apt install python3-pip
date
curl -Lo localstack-cli-3.4.0-linux-amd64-onefile.tar.gz \
    https://github.com/localstack/localstack-cli/releases/download/v3.4.0/localstack-cli-3.4.0-linux-amd64-onefile.tar.gz
sudo tar xvzf localstack-cli-3.4.0-linux-*-onefile.tar.gz -C /usr/local/bin
localstack --version
date
export LOCALSTACK_AUTH_TOKEN="ls-cIpo9773-Vaku-guFE-1697-DaBUyEwu01b9"
export LOCALSTACK_SERVICEES="SQS"
localstack start -d
date
# curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
# unzip awscliv2.zip
# sudo ./aws/install --bin-dir /usr/local/bin --install-dir /usr/local/aws-cli --update
# which aws
# ls -l /usr/local/bin/aws
# aws --version
date
pip install awscli-local
awslocal help
date
echo Create Queue
awslocal sqs create-queue --queue-name localstack-queue
echo List Queues
awslocal sqs list-queues
date

