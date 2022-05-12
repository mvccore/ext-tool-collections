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

abstract class	Map 
implements		\Iterator, \ArrayAccess, \Countable, \JsonSerializable {

	/*********************************************************************
	 *                                                                   *
	 *                          TEMPLATE METHODS:                        *
	 *                                                                   *
	 *********************************************************************/

	//	// replace all mixed types with desired collection type:
	//	public function current (): mixed {
	//		$key = $this->keys[$this->position];
	//		return $this->array[$key];
	//	}
	//	/**
	//	 * @param string $offset 
	//	 * @param mixed  $value 
	//	 */
	//	public function offsetSet ($offset, $value): void {
	//		if ($offset === NULL) {
	//			$this->array[] = $value;
	//		} else {
	//			$offsetStr = (string) $offset;
	//			$this->array[$offsetStr] = $value;
	//		}
	//		$this->keys = array_keys($this->array);
	//		$this->count = count($this->array);
	//	}
	//	/** @param string $offset */
	//	public function offsetGet ($offset): mixed {
	//		$offsetStr = (string) $offset;
	//		return array_key_exists($offsetStr, $this->array)
	//			? $this->array[$offsetStr] 
	//			: null;
	//	}
	//	public function shift (): mixed {
	//		$offsetStr = array_shift($this->keys);
	//		$value = $this->array[$offsetStr];
	//		unset($this->array[$offsetStr]);
	//		$this->count -= 1;
	//		if ($this->position === $this->count)
	//			$this->position--;
	//		return $value;
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
	 * Keys are always string, values are mixed types.
	 * @var array
	 */
	protected $array = [];

	/**
	 * Collection keys.
	 * @var \string[]
	 */
	protected $keys = [];

	/**
	 * Create collection from associative array.
	 * @param array $array 
	 */
	public function __construct (array $array = []) {
		$this->position = 0;
		$this->count = count($array);
		$this->array = & $array;
		$this->keys = array_keys($array);
	}

	/**
	 * Get collection length.
	 * @return int
	 */
	public function count() {
		return $this->count;
	}

	/**
	 * Rewind current collection position to the beginning.
	 * @return void
	 */
	public function rewind () {
		$this->position = 0;
	}

	/**
	 * Move current collection position to next item.
	 * @return void
	 */
	public function next() {
		$this->position += 1;
	}

	/**
	 * Get current collection position key.
	 * @return string
	 */
	public function key () {
		return $this->keys[$this->position];
	}

	/**
	 * Get if current collection position is valid for next loop step.
	 * @return bool
	 */
	public function valid () {
		return $this->position < $this->count;
	}

	/**
	 * Check if collection contains given key and the value is not `NULL`.
	 * @param  string $key 
	 * @return bool
	 */
	public function __isset ($key) {
		$keyStr = (string) $key;
		return isset($this->array[$keyStr]);
	}

	/**
	 * Check if collection contains given key (value could be `NULL`).
	 * @param  string $offset 
	 * @return bool
	 */
	public function offsetExists ($offset) {
		$offsetStr = (string) $offset;
		return array_key_exists($offsetStr, $this->array);
	}

	/**
	 * Unset given offset if exists.
	 * @param  string $offset 
	 * @return void
	 */
	public function offsetUnset ($offset) {
		$offsetStr = (string) $offset;
		if (isset($this->array[$offsetStr])) {
			unset($this->array[$offsetStr]);
			$this->keys = array_keys($this->array);
			$this->count = count($this->array);
		}
	}

	/**
	 * Get collection array store.
	 * @return array
	 */
	public function getArray () {
		return $this->array;
	}

	/**
	 * Get collection keys as string array.
	 * @return \string[]
	 */
	public function getKeys () {
		return $this->keys;
	}

	/**
	 * Implementation to encode collection by `json_encode()`.
	 * @return array
	 */
	public function jsonSerialize () {
		return $this->array;
	}
	
	/**
	 * Return current position value.
	 * @return mixed
	 */
	public function current () {
		$offsetStr = $this->keys[$this->position];
		return $this->array[$offsetStr];
	}

	/**
	 * Set value under given index.
	 * @param  string $offset 
	 * @param  mixed  $value 
	 * @return void
	 */
	public function offsetSet ($offset, $value) {
		if ($offset === NULL) {
			$this->array[] = $value;
		} else {
			$offsetStr = (string) $offset;
			$this->array[$offsetStr] = $value;
		}
		$this->keys = array_keys($this->array);
		$this->count = count($this->array);
	}

	/**
	 * Return value for given index.
	 * @param  string $offset 
	 * @return mixed
	 */
	public function offsetGet ($offset) {
		$offsetStr = (string) $offset;
		return array_key_exists($offsetStr, $this->array)
			? $this->array[$offsetStr] 
			: NULL;
	}

	/**
	 * Removes first collection item, keeps indexes 
	 * as it is (doesn't move collection down) 
	 * and returns the first item.
	 * @return mixed
	 */
	public function shift () {
		$offsetStr = array_shift($this->keys);
		$value = $this->array[$offsetStr];
		unset($this->array[$offsetStr]);
		$this->count -= 1;
		if ($this->position === $this->count)
			$this->position--;
		return $value;
	}
}