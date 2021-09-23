![Build Status](https://img.shields.io/github/workflow/status/Neur0toxine/pock/Tests?style=flat-square)
[![Coverage](https://img.shields.io/codecov/c/gh/Neur0toxine/pock/master.svg?logo=codecov&logoColor=white&style=flat-square)](https://codecov.io/gh/Neur0toxine/pock)
[![Latest stable](https://img.shields.io/packagist/v/neur0toxine/pock.svg?style=flat-square)](https://packagist.org/packages/neur0toxine/pock)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/neur0toxine/pock.svg?logo=php&logoColor=white&style=flat-square)](https://packagist.org/packages/neur0toxine/pock)
![License](https://img.shields.io/github/license/Neur0toxine/pock?style=flat-square)

# pock

Easy to use HTTP mocking solution, compatible with PSR-18 and HTTPlug.

Project is still in its early development stage. API can change over time, but I'll try to not introduce breaking changes.
You can find autogenerated documentation [here](https://neur0toxine.github.io/pock/) or look at the examples. API for the mock building can be found
[here](https://neur0toxine.github.io/pock/classes/Pock-PockBuilder.html) and API for the response building (returned from `PockBuilder::reply` call) 
can be found [here](https://neur0toxine.github.io/pock/classes/Pock-PockResponseBuilder.html).

# Examples

Mock JSON API route with Basic authorization, reply with JSON.

```php
use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\PockBuilder;

$builder = new PockBuilder();
$builder->matchMethod(RequestMethod::GET)
    ->matchScheme(RequestScheme::HTTPS)
    ->matchHost('example.com')
    ->matchPath('/api/v1/users')
    ->matchHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic YWxhZGRpbjpvcGVuc2VzYW1l'
    ])
    ->reply(200)
    ->withHeader('Content-Type', 'application/json')
    ->withJson([
        [
            'name' => 'John Doe',
            'username' => 'john',
            'email' => 'john@example.com'
        ],
        [
            'name' => 'Jane Doe',
            'username' => 'jane',
            'email' => 'jane@example.com'
        ],
    ]);

// Pass PSR-18 compatible client to the API client.
$client = new MysteriousApiClient($builder->getClient());
$client->setCredentials('username', 'password');

// Receive mock response.
$response = $client->getUsers();
```

Same mock, but with models! Also, the code itself is slightly shorter.

```php
use Pock\Enum\RequestMethod;
use Pock\PockBuilder;

$builder = new PockBuilder();
$builder->matchMethod(RequestMethod::GET)
    ->matchUri('https://example.com/api/v1/users')
    ->matchHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic YWxhZGRpbjpvcGVuc2VzYW1l'
    ])
    ->reply(200)
    ->withHeader('Content-Type', 'application/json')
    ->withJson([
        // We're assuming here that MysteriousUser's constructor can receive an initial values.
        new MysteriousUser('John Doe', 'john', 'john@example.com'),
        new MysteriousUser('Jane Doe', 'jane', 'jane@example.com'),
    ]);

// Pass PSR-18 compatible client to the API client.
$client = new MysteriousApiClient($builder->getClient());
$client->setCredentials('username', 'password');

// Receive mock response.
$response = $client->getUsers();
```

It is possible to mock a response using DTO's because pock can use third-party serializers under the hood.

# Serializer support

pock supports JMS serializer and Symfony serializer out of the box. Available serializer will be instantiated automatically.
It will be used to serialize requests and responses in mocks which means you actually can pass an entire DTO
into the corresponding methods (for example, `matchJsonBody` as an assertion or `withJsonBody` to generate a response body).

By default, JMS serializer has more priority than the Symfony serializer. You can use methods below before running tests (`bootstrap.php`)
if you want to override default behavior.

```php
use Pock\Factory\JsonSerializerFactory;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\SymfonySerializerDecorator;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

$encoders = [new XmlEncoder(), new JsonEncoder()];
$normalizers = [new ObjectNormalizer()];
$serializer = new SymfonySerializerDecorator(new Serializer($normalizers, $encoders));

JsonSerializerFactory::setSerializer($serializer);
XmlSerializerFactory::setSerializer($serializer);
```

In order to use unsupported serializer you should create a decorator which implements `Pock\Serializer\SerializerInterface`.

# Roadmap to stable

- [x] `at(N)` - execute mock only at Nth call.  
- [x] `always()` - always execute this mock (removes mock expiration).  
- [x] Separate `UniversalMockException` into several exceptions (`PockClientException`, `PockNetworkException`, etc).  
- [x] Add methods for easier throwing of exceptions listed in previous entry.  
- [x] `replyWithCallback` - reply using specified callback.  
- [x] `replyWithFactory` - reply using specified response factory (provide corresponding interface).  
- [x] Compare XML bodies using `DOMDocument`, fallback to text comparison in case of problems.  
- [x] Regexp matchers for body, query, URI and path.  
- [x] Form Data body matcher (partial & exact)
- [x] Multipart form body matcher (just like callback matcher but parses the body as a multipart form data)  
- [ ] `symfony/http-client` support.  
- [ ] Real network response for mocked & unmatched requests.  
- [ ] Document everything (with examples if it’s feasible).
