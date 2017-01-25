# Improved Polymorphic Eloquent Builder

Attempting to use `whereHas` queries with standard Eloquent polymorphic relationships will fail, due to Eloquent being unable
to determine the correct model to retrieve.

The 'Improved Polymorphic Eloquent Builder' is a class which extends the Eloquent Builder class that is built in to Laravel 5.1. 
It enables limited use of the `whereHas` method to query Eloquent polymorphic relationships.

## Requirements

You must be using Laravel 5.1 as your framework and Eloquent as your ORM.

## Limitations

Due to the nature of polymorphic relationships, the `whereHas` functionality will only work correctly if the query results
bring back one and only one 'type' of the relationship. If this is not the case, the returned results will be unexpected.

Therefore, it is advisable to use a `where` method to restrict the polymorphic query to a single 'type'.

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