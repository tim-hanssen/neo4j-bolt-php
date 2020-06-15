<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V3;

use GraphAware\Bolt\Driver;
use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Protocol\Message\V3\BeginMessage;
use GraphAware\Bolt\Protocol\Message\V3\CommitMessage;
use GraphAware\Bolt\Protocol\Message\V3\GoodbyeMessage;
use GraphAware\Bolt\Protocol\Message\V3\HelloMessage;
use GraphAware\Bolt\Protocol\Message\V3\RollbackMessage;
use GraphAware\Bolt\Protocol\Message\V3\RunMessageWithMetadata;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Session extends \GraphAware\Bolt\Protocol\V1\Session
{
    const PROTOCOL_VERSION = 3;

    /**
     * @param AbstractIO $io
     * @param EventDispatcherInterface $dispatcher
     * @param array $credentials
     * @param bool $init
     * @throws \Exception
     */
    public function __construct(
        AbstractIO $io,
        EventDispatcherInterface $dispatcher,
        array $credentials = [],
        $init = true
    )
    {
        parent::__construct($io, $dispatcher, $credentials, false);
        if ($init){
            // in Bolt v3+ init is replaced by hello
            $this->hello();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    protected function createRunMessage($statement, $prams = [])
    {
        // Bolt V3+ uses run messages with metadata
        return new RunMessageWithMetadata($statement, $prams);
    }

    /**
     * {@inheritdoc}
     */
    public function transaction()
    {
        if ($this->transaction instanceof Transaction) {
            throw new \RuntimeException('A transaction is already bound to this session');
        }

        return new Transaction($this);
    }

    public function close()
    {
        $this->goodbye();
        parent::close();
    }

    public function hello() {
        $this->io->assertConnected();
        $ua = Driver::getUserAgent();
        $this->sendMessage(new HelloMessage($ua, $this->credentials));
        $responseMessage = $this->receiveMessage();
        if ($responseMessage->getSignature() != 'SUCCESS') {
            throw new \Exception('Unable to HELLO');
        }
        $this->isInitialized = true;
    }

    public function begin() {
        $this->sendMessage(new BeginMessage());
        $this->receiveMessage();
    }

    public function commit() {
        $this->sendMessage(new CommitMessage());
        $this->receiveMessage();
    }

    public function rollback() {
        $this->sendMessage(new RollbackMessage());
        $this->receiveMessage();
    }

    public function goodbye() {
        $this->sendMessage(new GoodbyeMessage());
    }

}