<?php

	class Product
	{
		private $id;
		private $name;
		private $description;
		private $image;
		private $video;
		private $order;

		public function Product($data=null)
		{
			if(!is_null($data))
			{
				$this->fillFromDatabase($data);
			}
		}
		
		public function fillFromDatabase($data)
		{
			$this->id=$data->id;
			$this->name=$data->name;
			$this->description=$data->description;
			$this->image=$data->image;
			$this->video=$data->video;
			$this->order=$data->order;
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'name' => $this->name,
						 'description' => $this->description,
						 'image' => $this->image,
						 'video' => $this->video,
						 'order' => $this->order);
		}
	
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function set_name($name){	$this->name=$name;	}
		public function get_name(){	return $this->name;}
		public function set_description($description){	$this->description=$description;	}
		public function get_description(){	return $this->description;}
		public function get_image(){ return $this->image; }
		public function set_image($image){ $this->image = $image; }
		public function get_video(){	return $this->video;	}
		public function set_video($video){ $this->video = $video; }
		public function get_order(){	return $this->order;	}
		public function set_order($order){ $this->order = $order; }
	}
	
?>