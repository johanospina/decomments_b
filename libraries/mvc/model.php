<?php

class DECOM_Model {
	protected $wpdb = null;
	protected $prefix = '';

	/**
	 * This field contains results of the queries execution
	 * Key of the each item is represented by query md5 hash
	 *
	 * @var array
	 */
	protected $query_results = array();

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;

		$this->setTablePrefix( $this->wpdb->prefix );
	}

	public function __destruct() {
		unset( $this->query_results );
	}

	protected function cacheQuery( $query_hash, $query_results ) {
		$this->query_results[$query_hash] = $query_results;
	}

	protected function getCachedQueryResults( $query_hash ) {
		if ( !array_key_exists( $query_hash, $this->query_results ) ) {
			return false;
		}

		return $this->query_results[$query_hash];
	}

	/**
	 * Set table prefix
	 *
	 * @param string table prefix
	 */
	public function setTablePrefix( $prefix ) {
		$this->prefix = $prefix;
	}

	/**
	 *
	 *
	 */
	public function query( $sql ) {
		return $this->wpdb->query( $sql );
	}

	/**
	 * Create and alter tables
	 */
	public function executeTableQuery( $sql ) {
		DECOM_Loader::includeWPUpgrade();
		dbDelta( $sql );
	}

	public function getPluginDBVersion() {
		return get_option( DECOM_DB_VERSION_OPTION_NAME );
	}

	public function setPluginDBVersion() {
		return update_option( DECOM_DB_VERSION_OPTION_NAME );
	}

	public function createTable( $sql ) {
		//$upgraded_version = $this->getPluginDBVersion();

		//if( DECOM_DB_VERSION != $upgraded_version )
		//{
		$this->executeTableQuery( $sql );
		//    $this->setPluginDBVersion($upgraded_version);
		//}
	}

	/**
	 * @param $sql SELECT OR UPDATE AND JOIN (UPDATE `table1` SET `row1` = ":value1", `row2` => "value2")
	 *
	 */
	public function preparedQuery( $sql, $param ) {
		return $this->wpdb->query( $this->wpdb->prepare( $sql, $param ) );
	}


	/**
	 * Insert data into the table
	 *
	 * @param array $data =array('badge_name' => eeeee', 'badge_like_number' => 4);
	 * @param array $type = array('%s', '%d');
	 *
	 * @return int insert id or bool false
	 */
	public function insert( $table_name, array $data, array $type = array() ) {
		$keys = array_keys( $data );

		if ( ( $table_name == '' ) || ( count( $keys ) == 0 ) ) {
			return false;
		}

		if ( $this->wpdb->insert( $table_name, $data, $type ) ) {
			return $this->wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Update data in the table.
	 *
	 * Update query will be prepared with WPDB method.
	 * Use other method if you need to return number of affected rows.
	 *
	 * @param       string table name
	 * @param array $data  = array('eeeee', 4);
	 * @param array $type  =array('badge_name' => '%s', 'badge_like_number' => '%d');
	 * @param       array  associative array to form the 'where' sql condition. All array items will be concatinated by 'AND'.
	 *
	 * @return int count update rows result of operation
	 */
	public function update( $table_name, array $data, array $type, array $where = array() ) {
		if ( ( $table_name == '' ) || ( count( $data ) == 0 ) ) {
			return false;
		}
		if ( count( $where ) > 0 ) {
			foreach ( $where AS $key => $value ) {
				$where_columns_arr[] = $key . " = " . $value;
			}
			$where_condition = " WHERE " . implode( ' AND ', $where_columns_arr );
		} else {
			return false;
		}

		if ( count( $type ) > 0 ) {
			foreach ( $type AS $key => $value ) {
				$data_columns_arr[] = $key . " = '" . $value . "'";
			}
			$data_update = " SET " . implode( ', ', $data_columns_arr );
		} else {
			return false;
		}

		return $this->wpdb->query(
			$this->wpdb->prepare( "UPDATE " . $table_name . $data_update . $where_condition, $data )
		);
	}

	/**
	 * Select rows
	 *
	 * @param string sql statement
	 * @param array  array of query parameters
	 * @param const  constant responsible for fetch type (PDO::FETCH_OBJ by default)
	 *
	 * @return mixed return array if query have several results and return a single item if query has single result
	 */
	public function selectRows( $sql, array $params = array(), $fetch_type = 'OBJECT' ) {
		$sql = $this->wpdb->prepare( $sql, $params );

		return $this->wpdb->get_results( $sql, $fetch_type );
	}

	/**
	 *
	 * select rows where *
	 * @params (string) $table_name
	 * @params (array) $set_columns_arr :: exemple (array(id, name))
	 * @params (array) $where :: exemple (array('id' => '2', 'post' => '3'))
	 * @params (array) $order ::
	 * @params (constant) $fetch_type ASSOC for array or OBJ for object
	 */
	public function selectRowsWhere( $table_name, array $set_columns_arr = array(), array $where = array(), array $order = array(), $fetch_type = 'OBJECT' ) {
		if ( count( $set_columns_arr ) > 0 ) {
			$set_columns = implode( ', ', $set_columns_arr );
		} else {
			$set_columns = '*';
		}

		$where_condition = '';
		$pdo_params      = array();
		if ( count( $where ) > 0 ) {
			foreach ( $where AS $key => $value ) {
				$pdo_key             = 'where_' . $key;
				$where_columns_arr[] = $key . " =%s";
				$pdo_params[]        = $value;
			}

			$where_condition = " WHERE " . implode( ' AND ', $where_columns_arr );
		}

		$orderBy = '';
		if ( count( $order ) > 0 ) {
			/*реализовать order*/
		}

		$query  = 'SELECT ' . $set_columns . ' FROM ' . $table_name . $where_condition;
		$result = $this->selectRows( $query, $pdo_params, $fetch_type );

		return $result;
	}

	public function selectIndexedRows( $sql, array $pdo_params = array(), $fetch_type = OBJECT, array $index_columns_array = array(), $multiple = false ) {
		$index_columns_str = '';
		if ( count( $index_columns_array ) > 0 ) {
			$index_columns_str = implode( ',', $index_columns_array );
		}

		$pdo_params_str = '';
		if ( count( $pdo_params ) > 0 ) {
			foreach ( $pdo_params AS $key => $value ) {
				$pdo_params_str .= $key . '=' . $value . ';';
			}
		}

		// get hash of the query
		$query_hash = md5( $fetch_type . '/' . $pdo_params_str . '/' . $index_columns_str . '/' . $sql );

		$cached_query_results = $this->getCachedQueryResults( $query_hash );

		if ( $cached_query_results ) {
			return $cached_query_results;
		}

		$rows = $this->selectRows( $sql, $pdo_params, $fetch_type );

		$results = array();
		if ( count( $index_columns_array ) > 0 ) {
			if ( count( $rows ) > 0 ) {
				if ( is_object( $rows[0] ) ) {
					$items_type = 'object';
				}

				if ( is_array( $rows[0] ) ) {
					$items_type = 'array';
				}
			}

			for ( $i = 0; $i < count( $rows ); $i ++ ) {
				$columns = array();
				for ( $j = 0; $j < count( $index_columns_array ); $j ++ ) {
					switch ( $items_type ) {
						case 'array':
							if ( !array_key_exists( $index_columns_array[$j], $rows[$i] ) ) {
								continue;
							}
							$columns[] = $rows[$i][$index_columns_array[$j]];
							break;

						case 'object':
							if ( !isset( $rows[$i]->$index_columns_array[$j] ) ) {
								continue;
							}
							$columns[] = $rows[$i]->$index_columns_array[$j];
							break;
					}
				}

				$key = $this->formResultsKey( $columns );

				if ( $multiple ) {
					$results[$key][] = $rows[$i];
				} else {
					$results[$key] = $rows[$i];
				}

			}
		} else {
			$results = $rows;
		}

		$this->cacheQuery( $query_hash, $results );

		return $results;
	}

	public function isRowExists( $sql, array $pdo_params = array(), $fetch_type = OBJECT, array $search_array = array() ) {
		if ( count( $search_array ) == 0 ) {
			return false;
		}

		$search_array_keys   = array_keys( $search_array );
		$search_array_values = array_values( $search_array );

		$rows = $this->selectIndexedRows( $sql, $pdo_params, $fetch_type, $search_array_keys );

		$key = $this->formResultsKey( $search_array_values );

		if ( array_key_exists( $key, $rows ) ) {
			return $rows[$key];
		}

		return false;
	}


	public function selectRow( $sql, $param = array(), $output_type = 'OBJECT', $row_offset = 0 ) {
		$sql             = $this->wpdb->prepare( $sql, $param );
		$fetched_results = $this->wpdb->get_row( $sql, $output_type, $row_offset );
		if ( !$fetched_results ) {
			return false;
		}

		return $fetched_results;
	}

	/**
	 * Form the table complex key
	 *
	 * @param array $columns array which represents column names for the key
	 *
	 * @return string
	 */
	public function formResultsKey( $columns ) {
		$glue = '-';

		if ( count( $columns ) > 0 ) {
			return implode( $glue, $columns );
		}

		return false;
	}
	// Using where formatting.
	//$wpdb->delete( 'table', array( 'ID' => 1 ), array( '%d' ) );
	public function deleteWhere( $table_name, array $where = array(), array $type = array() ) {

		$this->wpdb->delete( $table_name, $where, $type );
	}

	public function deleteRowById( $table_name, $id ) {
		$where = array( 'id' => $id );
		$type  = array( '%d' );
		// Using where formatting.
		$this->wpdb->delete( $table_name, $where, $type );
	}

}
