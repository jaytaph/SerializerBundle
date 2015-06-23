Serializer bundle
-----------------

A serializer bundle, inspired by the JMSSerializer and HateoasBundle.

Simpler in design, as we don't need a lot of features, but most importantly,
instead of doing everything within yaml files or annotations, all mappings are done
through custom Mapping files. This allows to decouple database entities and final 
output even more. 

Unless you know what you are doing, I would not recommend using this bundle 
directly without making sure that JMSSerializer & HateoasBundle doesn't suit your 
needs first.


# Installation

* Install through composer:

<pre><code>
    php composer.phar require "noxlogic/serializer-bundle"
</code></pre>

* Add the bundle to your `AppKernel.php`:

<pre><code>
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Noxlogic\SerializerBundle\NoxlogicSerializerBundle(),
                ...
            );
</code></pre>

# Usage

Once installed, the bundle is ready to run. In your controller:

<pre><code>
function indexAction() {
    $element = new \DateTime();
    
    $serializer = $this->get('noxlogic.serializer');
    $data = $serializer->serializer($element);
    return $serializer->createResponse($data, 'json');
}
</code></pre>

This will simply output the time as a string within json. But obviously, you want more complex serialization.

We assume you have a simple entity in `DefaultBundle\Entity\User` which holds (at least) the user's first and last name.

We create a mapping file that maps fields from the entity to a more generic structure that can be converted to our 
output.

<pre><code>
<?php

namespace defaultBundle\Mapping;

use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Service\SerializerMapping;
use DefaultBundle\SerializerBundle\Entity\User;


class UserMapping implement SerializerMapping 
{
    public function mapping($entity, Serializer $serializer, SerializerContext $context)
    {
        /* @var User $entity */
        return Data::create()
            ->addState('firstname', $entity->getFirstName())
            ->addState('lastname', $entity->getLastName())
            ->addState('fullname', $entity->getFirstName().' '.$entity->getLastName())
            ->addLink('self', 'http://www.google.com')
        ;
    }
}
</code></pre>

Now, all we need to do is add a user element to the serializer:

<pre><code>
function indexAction() {
    $user = new User();
    $user->setFirstName('john');
    $user->setLastName('doe');
    
    $serializer = $this->get('noxlogic.serializer');
    $data = $serializer->serializer($user);
    return $serializer->createResponse($data, 'json');
}
</code></pre>

This will output a JSON response object with firstname, lastname and fullname, including a link to google.com:

<pre><code>
{
    "firstname": "john",
    "lastname": "doe",
    "fullname": "john doe",
    "_links": {
        "self": {
            "href": "http:\/\/www.google.com"
        }
    }
}
</code></pre>

At this point it's quite easy to create complex mappings, collections (paged, doctrinecollections or simple arrays) of 
entities etc.


# Serializer Context
It's possible to add a `SerializerContext` instance to the `serializer()` call in order to change its context. This is 
useful for when you have an entity (and thus mapping) which can change depending on how it is called. For instance, you 
might want to display all details by default, but when somebody requests a 'minimal' version, only a small set of data 
should be returned. This is useful for when you are building API's that might be consumed by systems with enough 
bandwidth, but also by low bandwidth systems like mobile apps that only want to fetch the data it really needs.

 
## Grouping
The previous example is done through "grouping". By default, a serializerContext has a "default" group, which cannot be 
removed. It's possible to add more groups, and return information based on those groups.

<pre><code>
function indexAction(User $user) {
    $serializer = $this->get('noxlogic.serializer');
    return $serializer->createResponse($user, 'json');
    
    /*
    {
        "firstname": "john",
        "lastname": "doe",
        "_links": {
            "self": {
                "href": "http:\/\/api.example.org/user/johndoe"
            }
        }
    }
    */
}
</code></pre>

<pre><code>
function indexAction(User $user) {
    $context = SerializerContext::create()
        ->addGroup('admin')
    ;

    $serializer = $this->get('noxlogic.serializer');
    $data = $serializer->serializer($element);
    return $serializer->createResponse($data, 'json');
    
    /*
    {
        "firstname": "john",
        "lastname": "doe",
        "roles": {
            "ROLE_USER",
            "ROLE_MANAGER",
        },
        "_links": {
            "self": {
                "href": "http:\/\/api.example.org/user/johndoe"
            }
        }
    }
    */
}
</code></pre>

In this example, when adding the `admin` group to the context, it will return additional information, `roles` in this case. 

The `mapping()` method in the `UserMapping` class would look something like this:

<pre><code>
    public function mapping($entity, Serializer $serializer, SerializerContext $context)
    {
        /* @var User $entity */
        $data = Data::create()
            ->addState('firstname', $entity->getFirstName())
            ->addState('lastname', $entity->getLastName())
            ->addState('fullname', $entity->getFirstName().' '.$entity->getLastName())
            ->addLink('self', 'http://www.google.com')
        ;
        
        if ($context->hasGroup('admin')) {
            $data->addState('roles', $entity->getRoles());
        }
        
        return $data;
    }
</code></pre>



## Versioning
When your API evolves, it might be possible that some elements will be added or some will disappear. It is not always 
possible to just remove them from your mapping in order to make sure that older clients won't break. For this, you can 
use the versioning feature of the context:
  
<pre><code>
  function indexAction(User $user) {
      $context = SerializerContext::create()
          ->setVersion('1.2.3')
      ;
/code></pre>

Normally, this version information should be taken from the request like the `Accept` header, for instance, or maybe even 
from the URL: (ie: `http://example.org/api/v1.2.3/...`).

Now you can adjust your mapping dynamically based on the given version:

<pre><code>
    public function mapping($entity, Serializer $serializer, SerializerContext $context)
    {
        /* @var User $entity */
        $data = Data::create()
            ->addState('firstname', $entity->getFirstName())
            ->addState('lastname', $entity->getLastName())
            ->addLink('self', 'http://www.google.com')
        ;
        
        if ($context->sinceVersion('1.2.0')) {
            $data->addState('maiden_name', $entity->getMaidenName());
        ;
        
        if ($context->untilVersion('2.0.0')) {
            $data->addState('fullname', $entity->getFirstName().' '.$entity->getLastName())
        ;
        
        return $data;
    }
</code></pre>

In this example, the 'maiden_name' element is available since version `1.2.0`. Any clients requesting a version below 
this, will not receive this element. Also, the `fullname` is available until version `2.0.0`, excluding version `2.0.0` 
itself. 

Note that versioning will follow semantic versioning: `1.2.3` is larger than `1.2.2` and less than `1.3.10`.



# Nodes
Each element (a `node`), that the serializer encounters will be processed by the node handlers. Each node handler is 
capable of converting the value of a node (a string, a collection, or even a doctrine entity) into either a string or 
to a `Data` structure. The latter is normally done for compound structures so an object gets its properties serialized, 
or serializing an array result in `Data` structure with all elements inside etc.

It's possible to add your own node handlers if needed to the serializer through `addNodeHandler`. Each node handler can 
be placed onto a certain priority ranging from `-255` being the last ones to be called, up to `255`, being the first to 
be called.

Once a node handler handles a node, no other node handlers will be tried.


### Scalars
All scalar elements (when returned `true` by `is_scalar()`) are mapped directly as-is. 

<pre><code>
    $context = new SerializerContext();
    $data = $serializer->serialize('foobar', $context);
    var_dump($data);
    // string(6) "foobar"
</code></pre>
    
### DateTime
DateTime and DateTimeImmutable objects, basically, anything implementing a `DateTimeInterface`, will be converted to
an iso 8601 format.

<pre><code>
    $context = new SerializerContext();
    $data = $serializer->serialize(new \DateTime(), $context);
    var_dump($data);
    // string(6) "2015-02-03T12:34:56+0100"
</code></pre>

### Collections
A collection is anything that implements `\Traversable` or an `array`. It returns an array of `elements`, with an 
`count` indicating how many elements there stored. The elements are not paged like in `PagerFanta` nodes. The elements
themselves are serialized again, so they do not have to be scalars, but can be other object-type elements, and even 
collections (for multi-dimensional collections).

<pre><code>
     $context = new SerializerContext();
     $data = $serializer->serialize(array('foo', 'bar', 'baz'), $context);
     var_dump($data);
     /*
     Array
     (
         [count] => 3
         [elements] => Array
             (
                 [0] => foo
                 [1] => bar
                 [2] => baz
             )
     )
     */
</code></pre>

<pre><code>
     $context = new SerializerContext();
     $data = $serializer->serialize(array('foo', array(1,2,3), 'baz', $context);
     $output = $data->compile();
     print_r($data);
     /*
     Array
     (
         [count] => 3
         [elements] => Array
             (
                 [0] => foo
                 [1] => Array
                     (
                         [count] => 3
                         [elements] => Array
                             (
                                 [0] => 1
                                 [1] => 2
                                 [2] => 3
                             )
                     )
                 [2] => baz
             )
     )
     */
</code></pre>


### Doctrine entity mappings
Doctrine entities (but really, any class if needed) are mapped through special `mapping` classes. These classes are 
nothing more than the entity class name with `Mapping` suffixed (ie: `UserMapping` when the class is called `User`). 
Also, when it detects an `Entity` directory in the FQDN, it will convert this into `Mapping`. 

Thus, an entity with the following FQDN:
    
        \App\MyBundle\Entity\User

will look for the mapping in:

        \App\MyBundle\Mapping\UserMapping

This setup is pretty specific for Symfony and will most likely change in a later stadium (like using @mapping 
annotations to define the actual mapping class for instance).

Each mapping file should implement the `SerializerMapping` interface. The mapping class itself is nothing more than a 
simple `mapping()` method that will convert the given entity into either a `Data` structure for compound elements (most 
likely on objects), or even a single scalar if needed.

<pre><code>
    class UserMapping implements SerializerMapping {
    
        public function mapping($entity, Serializer $serializer, SerializerContext $context)
        {
            return Data::create()
                ->addState('firstname', $entity->getFirstName())
                ->addState('lastname', $entity->getLastName())
                ->addState('fullname', $entity->getFirstName().' '.$entity->getLastName())
                ->addLink('self', 'http://www.google.com')
            ;
        }
    
    }
</code></pre>


### Pagerfanta collections 
When you are in the need of large collections with pagination, you can use the `PagerFantaWrapper` class. Unfortunately, 
in order to make this work, a little bit more work must be done:

1. Create a `CollectionRouting` instance and connect the router to it
2. Create a `PagerFanta` instance.
3. Configure your `PagerFanta` instance with the correct values.
4. Wrap them inside a `PagerFantaWrapper` instance.


The `CollectionRouting` is an object that is needed in order to let `PagerFantaWrapper` know where the links to the 
previous and next pages are. The collection routing is nothing more than a (symfony) route, and optional arguments. With 
this information it will automatically generate the correct urls to navigate, keeping any additional information on the 
query string in tact. 

Next, create the `PagerFanta` instance, by adding elements through an adapter. This is documented at the PagerFanta 
documentation itself. 

Then we must set the `setMaxPerPage` and `setCurrentPage` of the pager to the correct values, most likely taken from 
the current `Request`.

Finally, we can create our `PagerFantaWrapper` by connecting everything together:

    $wrapper = new PagerFantaWrapper($pagerFanta, $collectionRouting, 'page', 'limit', 'users');
    
The 'page' and 'limit' strings are the names of the query string parameters that will be used to set the page number 
and the limit of elements per page (ie: `http://my.tld/users?page=2&limit=10`). The last string `users`, is used to set 
the array name in the `Data` structure. It will default to `elements` if none is given, but often a more descriptive 
name like `users` or `blogposts` is desirable.


<pre><code>
    
    // assuming that `route_to_collection` resolves to: http://example.org/alphabet
    $routing = new CollectionRouting('route_to_collection', array('sort' => 'asc'));
    // We must connect our router to the CollectionRouting manually
    $routing->setRouting($this->getRouter());

    $elements = range('a', 'z');
    $pagerFanta = new PagerFanta(new ArrayAdapter($elements));
    $pagerFanta->setCurrentPage($request->query->get('page'));
    $pagerFanta->setMaxPerPage($request->query->get('limit'));

    
    $wrapper = new PagerFantaWrapper($pagerFanta, $routing, 'page', 'limit', 'letters');
        
    $context = new SerializerContext();
    $data = $serializer->serialize($wrapper, $context);
    
    $output = $data->compile();
    print_r($output);
    
    
    /*
    Array
    (
        [count] => 26
        [pages] => 6
        [letters] => Array
            (
                [0] => f
                [1] => g
                [2] => h
                [3] => i
                [4] => j
            )
        [_links] => Array
            (
                [self] => Array
                    (
                        [href] => http://example.org/alphabet?sort=asc&page=2&limit=5
                    )
                [prev] => Array
                    (
                        [href] => http://example.org/alphabet?sort=asc&page=1&limit=5
                    )
                [next] => Array
                    (
                        [href] => http://example.org/alphabet?sort=asc&page=3&limit=5
                    )
            )
    )
    */
   
</code></pre>




# Output adapters
The serializer supports different output formats through `OutputAdapter`s. These adapters should be capable to convert 
the given arrays as compiled from the `Data` structures into the given output. For now, the serialized is centered 
around converting to `HAL-JSON` format, although it should be trivial to add different output formats as well.

Normally, the output adapter by themselves will just return object a `Response` object with all nessecary settings 
(content-type, body etc). 

<pre><code>
    $data = $serializer->serialize($user);
    $response = $serializer->createResponse($data, 'json');
</code></pre>


## Adding output adapters
It's easy to add additional output adapters through tagged services. Implement the `OutputAdapterInterface` and tag your 
adapter with `noxlogic.serializer.output.adapter`. The bundle will automatically pick up customer adapters and add them 
to the serializer.
