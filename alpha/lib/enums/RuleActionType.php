<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface RuleActionType extends BaseEnum
{
	const BLOCK = 1;
	const PREVIEW = 2;
	const LIMIT_FLAVORS = 3;
	const ADD_TO_STORAGE = 4;
	const LIMIT_DELIVERY_PROFILES = 5;
}