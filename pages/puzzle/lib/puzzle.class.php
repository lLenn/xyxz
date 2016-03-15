<?php

class Puzzle
{
	private $id;
	private $fen;
	private $first_move;
	private $moves;

	function Puzzle($data=null)
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
		$this->id = $data['id'];
		$this->fen = $data['fen'];
		$this->first_move = $data['first_move'];
		$this->moves = $data['moves'];
	}

	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->fen = $data->fen;
		$this->first_move = $data->first_move;
		$this->moves = $data->moves;
	}

	public function get_properties()
	{
		return array('id' => $this->id,
					 'fen' => $this->fen,
					 'first_move' => $this->first_move,
					 'moves' => $this->moves);
	}

	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_fen() { return $this->fen; }
	public function set_fen($fen) { $this->fen = $fen; }
	public function get_first_move() { return $this->first_move; }
	public function set_first_move($first_move) { $this->first_move = $first_move; }
	public function get_moves() { return $this->moves; }
	public function set_moves($moves) { $this->moves = $moves; }

}

?>