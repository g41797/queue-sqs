#!/usr/bin/env bash
curl -Lo localstack-cli-3.4.0-linux-amd64-onefile.tar.gz \
    https://github.com/localstack/localstack-cli/releases/download/v3.4.0/localstack-cli-3.4.0-linux-amd64-onefile.tar.gz
sudo tar xvzf localstack-cli-3.4.0-linux-*-onefile.tar.gz -C /usr/local/bin
localstack --version
