EndOfType = function() {
}

EndOfType.is = function(pVariable) {
	return (pVariable !== null && typeof pVariable === "object" && typeof pVariable.isEndOfType === "function" && pVariable.isEndOfType() === true);
}

EndOfType.prototype = {
	constructor: EndOfType,
	isEndOfType: function() {
		return true;
	},

	copy: function() {
		return new EndOfType();
	}
}

module.exports = EndOfType;