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



# Usage

# Nodes
The system supports scalar nodes, collections through Doctrine collections, Doctrine entities and pagerfanta paging 
collections. It should be possible to add different kind of nodes by implementing a `NodeHandler` as found in the 
`NodeHandler` directory.


### Scalars
All scalar elements (when returned `true` by `is_scalar()`) are mapped directly as-is. 

    $context = new SerializerContext();
    $data = $serializer->serialize('foobar', $context);
    var_dump($data);
    // string(6) "foobar"
    

### Collections
A collection is anything that implements `\Traversable` or an `array`. It returns an array of `elements`, with an 
`count` indiciating how many elements there stored. The elements are not paged like in `PagerFanta` nodes.
 
 
     $context = new SerializerContext();
     $data = $serializer->serialize('foobar', $context);
     var_dump($data);
     // string(6) "foobar"


### Pagerfanta collections 

### Doctrine entity mappings
Doctrine entities are mapped through special `mapping` classes.


# Output adapters
The serializer supports different output formats through `OutputAdapter`s. These adapters should be capable to convert 
the given arrays as compiled from the `Data` structures into the given output. For now
