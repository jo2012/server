<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kResponseProfileMapping
{
	/**
	 * @var string
	 */
	private $parentProperty;
	
	/**
	 * @var string
	 */
	private $filterProperty;
	
	/**
	 * @var bool
	 */
	private $allowNull;
	
	/**
	 * @return the $allowNull
	 */
	public function getAllowNull()
	{
		return $this->allowNull;
	}

	/**
	 * @param bool $allowNull
	 */
	public function setAllowNull($allowNull)
	{
		$this->allowNull = $allowNull;
	}

	/**
	 * @return the $parentProperty
	 */
	public function getParentProperty()
	{
		return $this->parentProperty;
	}

	/**
	 * @return the $filterProperty
	 */
	public function getFilterProperty()
	{
		return $this->filterProperty;
	}

	/**
	 * @param string $parentProperty
	 */
	public function setParentProperty($parentProperty)
	{
		$this->parentProperty = $parentProperty;
	}

	/**
	 * @param string $filterProperty
	 */
	public function setFilterProperty($filterProperty)
	{
		$this->filterProperty = $filterProperty;
	}
}