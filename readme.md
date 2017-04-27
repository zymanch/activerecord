Configuration
=

Create db connection:
```php
$db = new \ActiveRecord\db\Connection();
$db->dsn = 'mysql:host=sanddb.gtflixtv.com;dbname=shared';
$db->username = 'develop';
$db->password = 'develop@box';
$db->charset = 'utf8';
```

provide it to ActiveRecord classes:
```php
\ActiveRecord\db\ActiveRecord::setDb($db);
\ActiveRecord\db\Query::setDb($db);
```

Usage
=

http://www.yiiframework.com/doc-2.0/guide-db-active-record.html

Generator
=

Use generator to auto create active record models related with db tables: 
```php
$generator = new \ActiveRecord\Generator('<database name>',$db);
$generator->generate('<namespace>','<path>');
```
for example:

```php
$generator = new \ActiveRecord\Generator('shared',$db);
$generator->generate('model',__DIR__.'/model');
```