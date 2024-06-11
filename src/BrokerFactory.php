<?php

declare(strict_types=1);

namespace G41797\Queue\Pulsar;

use Psr\Log\LoggerInterface;
use G41797\Queue\Pulsar\Configuration as BrokerConfiguration;
use Yiisoft\Queue\QueueFactoryInterface;

class BrokerFactory implements BrokerFactoryInterface
{

    public function get(
                            string $channel = Adapter::DEFAULT_CHANNEL_NAME,
                            array $config = [],
                            ?LoggerInterface $logger = null
                        ): ?BrokerInterface {
        return new Broker(
            $channel,
             BrokerConfiguration::default()->update($config),
            $logger
        );
    }
}
