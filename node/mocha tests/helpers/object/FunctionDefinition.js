const PropertyDefinition = require("./PropertyDefinition.js");

const ALLOWED_DEFINITION_TYPES = ["string", "number", "date", "date_unix_epoch", "array", "boolean", "object", "test", "uniqID", "parent", "values", "function"];
//ToDo: add type for functions which just does stuff and doesn't return anything, like an event function and mock function which should be skipped by fillObject of options in TestObject
FunctionDefinition = function(pName, pTypes, pThenable, pDelays) {
	this.propertyDefinition(pName, pTypes);

	this.thenable = pThenable;
	this.delays = [300, 10, 100];

	this.functionPointer = 0;
	this.delaysPointer = 0;
}

FunctionDefinition.prototype = Object.assign({}, PropertyDefinition.prototype, {
	constructor: FunctionDefinition,
	propertyDefinition: PropertyDefinition,

	withDelays: function(pDelays) {
		if(Array.isArray(pDelay) === false) {
			throw new Error("FunctionExpectation: withDelays expects an array as first argument!");
		}
		this.delays = pDelays;

		return this;
	},

	next: function(pTestObject) {
		if(this.functionPointer === 0 && this.thenable === undefined) {
			this.functionPointer++;
		}
		if(this.typesPointer === this.types.length && this.functionPointer === 1) {
			return new EndOfType();
		} else if(this.typesPointer === this.types.length) {
			this.delayPointer++;
			this.typesPointer = 0;
			if(this.delayPointer === this.delays.length) {
				this.functionPointer++;
			}
		}
		var value = this.getTypeObject(this.typesPointer).next(pTestObject);
		if(EndOfType.is(value)) {
			this.getTypeObject(this.typesPointer).reset();
			this.typesPointer++;
			return this.next(pTestObject);
		}
		if(this.functionPointer === 0) {
			return (function(pValue, pDelay) {
				return function() {
					return $.Deferred(function(pDefer) {
						setTimeout(function() { pDefer.resolve(pValue) }, pDelay);
					});
				}
			})(value, this.delays[this.delayPointer])
		} else {
			return (function(pValue) { return function() { return pValue } })(value);
		}
	},

	reset: function() {
		this.typesPointer = 0;
		this.functionPointer = 0;
		this.delayPointer = 0;
		for(var i = 0, len = this.types.length; i < len; i++) {
			this.getTypeObject(i).reset();
		}
	},

	resetAll: function() {
		this.reset();
	},

	copy: function() {
		return new FunctionDefinition(this.name, this.types.map(function(pType) { return pType.copy(); }), this.thenable, this.delays);
	}
});

module.exports = FunctionDefinition;