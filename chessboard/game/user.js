function chssUser(username, name, surname, email, rating)
{
	this._username = username;
	this._name = name;
	this._surname = surname;
	this._email = email;
	this._rating = rating;
}

chssUser.prototype = {
		constructor: chssUser,
		setUsername: function(username)
		{
			this._username = username;
		},
		
		getUsername: function()
		{
			return this._username;
		},
		
		setName: function(name)
		{
			this._name = name;
		},
		
		getName: function()
		{
			return this._name;
		},
		
		setSurname: function(surname)
		{
			this._surname = surname;
		},
		
		getSurname: function()
		{
			return this._surname;
		},
		
		setRating: function(rating)
		{
			this._rating = rating;
		},
		
		getRating: function()
		{
			return this._rating;
		},
		
		setEmail: function(email)
		{
			this._email = email;
		},
		
		getEmail: function()
		{
			return this._email;
		}
}