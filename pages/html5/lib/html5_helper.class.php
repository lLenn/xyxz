<?php
class HTML5Helper
{
	static function convertToJSON($data, $object_serial, $location)
	{
		$jsontext = '{"object_serial": "' . $object_serial[0] . '", "object_correct": "' . $object_serial[1] . '", "object_wrong": "' . $object_serial[2] . '", '; 
		$jsontext .= '"game": {"fen": "' . $data["object"]->fen . '"' . ($location!="end_game" && $location!="multipleAnswers"?', "moves":"' .  $data["object"]->moves . '"' . ($location=="puzzle"?', "firstMove": "' . $data["object"]->first_move . '"':'') . '}, ':'}, ');
			
		if($location == "puzzle" && $data["puzzle_properties"]!=null)
		{
			$jsontext .= '"puzzle_properties": {"rating": ' . $data["puzzle_properties"]->rating . ', "comment": "' . self::escapeJsonString($data["puzzle_properties"]->comment) . '"}, ';
		}
		
		if($location == "game")
		{
			$empty_meta_data = empty($data["game_meta_data"]);
			if(!$empty_meta_data)
			{
				$jsontext .= '"game_meta_data": [';
				foreach($data["game_meta_data"] as $meta_data)
				{
					$jsontext .= '{"key": "' . $meta_data->key . '", "value": "' . self::escapeJsonString($meta_data->value) . '"}, ';
				}
				$jsontext = substr($jsontext, 0, -2);
				$jsontext .= "], ";
			}

			$empty_breaks = empty($data["game_breaks"]);
			if(!$empty_breaks)
			{
				$jsontext .= '"game_breaks": [';
				foreach($data["game_breaks"] as $break)
				{
					$jsontext .= '{"halfMove": ' . $break->half_move . ', "breakType": ' . $break->break_type . ', "breakQuestion": "' . self::escapeJsonString($break->break_question) . '"}, ';
				}
				$jsontext = substr($jsontext, 0, -2);
				$jsontext .= "], ";
			}
		}
		
		if($location == "end_game" && $data["end_game_properties"]!=null)
		{
			$jsontext .= '"end_game_properties": {"title": "' . self::escapeJsonString($data["end_game_properties"]->title) . '", "description": "' . self::escapeJsonString($data["end_game_properties"]->description) . '", "result": "' . $data["object"]->result . '"}, ';
		}
		
		if($location == "multipleAnswers")
		{
			$jsontext .= '"question": "' . self::escapeJsonString($data["object"]->question) . '", ';
			
			$empty_answers = empty($data["question_answers"]);
			if(!$empty_answers)
			{
				$jsontext .= '"question_answers": [';
				foreach($data["question_answers"] as $answer)
				{
					$jsontext .= '{"answer": "' . self::escapeJsonString($answer->answer) . '", "correct": ' . $answer->correct . '}, ';
				}
				$jsontext = substr($jsontext, 0, -2);
				$jsontext .= "], ";
			}
		}
		
		$empty_themes = empty($data["object_themes"]);
		if(!$empty_themes)
		{
			$jsontext .= '"themes": [';
			foreach($data["object_themes"] as $theme)
			{
				$jsontext .= $theme->name . ', ';
			}
			$jsontext = substr($jsontext, 0, -2);
			$jsontext .= "], ";
		}
			
		$empty_comments = empty($data["object_comments"]);
		if(!$empty_comments)
		{
			$jsontext .= '"comments": [';
			foreach($data["object_comments"] as $comment)
			{
				$jsontext .= '{"halfMove": ' . $comment->half_move . ', "comment": "' . self::escapeJsonString($comment->comment) . '"}, ';
			}
			$jsontext = substr($jsontext, 0, -2);
			$jsontext .= "], ";
		}
			
		$empty_variations = empty($data["object_variations"]);
		if(!$empty_variations)
		{
			$jsontext .= '"variations": [';
			foreach($data["object_variations"] as $variation)
			{
				$jsontext .= '{"variationId": ' . $variation->variation_id . ', "halfMove": ' . $variation->half_move . ', "moves": "' . $variation->moves . '", ' . ($location=="puzzle"?'"solution": ' . ($variation->solution?'true':'false') . ', ':'') . '"parentVariationId": ' . $variation->parent_variation_id . '}, ';
			}
			$jsontext = substr($jsontext, 0, -2);
			$jsontext .= "], ";
		}
			
		$empty_variation_comments = empty($data["object_variation_comments"]);
		if(!$empty_variation_comments)
		{
			$jsontext .= '"variation_comments": [';
			foreach($data["object_variation_comments"] as $variation_comment)
			{
				$jsontext .= '{"variationId": ' . $variation_comment->variation_id . ', "halfMove": ' . $variation_comment->half_move . ', "comment": "' . self::escapeJsonString($variation_comment->comment) . '"}, ';
			}
			$jsontext = substr($jsontext, 0, -2);
			$jsontext .= "], ";
		}
			
		return substr($jsontext, 0, -2) . '}';
	}
	
	static function convertSelectionToJSON($data, $object_serial)
	{
		$jsontext = '{"object_serial": "' . $object_serial[0] . '", "object_correct": "' . $object_serial[1] . '", "object_wrong": "' . $object_serial[2] . '", ';
		$jsontext .= '"game": {"fen": "' . $data->get_fen() . '"}, ';
		$jsontext .= '"selection": {"question": "' . self::escapeJsonString($data->get_question()) . '", "description": "' . self::escapeJsonString($data->get_description()) . '", "selections": "' . $data->get_selections() . '"}';
		return $jsontext . '}';
	}	
	
	static function convertExcerciseMistakesToJSON($data, $object_serial)
	{
		$jsontext = '{"object_serial": "' . $object_serial[0] . '", ';
		$jsontext .= '"components": [';
		foreach ($data as $component)
		{
			$location = "";
			switch($component->get_type())
			{
				case 1: $location = "puzzle"; break;
				case 2: $location = "multipleAnswers"; break;
				case 3: $location = "selection"; break;
			}
			$component_serial = self::storeObject($location, $component->get_type_object_id());
			$jsontext .= '{"type": ' . $component->get_type() . ', "typeObjectSerial": "' . $component_serial[0] . '", "typeObjectCorrect": "' . $component_serial[1] . '", "typeObjectWrong": "' . $component_serial[2] . '"}, ';
		}
		return substr($jsontext, 0, -2) . "]}";
	}	
	
	static function convertExcerciseToJSON($data, $object_serial)
	{
		$jsontext = '{"object_serial": "' . $object_serial[0] . '", ';
		$jsontext .= '"excercise": {"title": "' . self::escapeJsonString($data["excercise"]->title) . '", "description": "' . self::escapeJsonString($data["excercise"]->description) . '"}, ';
		$jsontext .= '"components": [';
		foreach ($data["components"] as $component)
		{
			$location = "";
			switch($component->type)
			{
				case 1: $location = "puzzle"; break;
				case 2: $location = "multipleAnswers"; break;
				case 3: $location = "selection"; break;
			}
			$component_serial = self::storeObject($location, $component->type_object_id);
			$jsontext .= '{"type": ' . $component->type . ', "typeObjectSerial": "' . $component_serial[0] . '", "typeObjectCorrect": "' . $component_serial[1] . '", "typeObjectWrong": "' . $component_serial[2] . '"}, ';
		}
		return substr($jsontext, 0, -2) . "]}";
	}	
	
	static function storeObject($location, $object_id)
	{
		$location_arr = Session::retrieve("plo_location_" . $location);
		if(is_null($location_arr))
			$location_arr = array();
		else
			$location_arr = unserialize($location_arr);
		
		$object_serial = Utilities::randomCharSet();
		do
		{
			$in_array = false;
			foreach($location_arr as $obj)
			{
				if($obj[0]==$object_serial)
				{
					$object_serial = Utilities::randomCharSet();
					$in_array = true;
					break;
				}
			}
		}while($in_array);
		
		$correct = Utilities::randomCharSet();
		do
		{
			$wrong = Utilities::randomCharSet();
		}
		while($wrong == $correct);
		$serial_arr = [$object_serial, $correct, $wrong];
		
		$location_arr[$object_id] = $serial_arr;
		Session::register("plo_location_" . $location, serialize($location_arr));
		return $serial_arr;
	}
	
	static function getObject($location, $object_serial)
	{
		$location_arr = Session::retrieve("plo_location_" . $location);
		if(is_null($location_arr))
			return false;
		
		$location_arr = unserialize($location_arr);
		
		$object_id = null;
		foreach($location_arr as $id => $obj)
		{
			if($obj[0]==$object_serial)
				return [$id, $obj];
		}
		
		return false;
	}
	
	static function getSolutionObject($location, $object, $solution)
	{
		$location_arr = Session::retrieve("plo_location_" . $location);
		if(is_null($location_arr))
			return false;
		
		$location_arr = unserialize($location_arr);
		$rtn = false;
		if(array_key_exists($object, $location_arr))
		{
			if($location_arr[$object][1] == $solution)
				$rtn = 1;
			elseif($location_arr[$object][2] == $solution)
				$rtn = 0;
			unset($location_arr[$object]);
		}
		
		return $rtn;
	}

	static function escapeJsonString($value)
	{
		# list from www.json.org: (\b backspace, \f formfeed)
		//$escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
		//$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
		$escapers =     array("\\",     "/",   "\"",  "\r\n", "\n",  "\r",  "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "</br>", "</br>", "</br>", "\\t",  "\\f",  "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	
	static function loadChessboardScripts()
	{
		$html = array();
		$html[] = '<link rel="stylesheet" type="text/css" href="' . Path::get_url_path() . 'plugins/chessboard/assets/style/mainstyle.css"/>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/chessboard.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/piece.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/pawn.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/rook.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/bishop.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/knight.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/queen.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pieces/king.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/language/language.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/assets/json2.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/assets/languages/language_NL.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/engine.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/boardChange.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/comment.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/pgnfile.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/move.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/variationList.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/game.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/nag.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/game/preload.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/module/moduleManager.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/puzzle/puzzleModule.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/selection/selectionModule.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/game/gameModule.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/excercise/excerciseModule.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/multipleAnswers/multipleAnswersModule.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/module/ajaxRequest.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/module/helper.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/pieceAbstract.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/piece.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/board.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/row.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/promotion.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/moves.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/move.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/info.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/variation.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/enlarge.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/upload.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/engine.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/button.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/bigButton.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/seperator.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/change.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/commentArea.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/linkButton.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/load.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/actions.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/drag.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/radio.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/statusImage.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/puzzle/timer.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/puzzle/puzzleInfo.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/puzzle/puzzleActions.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/game/gameInfo.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/game/endGameInfo.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/selection/selectionInfo.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/multipleAnswers/multipleAnswersInfo.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/multipleAnswers/answer.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path::get_url_path() . 'plugins/chessboard/lay-out/excercise/excerciseInfo.js"></script>';
		$html[] = '<script type="text/javascript">';
	//	$html[] = '<!--';
		$html[] = '  chssOptions.images_url = "' . Path::get_url_path() . 'plugins/chessboard/assets/images/"';
		$html[] = '  chssOptions.root_url = "' . Path::get_url_path() . 'plugins/chessboard/"';
	//	$html[] = '-->';
		$html[] = '</script>';
		return implode("\n", $html);
	}

}