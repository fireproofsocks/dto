# Data Transfer Object (DTO)

[![Build Status](https://travis-ci.org/fireproofsocks/dto.svg?branch=master)](https://travis-ci.org/fireproofsocks/dto) [![codecov](https://codecov.io/gh/fireproofsocks/dto/branch/master/graph/badge.svg)](https://codecov.io/gh/fireproofsocks/dto)


A Data Transfer Object (DTO) is a container used to pass structured data between layers in your application.  They can be objects or arrays.  They usually do not have behaviors and instead are primarily containers for values (similiar in concept to Martin Fowler's [Transfer Objects](http://martinfowler.com/eaaCatalog/dataTransferObject.html) or [Value Objects](https://en.wikipedia.org/wiki/Value_object)).  DTOs are often the counterpart of the [Data Accessor Object (DAO)](https://en.wikipedia.org/wiki/Data_access_object) pattern, and DTOs can be useful in implementing the [Repository](https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/) pattern.

This package aims to provide a quick and easy way to define structured objects, filter the data (e.g. as integers, boolean, hashes, etc.), and reuse structures between objects (e.g. a "Person" DTO might reference an "MailingAddress" DTO).

This package was inspired by the [JSON Schema](http://json-schema.org/) specification which allows you to describe the format of your JSON documents.

## A Few Examples

- In Views: Instead of passing your view layer just any array, you can define a DTO and use type-hinting to ensure that your views will always have the data attributes they need. 
- For Caching: Instead of relying on your own haphazard convention, you can store and retrieve a specific DTO class from cache without having to guess which attributes or array keys are available. 
- Service Classes: when your service class expects to operate on a specific type of data.
- Result Sets: instead of returning an array of stdClass objects or associative arrays from a database lookup, a DTO can describe the result set (as well as the individual records).    

Read more in the [DTO Wiki](https://github.com/fireproofsocks/dto/wiki)