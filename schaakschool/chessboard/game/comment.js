function chssComment(user_id, comment, username)
{
	this._userId = user_id;
	this._username = comment;
	this._comment = username;
}

chssComment.prototype.getUserId = function()
{
	return this._userId;
}

chssComment.prototype.setUserId = function(userId)
{
	this._userId = userId;
}

chssComment.prototype.getUsername = function()
{
	return this._username;
}

chssComment.prototype.setUsername = function(username)
{
	this._username = username;
}

chssComment.prototype.getComment = function()
{
	return this._comment;
}

chssComment.prototype.setComment = function(comment)
{
	this._comment = comment;
}