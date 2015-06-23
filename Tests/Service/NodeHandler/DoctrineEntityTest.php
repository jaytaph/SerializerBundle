<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\NodeHandler\DoctrineEntity;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Entity\Account;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Entity\Nomapper;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Entity\User;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Root;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class DoctrineEntityTest extends \PHPUnit_Framework_TestCase
{
    /* @var Serializer */
    protected $serializer;

    /* @var Registry */
    protected $mockRegistry;

    /* @var RouterInterface */
    protected $mockRouter;

    /* @var ContainerInterface */
    protected $mockContainer;

    function setUp() {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter, $this->mockContainer);
    }


    function testIncorrectDoctrineEntityNode()
    {
        $context = new SerializerContext();

        $node = new DoctrineEntity();
        $output = $node->handle('foobar', $this->serializer, $context);
        $this->assertNull($output);
    }

    function testNoMapping()
    {
        $context = new SerializerContext();

        $entity = new Account();

        $node = new DoctrineEntity();
        $output = $node->handle($entity, $this->serializer, $context);
        $this->assertNull($output);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    function testNomapperInterface()
    {
        $context = new SerializerContext();

        $entity = new Nomapper();

        $node = new DoctrineEntity();
        $output = $node->handle($entity, $this->serializer, $context);
        $this->assertNull($output);
    }

    function testMappingInSameDir()
    {
        $context = new SerializerContext();

        $entity = new Root();

        $node = new DoctrineEntity();
        $output = $node->handle($entity, $this->serializer, $context);
        $this->assertNull($output);
    }

    function testOutput() {
        $context = new SerializerContext();

        $entity = new User();
        $entity->setFirstname('john');
        $entity->setLastname('doe');

        $node = new DoctrineEntity();
        $data = $node->handle($entity, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['firstname'], 'john');
        $this->assertEquals($output['lastname'], 'doe');
        $this->assertEquals($output['fullname'], 'john doe');
        $this->assertEquals($output['_links']['self']['href'], 'http://www.google.com');
    }

}
