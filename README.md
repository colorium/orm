# Pragmatic ORM

## Basic usage

```php
use Colorium\Orm\MySQL;

$mysql = new MySQL('dbname);
```

### Read

Read all (return a set of `\stdClass`)
```php
$users = $mysql->user->fetch();
$users = $mysql->user->where(...)->sort(...)->limit(...)->fetch();
```

Where clause (working operators: `=`, `>`, `>=`, `<`, `<=`, `like`, `in` with array as parameter)
```php
$users = $mysql->user->where(['age' => 27])->fetch();
$users = $mysql->user->where(['age >' => 27])->fetch();
$users = $mysql->user->where(['age' => [25, 26, 27]])->fetch();
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

Read one (return a `\stdClass`)
```php
$user = $mysql->user->one(); // first record
$user = $mysql->user->where(...)->one();
```

## Mapper

```php
namespace My\App;

class User
{
  /**
   * @id
   * @var int
   */
  public $id;
  
  /** @var string */
  public $username;
  
  /** @var string */
  public $password;
}
```

```php
use Colorium\Orm\MySQL;
use Colorium\Orm\Mapper;

$mysql = new MySQL('dbname);
$mapper = new Mapper($mysql, [
  'user' => My\App\User::class
]);

$user = $mapper->query('user')->where(['id' => 15])->one(); // My\App\User instance
```

## Install

`composer require colorium/orm`
