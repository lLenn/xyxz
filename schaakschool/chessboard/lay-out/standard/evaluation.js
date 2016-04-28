function chssEvaluation()
{
	this._wrapper = document.createElement("div");
	
	this._label = document.createElement("div");
	this._label.innerHTML = "{$string.evaluation}";

	this._evaluationLegend = document.createElement("div");
	this._evaluationLegend.style.position = "relative";
	this._middleMarker = document.createElement("div");
	this._middleMarker.style.position = "absolute";
	this._middleMarker.innerHTML = "|";
	this._scoreLabel = document.createElement("div");
	this._scoreLabel.style.position = "absolute";
	this._scoreLabel.innerHTML = "0";

	this._evaluationLegend.appendChild(this._scoreLabel);
	this._evaluationLegend.appendChild(this._middleMarker);
	
	this._evaluationBar = document.createElement("div");
	this._evaluationBar.style.backgroundColor = chssOptions.alt_color;
	this._evaluationScore = document.createElement("div");
	this._evaluationFiller = document.createElement("div");
	
	this._barClearFloat = document.createElement("br");
	this._barClearFloat.className = "clearfloat";
	
	this._evaluationBar.appendChild(this._evaluationFiller);
	this._evaluationBar.appendChild(this._evaluationScore);
	
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._evaluationLegend);
	this._wrapper.appendChild(document.createElement("br"));
	this._wrapper.appendChild(this._evaluationBar);
}

chssEvaluation.prototype = {
		constructor: chssEvaluation,
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		draw: function()
		{
			var height = 7 * (chssOptions.board_size/360)
			
			this._middleMarker.style.left = this._evaluationLegend.offsetWidth/2 - this._middleMarker.offsetWidth/2 + "px";
			
			this._evaluationScore.style.height = height + "px";
			this._evaluationFiller.style.height = height + "px";
			this._evaluationFiller.style.width = this._evaluationBar.offsetWidth/2 + "px";
			
			this.redraw();
		},
		
		redraw: function()
		{
			var move = chssBoard.chssGame.getMove(),
				score = undefined;
			
			if((typeof move === 'undefined' || move == null) && chssBoard.chssGame.getCurrentMove()==0)
				score = 0;
			else
				score = move.getEvaluation();

			if(typeof score !== 'undefined' && score != null)
			{
				score = Math.min(score, 20)==20?20:Math.max(score, -20);
				
				if(score >= 0)
				{
					this._evaluationFiller.style.float = "left";
					this._evaluationScore.style.float = "left";
					this._evaluationFiller.style.backgroundColor = chssOptions.white_color;
					this._evaluationScore.style.backgroundColor = chssOptions.white_color;
				}
				else
				{
					this._evaluationFiller.style.float = "right";
					this._evaluationScore.style.float = "right";
					this._evaluationFiller.style.backgroundColor = chssOptions.black_color;
					this._evaluationScore.style.backgroundColor = chssOptions.black_color;
				}
				
				this._scoreLabel.innerHTML = score;
				this._evaluationScore.style.width = (this._evaluationBar.offsetWidth/2) * Math.abs(score)/20 + "px";
			}
		}
}