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
