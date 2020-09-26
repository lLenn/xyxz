const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");

BooleanType = function(pRequired) {
	this.typeDefinition(pRequired);
}

BooleanType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: BooleanType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		switch(pPointer) {
			case 0: return false;
			case 1: return true;
			default: return new EndOfType();
		}
	},

	copy: function() {
		return new BooleanType(this.required);
	}
});

module.exports = BooleanType;