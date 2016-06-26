# Data Transfer Object (DTO)

A Data Transfer Object (DTO) seeks to simplify object definitions and structure by providing an easy way to define an object's allowed attributes and a type-agnostic interface for getting or setting them (array or object notation are allowed).  At its heart a DTO puts a structure around PHP's `ArrayObject` storage so you can define a default set of values and employ simple gate-keeping for enforcing your object's structure.

DTOs offer a great way of enforcing a "contract" of sorts between layers of your application.  Instead of passing your view layer just any array, you can define a DTO and use type-hinting to ensure that your views will always have the data attributes they need. 

## Usage

### Accessor

Out of the box, you can use an instance of the `Dto` class to normalize an object or array for uniform access. 

```
$D = new \Dto\Dto();
$D['cousin']['firstname'] = 'Lurch'; // Set as an array
$Lurch = $D->cousin->firstname; // Get as an object
```

Data normalization makes it easier to set and get your data without worrying about whether some nodes were arrays or objects.  In practice, this means it's easier to serialize and unserialze your data (e.g. for caching operations), and you can use `json_encode` and `json_decode` since you do not need to be concerned about the specific object types.
 
### Defining your Own DTO Templates
 
Loosely typed (just like PHP and JSON):
 
- boolean
- sclar (i.e. any string value)
- integer
- numeric (i.e. floats, or any number with a decimal)
 
 
### More Complex Data Types
 
Hash
- key (string)
- value
 
Ambiguous Hash
 
- key (string)
- value
    - template
    - another DTO
    
 Used when we don't know exactly what keys to expect
 
 GOTCHAS: setting values onto new (previously non-existent) keys.
 
 Array
 
 A list.  Not an object!
 
 DTOs
 
 Re-use other components!
 
 Nullable
 
 
 Gotchas:
 
 `print_r()` -- although DTOs implement PHP's `ArrayObject` storage, they are not strictly simple value arrays. 