# Data Transfer Object (DTO)

[![Build Status](https://travis-ci.org/fireproofsocks/dto.svg?branch=master)](https://travis-ci.org/fireproofsocks/dto) [![codecov](https://codecov.io/gh/fireproofsocks/dto/branch/master/graph/badge.svg)](https://codecov.io/gh/fireproofsocks/dto)


A Data Transfer Object (DTO) is a container used to pass structured data between layers in your application, similiar in concept to Martin Fowler's [Transfer Objects](http://martinfowler.com/eaaCatalog/dataTransferObject.html) or [Value Objects](https://en.wikipedia.org/wiki/Value_object).  DTOs are a helpful counterpart to the [Data Accessor Object (DAO)](https://en.wikipedia.org/wiki/Data_access_object) or [Repository](https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/) patterns.

This package aims to provide a quick and easy way to define structured objects, filter the data (e.g. as integers, boolean, hashes, etc.), and reuse structures between objects (e.g. a "Person" DTO might reference an "MailingAddress" DTO).

This package was inspired by the [JSON Schema](http://json-schema.org/) specification which allows you to describe the format of your JSON documents.

## A Few Examples

- In Views: Instead of passing your view layer just any array, you can define a DTO and use type-hinting to ensure that your views will always have the data attributes they need. 
- For Caching: Instead of relying on your own haphazard convention, you can store and retrieve a specific DTO class from cache without having to guess which attributes or array keys are available. 
- Service Classes: when your service class expects to operate on a specific type of data.
- Result Sets: instead of returning an array of stdClass objects or associative arrays from a database lookup, a DTO can describe the result set (as well as the individual records).    

Read more in the [DTO Wiki](https://github.com/fireproofsocks/dto/wiki)


TODO v2:

pattern, e.g. in 

```
"properties": {
    "type": { "enum": [ "disk" ] },
    "label": {
        "type": "string",
        "pattern": "^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$"
    }
}
```


enum support

int, numbers, strings with validation stuff added
```
"minimum": 0,
"exclusiveMinimum": true
```

How to type-hint different DTO classes when really, the power behind them is in the JSON Schema class?
I would prefer to avoid having to customize 2 classes for every data structure, e.g. ProductDto.php, ProductSchema
.php...
So that would mean that the Dto class would need to be able to define its own schema.

Problem: "type" is an array, e.g. a nullable string: `"type": ["string", "null"]`

You pass an integer, so it's not a clean match, and you must do a TypeConverter operation.  Which one?

Answer: probably best would be to use the first type that is defined (string in this example)... but what about in other cases?
There should be some way to identify the best guess of which converter to use.


Problem: "type" is an array, e.g. a nullable object: `"type": ["object", "null"]`

Hydration is passed a "null" value.  

Answer: store null as a scalar.

Problem: "type" is array, `["string", "array"]`.  You set a string value first, e.g. x=dog.  Then you later append a value to 
that property.  

You need to make sure that the original scalar value is included in the array.

Solution: store scalar values in 1st position of the ArrayObject.

------------------------------------

# Version History

## 1.0.8

- Fixes for anonymous hashes of DTOs injected into the constructor. 
- Corrected namespaces in tests.

## 1.0.7

Fixes for arrays of DTOs injected into the constructor as arrays.

## 1.0.6

Fixes for hierarchical data injected into the constructor.

## 1.0.5 

Initial release with some grooming as documentation was added.