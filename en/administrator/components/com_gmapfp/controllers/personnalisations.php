<?php
	/*
	* GMapFP Component Google Map for Joomla! 3.0.x
	* Version J3.13F
	* Creation date: Mars 2013
	* Author: Fabrice4821 - www.gmapfp.org
	* Author email: webmaster@gmapfp.org
	* License GNU/GPL
	*/

defined('_JEXEC') or die;

class GMapFPsControllerPersonnalisations extends GMapFPsController
{
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function view()
	{
		JRequest::setVar( 'view', 'personnalisations' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	function edit()
	{
		JRequest::setVar( 'view', 'personnalisations' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$post	= JRequest::get('post');
		$model = $this->getModel('personnalisations');
		$returnid=$model->store($post);
		if ($returnid>0) {
			$msg = JText::_( 'GMAPFP_SAVED' );
		} else {
			$msg = JText::_( 'GMAPFP_SAVED_ERROR' );
		}
		$link = 'index.php?option=com_gmapfp&controller=personnalisations&task=view';
		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect($link, $msg);
	}

	function apply()
	{
		$post	= JRequest::get('post');
		$model = $this->getModel('personnalisations');
		$returnid=$model->store($post);
		if ($returnid>0) {
			$msg = JText::_( 'GMAPFP_SAVED' );
		} else {
			$msg = JText::_( 'GMAPFP_SAVED_ERROR' );
		}

		$link = 'index.php?option=com_gmapfp&controller=personnalisations&task=edit&cid[]='.(int)$returnid;
		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('personnalisations');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or more GMapFPs could not be Deleted' );
		} else {
			$msg = JText::_( 'GMapFP(s) Deleted' );
		}
		$this->setRedirect( 'index.php?option=com_gmapfp&controller=personnalisations&task=view', $msg );
	}

	function publish()
	{
		$this->setRedirect( 'index.php?option=com_gmapfp&controller=personnalisations&task=view' );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );
		$query = $db->getQuery(true);
			$fields = $db->quoteName('published').' = '.(int) $publish;
			$conditions = $db->quoteName('id').' IN ( '.$cids.' )';
			$query->update($db->quoteName('#__gmapfp_personnalisation'))->set($fields)->where($conditions);
		$db->setQuery($query);
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
	}
	
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_gmapfp&controller=personnalisations&task=view', $msg );
	}
}
?>
