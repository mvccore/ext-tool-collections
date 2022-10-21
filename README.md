# MvcCore - Extension - Tool - Collections

[![Latest Stable Version](https://img.shields.io/badge/Stable-v5.0.2-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-tool-collections/releases)
[![License](https://img.shields.io/badge/License-BSD%203-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.4-brightgreen.svg?style=plastic)

Collection classes for typed arrays in PHP.  

Be careful, in places, where is necessary to process large arrays very fast,  
use arrays instead. This extensions are only for comfortable coding where  
you have enough execution time or where time is not so critical.

## Installation
```shell
composer require mvccore/ext-tool-collections
```

## Usage

First, le'ts say we have defined collection item class like this:
```php
class MyItem {
	public function __construct (protected string $name) {}
	public function GetName (): string {
		return $this->name;
	}
}
```

To have automatically typed collection items  
in for or foreach loops like this, without PHPDocs code:

```php
$myItemsSet = new MyItemsSet([
	new MyItem('John'),
	new MyItem('Joe'),
]);
$myItemsMap = new MyItemsMap([
	'john'	=> new MyItem('John'),
	'joe'	=> new MyItem('Joe'),
]);
for ($i = 0, $l = count($myItemsSet); $i < $l; $i++) {
	$myItem = $myItemsSet[$i];
	// now `$myItem` local variable is automatically 
	// typed by your IDE as `MyItem`, the method 
	// `GetName()` is always autocompleted by your IDE:
	echo $myItem->GetName();
}
foreach ($myItemsMap as $myItem) {
	// now `$myItem` local variable is automatically 
	// typed by your IDE as `MyItem`, the method 
	// `GetName()` is always autocompleted by your IDE:
	echo $myItem->GetName();
}
```

For sequence collection, you need to create:
- empty class extended from `\MvcCore\Ext\Tools\Collections\Set`
- go to extended class and copy paste commented template methods into empty class
- uncomment all template methods and replace all mixed types with final type (`MyItem`)
```php
class MyItemsSet extends \MvcCore\Ext\Tools\Collections\Set {
	// all methods are copy pasted from extended class:
	public function current (): MyItem {
		$offsetInt = $this->keys[$this->position];
		return $this->array[$offsetInt];
	}
	/**
	 * @param  int    $offset 
	 * @param  MyItem $value 
	 */
	public function offsetSet ($offset, $value): void {
		if ($offset === NULL) {
			$this->array[] = $value;
		} else {
			$offsetInt = intval($offset);
			$this->array[$offsetInt] = $value;
		}
		$this->keys = array_keys($this->array);
		$this->count = count($this->array);
	}
	/** @param int $offset */
	public function offsetGet ($offset): MyItem {
		$offsetInt = intval($offset);
		return array_key_exists($offsetInt, $this->array)
			? $this->array[$offsetInt] 
			: null;
	}
	public function shift (): MyItem {
		$offsetInt = array_shift($this->keys);
		$value = $this->array[$offsetInt];
		unset($this->array[$offsetInt]);
		$this->count -= 1;
		if ($this->position === $this->count)
			$this->position--;
		return $value;
	}
	/**
	 * @param MyItem $values,...
	 */
	public function unshift (): int {
		$args = func_get_args();
		$argsCnt = count($args);
		array_unshift($args, $this->array);
		$this->count = call_user_func_array('array_unshift', $args);
		$this->keys = array_keys($this->array);
		if ($this->position > 0)
			$this->position += $argsCnt;
		return $this->count;
	}
}
```


For associative collection, you need to create:
- empty class extended from `\MvcCore\Ext\Tools\Collections\Map`
- go to extended class and copy paste commented template methods into empty class
- uncomment all template methods and replace all mixed types with final type (`MyItem`)
```php
class MyItemsMap extends \MvcCore\Ext\Tools\Collections\Map {
	// all methods are copy pasted from extended class:
		public function current (): MyItem {
		$key = $this->keys[$this->position];
		return $this->array[$key];
	}
	/**
	 * @param string $offset 
	 * @param MyItem  $value 
	 */
	public function offsetSet ($offset, $value): void {
		if ($offset === NULL) {
			$this->array[] = $value;
		} else {
			$offsetStr = (string) $offset;
			$this->array[$offsetStr] = $value;
		}
		$this->keys = array_keys($this->array);
		$this->count = count($this->array);
	}
	/** @param string $offset */
	public function offsetGet ($offset): MyItem {
		$offsetStr = (string) $offset;
		return array_key_exists($offsetStr, $this->array)
			? $this->array[$offsetStr] 
			: null;
	}
	public function shift (): MyItem {
		$offsetStr = array_shift($this->keys);
		$value = $this->array[$offsetStr];
		unset($this->array[$offsetStr]);
		$this->count -= 1;
		if ($this->position === $this->count)
			$this->position--;
		return $value;
	}
}
```