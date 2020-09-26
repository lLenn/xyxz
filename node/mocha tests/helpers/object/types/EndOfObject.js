EndOfObject = function() {
}

EndOfObject.is = function(pVariable) {
	return (pVariable !== null && typeof pVariable === "object" && typeof pVariable.isEndOfObject === "function" && pVariable.isEndOfObject() === true);
}

EndOfObject.prototype = {
	constructor: EndOfObject,
	isEndOfObject: function() {
		return true;
	},

	copy: function() {
		return new EndOfObject();
	}
}

module.exports = EndOfObject;