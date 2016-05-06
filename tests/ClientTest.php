<?php
use mprofi\Message;
use mprofi\Client;

/**
 * @author Michał Motyczko <michal@motyczko.pl>
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check if sendint single message works properly
     */
    public function testSendSingleMessage()
    {
        $message = new Message('123456789', 'test message');
        /** @var Client|PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('mprofi\Client')
            ->setMethods(array('sendCurlRequest'))
            ->setConstructorArgs(['qwertyuiop'])
            ->getMock();

        $client->expects($this->once())
            ->method('sendCurlRequest')
            ->with($this->equalTo($client->sendEndpoint), $this->equalTo([
                'recipient' => '123456789',
                'message' => 'test message',
                'apikey' => 'qwertyuiop',
                'reference' => '',
            ]));

        $client->send($message);
    }

    public function testSendSingleMessageInArray()
    {
        $messages = [
            new Message('123456789', 'test message'),
        ];
        /** @var Client|PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('mprofi\Client')
            ->setMethods(array('sendCurlRequest'))
            ->setConstructorArgs(['qwertyuiop'])
            ->getMock();

        $client->expects($this->once())
            ->method('sendCurlRequest')
            ->with($this->equalTo($client->sendEndpoint), $this->equalTo([
                'recipient' => '123456789',
                'message' => 'test message',
                'apikey' => 'qwertyuiop',
                'reference' => '',
            ]));

        $client->send($messages);
    }

    public function testSendBulkMessages()
    {
        $messages = [
            new Message('123456789', 'first test message'),
            new Message('123456780', 'second test message'),
        ];
        /** @var Client|PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('mprofi\Client')
            ->setMethods(array('sendCurlRequest'))
            ->setConstructorArgs(['qwertyuiop'])
            ->getMock();

        $client->expects($this->once())
            ->method('sendCurlRequest')
            ->willReturn(["result" => ["id" => 56], ["id" => 57]])
            ->with($this->equalTo($client->sendBulkEndpoint), $this->equalTo([
                'apikey' => 'qwertyuiop',
                'messages' => [
                    ['recipient' => '123456789', 'message' => 'first test message', 'reference' => ''],
                    ['recipient' => '123456780', 'message' => 'second test message', 'reference' => ''],
                ],
            ]));

        $client->send($messages);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage You must pass at least one message
     */
    public function testSendNoMessagesException()
    {
        $messages = [];
        /** @var Client|PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('mprofi\Client')
            ->setMethods(array('sendCurlRequest'))
            ->setConstructorArgs(['qwertyuiop'])
            ->getMock();

        $client->send($messages);
    }

    public function testCreateUrl()
    {
        $client = new Client('qwertyuiop');

        $this->assertEquals('https://api.mprofi.pl/1.0/send/', $client->createUrl($client->sendEndpoint));
        $this->assertEquals('https://api.mprofi.pl/1.0/sendbulk/', $client->createUrl($client->sendBulkEndpoint));
        $this->assertEquals('https://api.mprofi.pl/1.0/status/?apikey=qwertyuiop&id=23421', $client->createUrl($client->statusEndpoint, ['apikey' => 'qwertyuiop', 'id' => 23421]));
    }
}
