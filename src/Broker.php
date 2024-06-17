<?php

declare(strict_types=1);

namespace G41797\Queue\Sqs;

use G41797\Queue\Sqs\Exception\NotConnectedSqsException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Yiisoft\Queue\Enum\JobStatus;
use Yiisoft\Queue\Message\IdEnvelope;
use Yiisoft\Queue\Message\MessageInterface;

use Interop\Queue\Context;
use Enqueue\Sqs\SqsConnectionFactory;

use G41797\Queue\Sqs\Configuration as BrokerConfiguration;
use G41797\Queue\Sqs\Exception\NotSupportedStatusMethodException;


class Broker implements BrokerInterface
{
    public const SUBSCRIPTION_NAME = 'jobs';

    public string $channelName;

    public function __construct(
        string                         $channelName = Adapter::DEFAULT_CHANNEL_NAME,
        public ?BrokerConfiguration    $configuration = null,
        public ?LoggerInterface        $logger = null
    ) {
        if (empty($channelName)) {
            $this->$channelName = Adapter::DEFAULT_CHANNEL_NAME;
        }

        if (null == $configuration) {
            $this->configuration = new BrokerConfiguration();
        }

        $endpoint = self::defaultEndpoint();

        if (isset($endpoint)) {
            $this->configuration->update(['endpoint' => $endpoint]);
        }

        if (null == $logger) {
            $this->logger = new NullLogger();
        }
    }


    public function withChannel(string $channel): BrokerInterface
    {
        if ($channel == $this->channelName) {
            return $this;
        }

        return new self($channel, $this->configuration, $this->logger);
    }

    private ?Submitter $submitter = null;

    public function push(MessageInterface $job): ?IdEnvelope
    {
        $this->prepare();

        if ($this->submitter == null)
        {
            $this->submitter = new Submitter($this->context);
        }

        $env = $this->submitter->submit($job);

        if ($env == null)
        {
            $this->submitter->disconnect();
            $this->submitter = null;
        }

        return $env;
    }

    public function jobStatus(string $id): ?JobStatus
    {
        throw new NotSupportedStatusMethodException();
    }


    private ?Receiver $receiver = null;

    public function pull(float $timeout): ?IdEnvelope
    {
        $this->prepare();

        if ($this->receiver == null)
        {
            $this->receiver = new Receiver($this->context);
        }

        try {
            $msg = $this->receiver->receive($timeout);
            return $msg;
        }
        catch (\Exception $exc) {
            $this->receiver->disconnect();
            $this->receiver = null;
            return null;
        }
    }

    public function done(string $id): bool
    {
        // For automatic ACK after consuming.
        return !empty($id);
    }

    public function disconnect(): void
    {
    }

    public ?Context    $context = null;

    private function prepare(): void
    {
        try
        {
            $this->init();
            return;
        }
        catch (\Exception $exc) {
            throw new NotConnectedSqsException();
        }
    }

    private function init(): void
    {
        if ($this->context !== null)
        {
            return;
        }

        $context = (new SqsConnectionFactory($this->configuration->raw()))->createContext();

        $queue = $context->createQueue($this->channelName.'.fifo');

        $queue->setFifoQueue(true);
        $queue->setReceiveMessageWaitTimeSeconds(20);
        $queue->setContentBasedDeduplication(true);

        $context->declareQueue($queue);

        $context->getQueueUrl($queue); // throws exception for failure

        $this->context = $context;

        return;
    }

    static public function defaultEndpoint(): string|null
    {
        return $_ENV['ENDPOINT'];
    }

}
