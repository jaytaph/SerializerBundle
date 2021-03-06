<?php

namespace Noxlogic\SerializerBundle\Tests\Service;


use Noxlogic\SerializerBundle\Service\SerializerContext;

class SerializerContextTest extends \PHPUnit_Framework_TestCase
{

    function testCreate() {
        $context = SerializerContext::create();

        $this->assertInstanceOf('Noxlogic\SerializerBundle\Service\SerializerContext', $context);
    }

    function testGroups() {
        $context = SerializerContext::create();
        $this->assertEquals($context->getGroups(), array('DEFAULT'));

        $this->assertTrue($context->hasGroup('default'));
        $this->assertTrue($context->hasGroup('DEFAULT'));
        $this->assertFalse($context->hasGroup('FOO'));

        $context->setGroups(array('foo','bar'));
        $this->assertEquals($context->getGroups(), array('FOO', 'BAR'));

        $this->assertFalse($context->hasGroup('default'));
        $this->assertFalse($context->hasGroup('DEFAULT'));
        $this->assertTrue($context->hasGroup('FOO'));
    }

    function testVersion() {
        $context = SerializerContext::create();
        $this->assertNull($context->getVersion());

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

    function testCollection() {
        $context = SerializerContext::create();

        $this->assertFalse($context->isInCollection());
        $context->setInCollection(true);
        $this->assertTrue($context->isInCollection());
        $context->setInCollection(false);
        $this->assertFalse($context->isInCollection());
    }

}
