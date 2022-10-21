<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Tools\Collections;

abstract class	Set 
implements		\Iterator, \ArrayAccess, \Countable, \JsonSerializable {
	
	/*********************************************************************
	 *                                                                   *
	 *                          TEMPLATE METHODS:                        *
	 *                                                                   *
	 *********************************************************************/

	//	// replace all mixed types with desired collection type:
	//	public function current (): mixed {
	//		$offsetInt = $this->keys[$this->position];
	//		return $this->array[$offsetInt];
	//	}
	//	/**
	//	 * @param  int   $offset 
	//	 * @param  mixed $value 
	//	 */
	//	public function offsetSet ($offset, $value): void {
	//		if ($offset === NULL) {
	//			$this->array[] = $value;
	//		} else {
	//			$offsetInt = intval($offset);
	//			$this->array[$offsetInt] = $value;
	//		}
	//		$this->keys = array_keys($this->array);
	//		$this->count = count($this->array);
	//	}
	//	/** @param int $offset */
	//	public function offsetGet ($offset): mixed {
	//		$offsetInt = intval($offset);
	//		return array_key_exists($offsetInt, $this->array)
	//			? $this->array[$offsetInt] 
	//			: null;
	//	}
	//	public function shift (): mixed {
	//		$offsetInt = array_shift($this->keys);
	//		$value = $this->array[$offsetInt];
	//		unset($this->array[$offsetInt]);
	//		$this->count -= 1;
	//		if ($this->position === $this->count)
	//			$this->position--;
	//		return $value;
	//	}
	//	/**
	//	 * @param mixed $values,...
	//	 */
	//	public function unshift (): int {
	//		$args = func_get_args();
	//		$argsCnt = count($args);
	//		array_unshift($args, $this->array);
	//		$this->count = call_user_func_array('array_unshift', $args);
	//		$this->keys = array_keys($this->array);
	//		if ($this->position > 0)
	//			$this->position += $argsCnt;
	//		return $this->count;
	//	}

	/*********************************************************************/

	/**
	 * Current position.
	 * @var int
	 */
	protected $position = 0;
	
	/**
	 * Collection length.
	 * @var int
	 */
	protected $count = 0;
	
	/**
	 * Collection store.
	 * Keys are always integers, values are mixed types.
	 * @var array
	 */
	protected $array = [];
	
	/**
	 * Collection keys.
	 * @var \int[]
	 */
	protected $keys = [];
	
	/**
	 * Create collection from sequence array.
	 * @param array $array 
	 */
	public function __construct (array $array = []) {
		$this->position = 0;
		$this->count = count($array);
		$this->array = & $array;
		$this->keys = array_keys($array);
	}

	/**
	 * @return void
	 */
	public function __clone () {
		$clones = [];
		foreach ($this->array as $key => $value) {
			if (is_object($value)) {
				$clones[$key] = clone $value;
			} else {
				$clones[$key] = $value;
			}
		}
		$this->array = $clones;
	}
	
	/**
	 * Get collection length.
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function count () {
		return count($this->array);
	}
	
	/**
	 * Rewind current collection position to the beginning.
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function rewind () {
		$this->position = 0;
	}

	/**
	 * Move current collection position to next item.
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function next () {
		$this->position += 1;
	}
	
	/**
	 * Get current collection position key.
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function key () {
		return $this->keys[$this->position];
	}
	
	/**
	 * Get if current collection position is valid for next loop step.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function valid () {
		return $this->position < $this->count;
	}
	
	/**
	 * Check if collection contains given key and the value is not `NULL`.
	 * @param  int $key 
	 * @return bool
	 */
	public function __isset ($key) {
		$keyInt = intval($key);
		return isset($this->array[$keyInt]);
	}

	/**
	 * Check if collection contains given key (value could be `NULL`).
	 * @param  int $offset 
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists ($offset) {
		$offsetInt = intval($offset);
		return array_key_exists($offsetInt, $this->array);
	}

	/**
	 * Unset given offset if exists.
	 * @param  int $offset 
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset ($offset) {
		$offsetInt = intval($offset);
		if (isset($this->array[$offsetInt])) {
			unset($this->array[$offsetInt]);
			$this->keys = array_keys($this->array);
			$this->count = count($this->array);
		}
	}

	/**
	 * Get collection array store.
	 * @return array
	 */
	public function getArray () {
		return array_values($this->array);
	}
	
	/**
	 * Get collection keys as string array.
	 * @return \int[]
	 */
	public function getKeys () {
		return $this->keys;
	}

	/**
	 * Implementation to encode collection by `json_encode()`.
	 * @return array
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize () {
		return array_values($this->array);
	}

	/**
	 * Return current position value.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function current () {
		$offsetInt = $this->keys[$this->position];
		return $this->array[$offsetInt];
	}

	/**
	 * Set value under given index.
	 * @param  int   $offset 
	 * @param  mixed $value 
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet ($offset, $value) {
		if ($offset === NULL) {
			$this->array[] = $value;
		} else {
			$offsetInt = intval($offset);
			$this->array[$offsetInt] = $value;
		}
		$this->keys = array_keys($this->array);
		$this->count = count($this->array);
	}
	
	/**
	 * Return value for given index.
	 * @param  int $offset 
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet ($offset) {
		$offsetInt = intval($offset);
		return array_key_exists($offsetInt, $this->array)
			? $this->array[$offsetInt] 
			: null;
	}

	/**
	 * Removes first collection item, keeps indexes 
	 * as it is (doesn't move collection down) 
	 * and returns the first item.
	 * @return mixed
	 */
	public function shift () {
		$offsetInt = array_shift($this->keys);
		$value = $this->array[$offsetInt];
		unset($this->array[$offsetInt]);
		$this->count -= 1;
		if ($this->position === $this->count)
			$this->position--;
		return $value;
	}

	/**
	 * Prepends one or more item(s) into the beginning 
	 * of the collection and returns new collection length.
	 * @param  mixed $values,...
	 * @return int
	 */
	public function unshift () {
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