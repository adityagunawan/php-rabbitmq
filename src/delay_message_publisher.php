<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$host = 'localhost';
$port = '5672';
$user = 'guest';
$pass = 'guest';
$vhost = '/';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

/**
 * Declares exchange
 *
 * @param string $exchange
 * @param string $type
 * @param bool $passive
 * @param bool $durable
 * @param bool $auto_delete
 * @param bool $internal
 * @param bool $nowait
 * @return mixed|null
 */
$channel->exchange_declare('delayed_exchange', 'x-delayed-message', false, true, false, false, false, new AMQPTable(array(
   'x-delayed-type' => AMQPExchangeType::FANOUT
)));

/**
 * Declares queue, creates if needed
 *
 * @param string $queue
 * @param bool $passive
 * @param bool $durable
 * @param bool $exclusive
 * @param bool $auto_delete
 * @param bool $nowait
 * @param null $arguments
 * @param null $ticket
 * @return mixed|null
 */
$channel->queue_declare('delayed_queue', false, false, false, false, false, new AMQPTable(array(
   'x-dead-letter-exchange' => 'delayed'
)));

$channel->queue_bind('delayed_queue', 'delayed_exchange');

$headers = new AMQPTable(array('x-delay' => 7000));
$messageBody = json_encode([
    'title' => 'coba lagi',
    'body' => 'lorem ipsum dolor.........'
]);
$message = new AMQPMessage($messageBody, array('delivery_mode' => 2));
$message->set('application_headers', $headers);
$channel->basic_publish($message, 'delayed_exchange');

echo "message is published.\n";
$channel->close();
$connection->close();