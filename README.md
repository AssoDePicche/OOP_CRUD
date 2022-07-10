# OOP CRUD
CRUD operations performed with the object-oriented programming paradigm in order to challenge my knowledge

## Technologies
- PHP
- MySQL

## How to use
Instantiate the class entity and pass the name of the table you want to handle in its constructor. The database connection settings (such as username, database, host, etc.) can be modified in a file called .env in the root directory


### Index.php
```php
<?php

declare(strict_types=1);

require_once __DIR__ .  "/vendor/autoload.php";

use App\Common\Enviroment;
use App\Database\Entity;

Enviroment::load(__DIR__);

```

### Enviroment File
```php
```

### Entity Class
```php
```
