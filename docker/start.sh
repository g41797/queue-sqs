#!/usr/bin/env bash
date
sudo apt install python3-pip
date
curl -Lo localstack-cli-3.4.0-linux-amd64-onefile.tar.gz \
    https://github.com/localstack/localstack-cli/releases/download/v3.4.0/localstack-cli-3.4.0-linux-amd64-onefile.tar.gz
sudo tar xvzf localstack-cli-3.4.0-linux-*-onefile.tar.gz -C /usr/local/bin
rm -f localstack-cli-3.4.0-linux-*-onefile.tar.gz
localstack --version
date
export LOCALSTACK_AUTH_TOKEN="ls-soyUdUBu-fODo-zAZE-XOlO-9157WULu09eb"
export LOCALSTACK_SERVICEES="sqs"
export LOCALSTACK_SQS_ENDPOINT_STRATEGY="path"
localstack start -d
date
sudo apt remove awscli
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip -u awscliv2.zip
sudo ./aws/install --bin-dir /usr/local/bin --install-dir /usr/local/aws-cli --update
which aws
ls -l /usr/local/bin/aws
aws --version
rm -f awscliv2.zip
date
pip install awscli-local
pip install awscli
date
pwd
echo Create Queue
awslocal sqs create-queue --queue-name test-queue.fifo --attributes FifoQueue=true,ContentBasedDeduplication=true
echo List Queues
awslocal sqs list-queues
echo Send Messages
awslocal sqs send-message --queue-url "http://localhost.localstack.cloud:4566/queue/us-east-1/000000000000/test-queue.fifo" --message-body "12345678901" --message-group-id jobs
awslocal sqs send-message --queue-url "http://localhost.localstack.cloud:4566/queue/us-east-1/000000000000/test-queue.fifo" --message-body "12345678902" --message-group-id jobs
echo Receive Message
awslocal sqs receive-message --queue-url "http://localhost.localstack.cloud:4566/queue/us-east-1/000000000000/test-queue.fifo" --attribute-names All --message-attribute-names All --max-number-of-messages 1
echo Purge Queue
awslocal sqs purge-queue --queue-url "http://localhost.localstack.cloud:4566/queue/us-east-1/000000000000/test-queue.fifo"
echo Delete Queue
awslocal sqs delete-queue --queue-url "http://localhost.localstack.cloud:4566/queue/us-east-1/000000000000/test-queue.fifo"
echo List Queues
awslocal sqs list-queues
date
pwd

