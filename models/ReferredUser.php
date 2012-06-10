<?PHP

class ReferredUser extends Module {
	
	protected static
		$module_name = 'referreduser',
		$table_name = 'referredusers',
		$allFields =
			array(
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'user_id',
					'type' => 'int',
					'max' => -1,
					'min' => 1,
				),
				array(
					'name' => 'referral_id',
					'type' => 'int',
					'max' => -1,
					'min' => 1,
				),
			);
		
	public
		$created, $user_id, $referral_id;
	
	
	
}

?>