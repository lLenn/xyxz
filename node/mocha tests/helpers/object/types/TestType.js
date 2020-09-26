const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");

TestType = function(pRequired) {
	this.typeDefinition(pRequired);
}

TestType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: TestType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		switch(pPointer) {
			case 0: return "a";
			default: return new EndOfType();
		}
	},

	copy: function() {
		return new TestType(this.required);
	}
});

module.exports = TestType;