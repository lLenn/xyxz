TypeDictionary = function() {
}

TypeDictionary.prototype = {
	constructor: TypeDictionary,
	copy: function() {
		return new TypeDictionary();
	}
}

module.exports = TypeDictionary;