<?php
use Hazzard\Database\Model;

class Option extends Model {
	
	protected $table = 'options';

	protected $guarded = array('id');
}