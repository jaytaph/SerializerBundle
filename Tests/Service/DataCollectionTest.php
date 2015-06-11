<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\DataCollection;

class DataCollectionTest extends \PHPUnit_Framework_TestCase
{

    function testCreate() {
        $data = DataCollection::create();

        $this->assertInstanceOf('Noxlogic\SerializerBundle\Service\DataCollection', $data);
    }

    function testSimpleCompilation() {
        $data1 = Data::create();
        $data1->addState('foo', 'bar');

        $data2 = Data::create();
        $data2->addState('baz', 'qux');


        $collection = DataCollection::create();
        $output = $collection->compile();
        $this->assertCount(0, $output['_links']);


        $collection->addElement($data1);
        $output = $collection->compile();
        $this->assertCount(1, $output['_embedded']);
        $this->assertEquals($output['_embedded'][0]['foo'], 'bar');


        $collection->addElement($data2);
        $output = $collection->compile();
        $this->assertCount(2, $output['_embedded']);
        $this->assertEquals($output['_embedded'][0]['foo'], 'bar');
        $this->assertEquals($output['_embedded'][1]['baz'], 'qux');

    }

}
