<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{

    function testCreate() {
        $data = Data::create();

        $this->assertInstanceOf('Noxlogic\SerializerBundle\Service\Data', $data);
    }

    function testSimpleCompilation() {
        $data = Data::create();

        $data->addState('foo', 'bar');
        $data->addState('baz', 'qux');

        $output = $data->compile();

        $this->assertEquals($output['foo'], 'bar');
        $this->assertEquals($output['baz'], 'qux');

        $this->assertCount(0, $output['_links']);
    }

    function testComplexLinkCompilation() {
        $data = Data::create();

        $data->addState('foo', 'bar');
        $data->addState('baz', 'qux');
        $data->addLink('l1', 'http://www.google.com');
        $data->addLink('l2', 'http://www.reddit.com', array('rel' => 'nofollow'));

        $output = $data->compile();

        $this->assertEquals($output['foo'], 'bar');
        $this->assertEquals($output['baz'], 'qux');

        $this->assertCount(2, $output['_links']);

        $this->assertEquals($output['_links']['l1']['href'], 'http://www.google.com');
        $this->assertCount(1, $output['_links']['l1']);

        $this->assertEquals($output['_links']['l2']['href'], 'http://www.reddit.com');
        $this->assertEquals($output['_links']['l2']['rel'], 'nofollow');
        $this->assertCount(2, $output['_links']['l2']);
    }

    function testComplexEmbedCompilation() {

        $data2 = Data::create();
        $data2->addState('deep', 'down');

        $data1 = Data::create();
        $data1->addState('foo', 'bar');
        $data1->addLink('l2', 'http://www.reddit.com');
        $data1->addEmbedded('e2', $data2);

        $data = Data::create();
        $data->addLink('l1', 'http://www.google.com');
        $data->addEmbedded('e1', $data1);

        $output = $data->compile();

        $this->assertCount(1, $output['_links']);

        $this->assertEquals('down', $output['_embedded']['e1']['_embedded']['e2']['deep']);
        $this->assertEquals('http://www.reddit.com', $output['_embedded']['e1']['_links']['l2']['href']);
    }
}
