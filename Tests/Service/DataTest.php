<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $data = Data::create();

        $this->assertInstanceOf('Noxlogic\SerializerBundle\Service\Data', $data);
    }

    public function testSimpleCompilation()
    {
        $data = Data::create();

        $data->addState('foo', 'bar');
        $data->addState('baz', 'qux');

        $output = $data->compile();

        $this->assertEquals($output['foo'], 'bar');
        $this->assertEquals($output['baz'], 'qux');

        $this->assertCount(0, $output['_links']);
    }

    public function testArrayStates()
    {
        $data = Data::create()
            ->addState('first', 'post')
            ->addState('root', 'up')
            ->addState('root', 'and down')
        ;
        $output = $data->compile();

        $this->assertEquals($output['first'], 'post');
        $this->assertCount(2, $output['root']);
        $this->assertEquals($output['root'][0], 'up');
        $this->assertEquals($output['root'][1], 'and down');

        $this->assertCount(0, $output['_links']);
    }

    public function testComplexLinkCompilation()
    {
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

    public function testComplexEmbedCompilation()
    {
        $data2 = Data::create()
            ->addState('deep', 'down')
        ;

        $data1 = Data::create()
            ->addState('foo', 'bar')
            ->addLink('l2', 'http://www.reddit.com')
            ->addEmbedded('e2.1', $data2)
        ;

        $data = Data::create()
            ->addLink('l1', 'http://www.google.com', array('rel' => 'relation'))
            ->addEmbedded('e1.1', $data1)
            ->addEmbedded('e1.2', $data2)
            ->addEmbedded('e1.2', $data1)
        ;

        $output = $data->compile();

        $this->assertCount(1, $output['_links']);
        $this->assertEquals($output['_links']['l1']['href'], 'http://www.google.com');
        $this->assertEquals($output['_links']['l1']['rel'], 'relation');

        $this->assertEquals('down', $output['_embedded']['e1.1']['_embedded']['e2.1']['deep']);
        $this->assertEquals('down', $output['_embedded']['e1.2'][0]['deep']);
        $this->assertEquals('http://www.reddit.com', $output['_embedded']['e1.1']['_links']['l2']['href']);
    }

    public function testAddState()
    {
        $data = Data::create();
        $data->addState('foo', 'bar');

        $output = $data->compile();
        $this->assertArraySubset(array('foo' => 'bar', '_links' => array()), $output);

        $data1 = Data::create();
        $data1->addState('bar', 'baz');

        $data2 = Data::create();
        $data2->addState('foo', $data1);

        $output = $data2->compile();
        $this->assertArraySubset(array('foo' => array('bar' => 'baz', '_links' => array()), '_links' => array()), $output);
    }

    public function testDisplayLinks()
    {
        $data = Data::create()
            ->addState('foo', 'bar')
        ;
        $output = $data->compile();
        $this->assertArrayHasKey('_links', $output);

        $data = Data::create()
            ->alwaysDisplayLinks(false)
            ->addState('foo', 'bar')
        ;
        $output = $data->compile();
        $this->assertArrayNotHasKey('_links', $output);

        $data = Data::create()
            ->alwaysDisplayLinks(false)
            ->addState('foo', 'bar')
            ->addLink('self', 'http://www.google.com')
        ;
        $output = $data->compile();
        $this->assertArrayHasKey('_links', $output);
    }
}
