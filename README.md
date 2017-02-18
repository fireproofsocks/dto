# Data Transfer Object (DTO)

[![Build Status](https://travis-ci.org/fireproofsocks/dto.svg?branch=master)](https://travis-ci.org/fireproofsocks/dto) [![codecov](https://codecov.io/gh/fireproofsocks/dto/branch/master/graph/badge.svg)](https://codecov.io/gh/fireproofsocks/dto)

This package provides a quick way to define structured objects (DTOs) using the [JSON Schema](http://json-schema.org/) 
standard.  

A Data Transfer Object (DTO) is an object used to pass typed data between layers in your application, similiar in 
concept to Martin Fowler's [Transfer Objects](http://martinfowler.com/eaaCatalog/dataTransferObject.html) or 
[Value Objects](https://en.wikipedia.org/wiki/Value_object).  

DTOs are a helpful counterpart to the [Data Accessor Object (DAO)](https://en.wikipedia.org/wiki/Data_access_object) or [Repository](https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/) patterns.


## Possible Uses

- In APIs: Consume a JSON Schema API at runtime without needing to parse data formats.
- In Views: Instead of passing your view layer just any array, you can define a DTO and use type-hinting to ensure that your views will always have the data attributes they need. 
- For Caching: Instead of relying on your own haphazard convention, you can store and retrieve a specific DTO class from cache without having to guess which attributes or array keys are available. 
- Service Classes: when your service class expects to operate on a specific type of data.
- Result Sets: instead of returning an array of stdClass objects or associative arrays from a database lookup, a DTO can describe the result set as well as the individual records.    

Read more in the [DTO Wiki](https://github.com/fireproofsocks/dto/wiki)


## TODO:

- property dependencies https://spacetelescope.github.io/understanding-json-schema/reference/object.html
- schema dependencies (extend the schema)
- room for other versions JSON Schema (v5 is coming!)

------------------------------------

# Version History

## 3.0.0

- Integration of the JSON Schema 4 specification to drive all structure and type definitions.

## 1.0.8

- Fixes for anonymous hashes of DTOs injected into the constructor. 
- Corrected namespaces in tests.

## 1.0.7

Fixes for arrays of DTOs injected into the constructor as arrays.

## 1.0.6

Fixes for hierarchical data injected into the constructor.

## 1.0.5 

Initial release with some grooming as documentation was added.