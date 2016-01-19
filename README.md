# Pragmatic ORM

## Basic usage

```php
use Colorium\Orm\MySQL;

$mysql = new MySQL('dbname');
```

You can also map some model classes that will be use intead of `\stdClass` :
```php
use Colorium\Orm\MySQL;

$mysql = new MySQL('dbname', [
    'user' => My\App\User::class
]);
```


### Read

Read may, return a set of `\stdClass` (or specified class if mapped)
```php
$users = $mysql->user->fetch();
$users = $mysql->user->where(...)->sort(...)->limit(...)->fetch();
```

Where clause (working operators: `=`, `>`, `>=`, `<`, `<=`, `like`, `in` with array as parameter)
```php
$users = $mysql->user->where('age', 27)->fetch();
$users = $mysql->user->where('age >', 27)->fetch();
$users = $mysql->user->where('age', [25, 26, 27]])->fetch();
// or multiple where
$users = $mysql->user->where(['age' => 27, 'eyes' => 'green'])->fetch();
```

Sort clause
```php
$users = $mysql->user->sort('city')->fetch(); // ASC
$users = $mysql->user->sort('city', SORT_DESC)->fetch();
```

Limit clause
```php
$users = $mysql->user->limit(5)->fetch(); // 5 records from start
$users = $mysql->user->limit(5, 10)->fetch(); // 10 records from the 5th
```

Read one, return a `\stdClass` (or specified class if mapped)
```php
$user = $mysql->user->one(); // first record
$user = $mysql->user->where(...)->one();
```

### Write

Add record (you can pass an array or an object)
```php
$id = $mysql->user->add([
    'username' => 'New player'
]);
```

Edit record
```php
$user = $mysql->user->where('id' => 15)->one();

$user->username = 'Old player';
$mysql->user->where('id', 15)->edit($user);
```

Drop record
```php
$mysql->user->where('id' => 15)->drop();
```


### Custom SQL query

```php
$users = $mysql->raw('select * from `user`');
```


## Hub

Hub is a global container for orm sources, used by model helper :

```php
Use Colorium\Orm\Hub;

Hub::source($mysql);

$users = Hub::user()->fetch();
```

## Model

Model is more convenient way of using Colorium Orm (do not forget to setup `Hub` before) :
```php
class User
{
    use Colorium\Orm\Model;
    
    /** @var int */
    public $id;
    
    /** @var string */
    public $username;
    
    /** @var string */
    public $password;
}
```

Then :
```php
$user = User::one(['id' => 15]);

$user->username = 'Winner';
$user->save();
```

## Install

`composer require colorium/orm`