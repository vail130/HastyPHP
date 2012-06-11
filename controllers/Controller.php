<?PHP

class Controller {
	
	public function setParams($params) {
		if(empty($params)) {
			return;
		}
		
		foreach($params as $key => $value) {
			if(isset($this->$key)) {
				$this->$key = $value;
			}
		}
	}
	
}

?>