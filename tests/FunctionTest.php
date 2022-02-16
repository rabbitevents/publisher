<?php

namespace RabbitEvents\Event\Tests;

use Illuminate\Container\Container;
use RabbitEvents\Event\Publisher;
use RabbitEvents\Event\ShouldPublish;
use RabbitEvents\Event\Support\AbstractPublishableEvent;

class FunctionTest extends TestCase
{
    private $publisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publisher = \Mockery::spy(Publisher::class);
        Container::getInstance()->instance(Publisher::class, $this->publisher);
    }

    public function testCallPublishFunctionWithEventNameAndPayload()
    {
        publish('item.created', ['pay' => 'load']);

        $this->publisher
            ->shouldHaveReceived()
            ->publish(\Mockery::type(ShouldPublish::class))
            ->once();
    }

    public function testPublishWithAnEventClass()
    {
        $event = new class extends AbstractPublishableEvent {
            public function publishEventKey(): string
            {
                return 'item.created';
            }

            public function toPublish(): mixed
            {
                return ['pay' => 'load'];
            }
        };

        publish($event);

        $this->publisher->shouldHaveReceived()
            ->publish($event);
    }
}
