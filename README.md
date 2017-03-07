# Improved Polymorphic Eloquent Builder

Attempting to use `whereHas` queries with standard Eloquent polymorphic relationships will fail, due to Eloquent being unable
to determine the correct model to retrieve. You may receive an error similar to the one below as Eloquent tries to build the 
query using columns from the model without including its table name.

```
QueryException in Connection.php line 662:
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'column_name' in 'where clause'
```

The 'Improved Polymorphic Eloquent Builder' is a class which extends the Eloquent Builder class that is built in to Laravel 5.1. 
It enables limited use of the `whereHas` method to query Eloquent polymorphic relationships.

## Requirements

You must be using Laravel 5.1 as your framework and Eloquent as your ORM.

## Usage

When performing a `whereHas` query, you must specify the morph types as the 5th argument. Morph types refers to an array
of the polymorphic types you wish to filter by. These should be presented as strings equal to one or more of the
possible values the `[...]_type` field of the polymorphic relationship in question.

The example below selects all `variation` records that have related `model` record  of type `bags`, with a `brand_id` of 2.

```php
Variation::whereHas('model', function($query) use ($brandId) {
    $query->where('brand_id', 2);
}, '>=', 1, ['bags'])->get();
```

A database structure for this example would be similar to the following. The polymorphic relationship fields are highlighted
in **bold**.

| variations |
|---|
| id |
| **model_type** ('bags', 'sunglasses', etc.) |
| **model_id** |

| bags |
|---|
| id |
| brand_id |

## Installation

Simply require this package, using Composer, in the root directory of your project.

```
composer require rapidwebltd/improved-polymorphic-eloquent-builder
```

Then add the following method to any Eloquent models you wish to use this alternative builder. This will usually be any 
model(s) containing methods which return `morphTo()` relationship(s).

```php
  // Override the default Eloquent Builder for the Variation model improve polymorphic relationships
  public function newEloquentBuilder($query)
  {
      return new \RapidWeb\ImprovedPolymorphicEloquentBuilder\Builder($query);
  }
  ```