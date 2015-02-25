<?php

/*
 * Strategy to be used for getting default where conditions for EEM_Base children.
 * Should be initialized and set on construction of model
 */
class EE_Default_Where_Conditions{
	/**
	 * Model fo rwhich this strategy find default where conditions
	 * @var EEM_Base
	 */
	protected $_model;
	/**
	 * finalizes construction of the strategy for use in getting default where conditions
	 * for querying of the model.
	 * @param EEM_Base $model
	 */
	function _finalize_construct(EEM_Base $model){
		$this->_model = $model;
	}
	function get_default_where_conditions(  $model_relation_chain = null  ){
		return array();
	}
	/**
	 * Gets the bare minimum where conditions. Usually this is NOTHING,
	 * but if the data for this model shares a table with another table,
	 * this is the bare minimum to differentiate between this model's data
	 * and the other model's data
	 * @param string $model_relation_chain
	 */
	function get_minimum_where_conditions( $model_relation_chain = null ){
		return array();
	}

	/**
	 *
	 * @param string $model_relation_chain
	 * @return string
	 */
	protected function _ensure_model_relation_chain_ends_in_period( $model_relation_chain ){
		if($model_relation_chain != '' && $model_relation_chain[strlen($model_relation_chain)-1] !='.'){
			$model_relation_chain=$model_relation_chain.".";
		}
		return $model_relation_chain;
	}
}
