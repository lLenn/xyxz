<?php

class LocationObjectReview extends LocationUser
{
	private $location_id;
	private $user_id;
	private $object_id;
	private $rating;
	private $review;
	private $anonymous;
	private $added;
	private $last_edited;
	
	function LocationObjectReview($data=null)
	{
		if(!is_null($data))
		{
			if(is_array($data))
				$this->fill_from_array($data);
			else
				$this->fill_from_database($data);
		}
	}

	public function fill_from_array($data)
	{
		$this->location_id = $data['location_id'];
		$this->user_id = $data['user_id'];
		$this->object_id = $data['object_id'];
		$this->rating = $data['rating'];
		$this->review = $data['review'];
		$this->anonymous = $data['anonymous'];
	}
	
	public function fill_from_database($data)
	{
		$this->location_id = $data->location_id;
		$this->user_id = $data->user_id;
		$this->object_id = $data->object_id;
		$this->rating = $data->rating;
		$this->review = $data->review;
		$this->anonymous = $data->anonymous;
		$this->added = $data->added;
		$this->last_edited = $data->last_edited;
	}
	
	public function get_properties()
	{
		return array('location_id' => $this->location_id,
					 'user_id' => $this->user_id,
					 'object_id' => $this->object_id,
					 'rating' => $this->rating,
					 'review' => $this->review,
					 'anonymous' => $this->anonymous,
					 'added' => $this->added,
					 'last_edited' => $this->last_edited);
	}

	public function get_location_id() { return $this->location_id; }
	public function set_location_id($location_id) { $this->location_id = $location_id; }
	public function get_user_id() { return $this->user_id; }
	public function set_user_id($user_id) { $this->user_id = $user_id; }
	public function get_object_id() { return $this->object_id; }
	public function set_object_id($object_id) { $this->object_id = $object_id; }
	public function get_rating() { return $this->rating; }
	public function set_rating($rating) { $this->rating = $rating; }
	public function get_review() { return $this->review; }
	public function set_review($review) { $this->review = $review; }
	public function get_anonymous() { return $this->anonymous; }
	public function set_anonymous($anonymous) { $this->anonymous = $anonymous; }
	public function get_added() { return $this->added; }
	public function set_added($added) { $this->added = $added; }
	public function get_last_edited() { return $this->last_edited; }
	public function set_last_edited($last_edited) { $this->last_edited = $last_edited; }
	
}

?>