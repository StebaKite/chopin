<?php

interface DatabaseAccessInterface {
	
	public static function getInstance();

	public function createDatabaseConnection();
	public function closeDBConnection();
	
	public function beginTransaction();
	public function rollbackTransaction();	
	public function commitTransaction();
	
	public function execSql($sql);
	public function getData($sql);
	
	public function getDBConnection();
	public function setDBConnection($dbconnection);
	
	public function getLastIdUsed();
	public function setLastIdUsed($lastIdUsed);
	
	public function getNumrows();
	public function setNumrows($numrows);
	
}

?>