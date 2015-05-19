Using Elasticsearch and elastic/elasticsearch-php
=================================================

[elastic/elasticsearch-php](https://github.com/elastic/elasticsearch-php) is one
of the targets supported by RulerZ. It allows tne engine to query an Elasticsearch
server.

This cookbook will show you how to retrieve objects using the official client
for Elasticsearch and RulerZ.

Here is a summary of what you will have to do:

 * [configure elastic/elasticsearch-php](#configure-elastic-elasticsearch-php);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure elastic/elasticsearch-php

This subject won't be directly treated here. You can either follow the [official
documentation](http://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_installation_2.html)
or use a bundle/module/whatever the framework you're using promotes.

## Configure RulerZ

Once elastic/elasticsearch-php is installed and configured we can the RulerZ engine:

```php
use RulerZ\Executor\ElasticsearchExecutor;
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\RulerZ;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new ElasticsearchExecutor(), // this line is Elasticsearch-specific
        // other executors...
    ]
);
```

The only Elasticsearch-related configuration is the `ElasticsearchExecutor` being added
to the list of the known executors.

## Filter your target

Now that both elastic/elasticsearch-php and RulerZ are ready, you can use them
to retrieve data.

The `ElasticsearchExecutor` instance that we previously injected into the RulerZ
engine only knows how to use `Elasticsearch\Client` objects so the first step is
creating one:

```php
$client = new Elasticsearch\Client();
```

And as usual, we call RulerZ with our target (the `Search` object) and our
rule.
RulerZ will find the right executor for the given target and use it to filter
the data, or in our case to retrieve data from Elasticsearch.

```php
$rule  = 'group in :groups and points > :points';
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];
$executionContext = [
    'index' => 'index_name',
    'type'  => 'type_name',
];

var_dump($rulerz->filter($client, $rule, $parameters, $executionContext));
```

**N.B**: you'll notice an unusual variable named `$executionContext`. It
contains a few parameters needed by the `ElasticsearchExecutor` in order to make
the request and are mandatory.

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
