<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\SerializerContext;

class SerializerContextTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $context = SerializerContext::create();

        $this->assertInstanceOf('Noxlogic\SerializerBundle\Service\SerializerContext', $context);
    }

    public function testGroups()
    {
        $context = SerializerContext::create();
        $this->assertEquals($context->getGroups(), array());

        $this->assertTrue($context->hasGroup('default'));
        $this->assertTrue($context->hasGroup('DEFAULT'));
        $this->assertFalse($context->hasGroup('FOO'));

        $context->setGroups(array('foo', 'bar'));
        $this->assertEquals($context->getGroups(), array('FOO', 'BAR'));

        $this->assertTrue($context->hasGroup('default'));
        $this->assertTrue($context->hasGroup('DEFAULT'));
        $this->assertTrue($context->hasGroup('FOO'));

        $context->addGroup('baz');
        $this->assertTrue($context->hasGroup('FOO'));
        $this->assertTrue($context->hasGroup('baz'));

        $context->removeGroup('baz');
        $this->assertTrue($context->hasGroup('FOO'));
        $this->assertFalse($context->hasGroup('baz'));
    }

    public function testVersion()
    {
        $context = SerializerContext::create();
        $this->assertNull($context->getVersion());
        $this->assertTrue($context->sinceVersion('1.2.3'));
        $this->assertTrue($context->untilVersion('1.2.3'));

        $context->setVersion('1.2.3');
        $this->assertEquals('1.2.3', $context->getVersion());

        $this->assertTrue($context->sinceVersion('1.2.3'));
        $this->assertFalse($context->sinceVersion('1.2.4'));
        $this->assertFalse($context->sinceVersion('2.2.3'));
        $this->assertTrue($context->sinceVersion('1.2.2'));
        $this->assertTrue($context->sinceVersion('1.2.0'));

        $this->assertFalse($context->untilVersion('1.2.3'));
        $this->assertTrue($context->untilVersion('1.2.4'));
        $this->assertTrue($context->untilVersion('2.2.3'));
        $this->assertFalse($context->untilVersion('1.2.2'));
        $this->assertFalse($context->untilVersion('1.2.0'));
    }

    public function testStack()
    {
        $context = SerializerContext::create();
        $this->assertFalse($context->has('foo'));

        $context->push('baz');
        $context->push('bar');
        $this->assertEquals('bar', $context->pop());

        $context->pop();
        $context->pop();
        $context->pop();

        $context->push('foo');
        $context->push('bar');
        $context->push('baz');
        $this->assertTrue($context->has('foo'));
        $this->assertTrue($context->has('baz'));

        $context->pop();
        $this->assertFalse($context->has('baz'));
    }

    public function testRecursive()
    {
        $context = SerializerContext::create();
        $this->assertFalse($context->canRecurse());

        $context->setRecursive(true);
        $this->assertTrue($context->canRecurse());

        $context->setRecursive(false);
        $this->assertFalse($context->canRecurse());
    }

    public function testDepths()
    {
        $context = SerializerContext::create();
        $this->assertEquals(10, $context->getMaximumDepth());
        $this->assertEquals(0, $context->getCurrentDepth());

        $context->setMaximumDepth(5);
        $this->assertEquals(5, $context->getMaximumDepth());

        $context->setCurrentDepth(2);
        $this->assertEquals(2, $context->getCurrentDepth());
    }


}
