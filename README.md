# SemVer Converter

Converts SemVer version (like Composer packages) into integer version with operators. Helps managing versions: store, compare, sort and retrive by conditions.

Converter accepts the same versions as Composer, so You can use it with any package manager that accepts [Semantic Versioning](http://semver.org/).

# How it works?

For each version it uses Composer SemVer parser to parse version and normalize it. And then, for each version, converter creates array exploded by dot values. Each value pads with zeros and create from it long string which is converted to integer. And for each version we have big integer.

### Sample:

1. Version **1.0.5**
2. Normalize with SemVer: **1.0.5**
3. Explode: **[ 1, 0, 5 ]**
4. Converts to strings and pad zeros: **[ '001', '000', '005' ]**
5. Concatenate all strings: **'001' + '000' + '005'**
6. Converts to integer: **(int) '1000005'**

Result: **'1.0.5' == 1000005**

# Examples

### Simple version

```php
$result = (new SemVerConverter)->convert('0.1.0');

// Result
array (size=1)
  0 => 
    array (size=2)
      'from' => 
        array (size=2)
          0 => int 1000000
          1 => string '==' (length=2)
      'to' => 
        array (size=2)
          0 => int 1000000
          1 => string '==' (length=2)
```

### Version between

```php
$result = (new SemVerConverter)->convert('>= 1.3.0 < 1.7.0');

// Result
array (size=1)
  0 => 
    array (size=2)
      'from' => 
        array (size=2)
          0 => int 1003000
          1 => string '>=' (length=2)
      'to' => 
        array (size=2)
          0 => int 1007000
          1 => string '<' (length=1)
```

### Tilde operator

```php
$result = (new SemVerConverter)->convert('~1.3');

// Result
array (size=1)
  0 => 
    array (size=2)
      'from' => 
        array (size=2)
          0 => int 1003000
          1 => string '>=' (length=2)
      'to' => 
        array (size=2)
          0 => int 2000000
          1 => string '<' (length=1)
```

### Version or

```php
$result = (new SemVerConverter)->convert('^1.9 || 3.0.*');

// Result
array (size=2)
  0 => 
    array (size=2)
      'from' => 
        array (size=2)
          0 => int 1009000
          1 => string '>=' (length=2)
      'to' => 
        array (size=2)
          0 => int 2000000
          1 => string '<' (length=1)
  1 => 
    array (size=2)
      'from' => 
        array (size=2)
          0 => int 3000000
          1 => string '>=' (length=2)
      'to' => 
        array (size=2)
          0 => int 3001000
          1 => string '<' (length=1)
```

# Settings

```php
new SemVerConverter($zeros, $sections);
```

### $zeros

Defines how meny zeros need to pad for each section of versions. It allows to define how long should be result.

### $sections

Defines how many sections need to be generated from input. Default is 3, Composer SemVer generates 4. This also have an impact for result.

# Licence

This code is licensed under MIT License.
