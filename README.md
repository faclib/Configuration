# Configuration

Конфигурация в виде массива.

## Installation / Usage

    {
        "require": {
            "fobia/php-configuration": ">=1.0"
        }
    }


```php
<?php
use Fobia\Configuration\Configuration;

$config = new Configuration();
$config->setArray(array(
    'path1.to1' => 'value11',
    'path1.to2' => 'value12',
    'path2' => array(
        'to1' => 'value21'
    )
));


// Array (
//     [to1] => value11 
//     [to2] => value12
// )
print_r($config['path1']);  

echo $config['path1.to1'];    // 'value11'
echo $config['path1']['to2']; // 'value12'
echo $config['path2']['to1']; // 'value21'
?>
```