<?php 
namespace Plugins\AutoComment;

// Disable direct access
if (!defined('APP_VERSION')) 
    die("Yo, what's up?");

/**
 * Schedules model
 *
 * @version 1.0
 * @author Onelab <hello@onelab.co> 
 * 
 */
class DuplicateModel extends \DataList
{	
	/**
	 * Initialize
	 */
	public function __construct()
	{
		$this->setQuery(\DB::table(TABLE_PREFIX."auto_comment_schedule"));
	}
}
