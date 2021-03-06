<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace NexmoTest\Calls;


use Nexmo\Calls\Call;
use Nexmo\Calls\Endpoint;
use Nexmo\Calls\Webhook;

class CallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Call
     */
    protected $call;

    public function setUp()
    {
        $this->call = new Call();
    }

    public function testConstructWithId()
    {
        $call = new Call('3fd4d839-493e-4485-b2a5-ace527aacff3');
        $this->assertSame('3fd4d839-493e-4485-b2a5-ace527aacff3', $call->getId());
    }

    public function testToIsSet()
    {
        $this->call->setTo('14845551212');
        $this->assertSame('14845551212', (string) $this->call->getTo());
        $this->assertSame('14845551212', $this->call->getTo()->getId());
        $this->assertSame('phone', $this->call->getTo()->getType());

        $data = $this->call->jsonSerialize();

        $this->assertArrayHasKey('to', $data);
        $this->assertInternalType('array', $data['to']);
        $this->assertArrayHasKey('number', $data['to'][0]);
        $this->assertArrayHasKey('type', $data['to'][0]);
        $this->assertEquals('14845551212', $data['to'][0]['number']);
        $this->assertEquals('phone', $data['to'][0]['type']);

        $this->call->setTo(new Endpoint('14845551212'));
        $this->assertSame('14845551212', (string) $this->call->getTo());
        $this->assertSame('14845551212', $this->call->getTo()->getId());
        $this->assertSame('phone', $this->call->getTo()->getType());

        $data = $this->call->jsonSerialize();

        $this->assertArrayHasKey('to', $data);
        $this->assertInternalType('array', $data['to']);
        $this->assertArrayHasKey('number', $data['to'][0]);
        $this->assertArrayHasKey('type', $data['to'][0]);
        $this->assertEquals('14845551212', $data['to'][0]['number']);
        $this->assertEquals('phone', $data['to'][0]['type']);
    }

    public function testFromIsSet()
    {
        $this->call->setFrom('14845551212');
        $this->assertSame('14845551212', (string) $this->call->getFrom());
        $this->assertSame('14845551212', $this->call->getFrom()->getId());
        $this->assertSame('phone', $this->call->getFrom()->getType());

        $data = $this->call->jsonSerialize();

        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('number', $data['from']);
        $this->assertArrayHasKey('type', $data['from']);
        $this->assertEquals('14845551212', $data['from']['number']);
        $this->assertEquals('phone', $data['from']['type']);

        $this->call->setFrom(new Endpoint('14845551212'));
        $this->assertSame('14845551212', (string) $this->call->getFrom());
        $this->assertSame('14845551212', $this->call->getFrom()->getId());
        $this->assertSame('phone', $this->call->getFrom()->getType());

        $data = $this->call->jsonSerialize();

        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('number', $data['from']);
        $this->assertArrayHasKey('type', $data['from']);
        $this->assertEquals('14845551212', $data['from']['number']);
        $this->assertEquals('phone', $data['from']['type']);
    }

    public function testWebhooks()
    {
        $this->call->setWebhook(Call::WEBHOOK_ANSWER, 'http://example.com');

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('answer_url', $data);
        $this->assertCount(1, $data['answer_url']);
        $this->assertEquals('http://example.com', $data['answer_url'][0]);

        $this->call->setWebhook(new Webhook(Call::WEBHOOK_ANSWER, 'http://example.com'));

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('answer_url', $data);
        $this->assertCount(1, $data['answer_url']);
        $this->assertEquals('http://example.com', $data['answer_url'][0]);

        $this->call->setWebhook(new Webhook(Call::WEBHOOK_ANSWER, ['http://example.com', 'http://example.com/test']));

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('answer_url', $data);
        $this->assertCount(2, $data['answer_url']);
        $this->assertEquals('http://example.com', $data['answer_url'][0]);
        $this->assertEquals('http://example.com/test', $data['answer_url'][1]);

        $this->call->setWebhook(new Webhook(Call::WEBHOOK_ANSWER, 'http://example.com', 'POST'));

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('answer_method', $data);
        $this->assertEquals('POST', $data['answer_method']);
    }

    public function testTimers()
    {
        $this->call->setTimer(Call::TIMER_LENGTH, 10);

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('length_timer', $data);
        $this->assertEquals(10, $data['length_timer']);
    }

    public function testTimeouts()
    {
        $this->call->setTimeout(Call::TIMEOUT_MACHINE, 10);

        $data = $this->call->jsonSerialize();
        $this->assertArrayHasKey('machine_timeout', $data);
        $this->assertEquals(10, $data['machine_timeout']);
    }

    public function testHydrate()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/responses/call.json'), true);
        $this->call->JsonUnserialize($data);

        $this->assertEquals('phone', $this->call->getTo()->getType());
        $this->assertEquals('phone', $this->call->getFrom()->getType());

        $this->assertEquals('14845552194', $this->call->getTo()->getId());
        $this->assertEquals('14841113423', $this->call->getFrom()->getId());

        $this->assertEquals('14845552194', $this->call->getTo()->getNumber());
        $this->assertEquals('14841113423', $this->call->getFrom()->getNumber());

        $this->assertEquals('3fd4d839-493e-4485-b2a5-ace527aacff3', $this->call->getId());
        $this->assertEquals('completed', $this->call->getStatus());
        $this->assertEquals('outbound', $this->call->getDirection());

        $this->assertInstanceOf('Nexmo\Conversations\Conversation', $this->call->getConversation());
        $this->assertEquals('0f9f56dd-9c90-4fd0-a40e-d075f009d2ee', $this->call->getConversation()->getId());


    }
}
