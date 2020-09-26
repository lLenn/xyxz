const TypeDefinition = require("./TypeDefinition.js");
const  EndOfType = require("./EndOfType.js");

ObjectType = function(pRequired, pObjectDefinition, pRandom, pDepth) {
	this.typeDefinition(pRequired);

	this.instance;
	this.objectDefinition = pObjectDefinition;
	this.depth = pDepth;
	this.random = pRandom;

	this.testObject;
}

ObjectType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: ObjectType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		this._createInstance(pTestObject);
		if(this.objectDefinition !== "self" || (this.depth === undefined && TestObject.MAX_DEPTH_TEST_OBJECT > pTestObject.depth) || (this.depth !== undefined && this.depth > pTestObject.depth)) {
			if(pPointer === 0 || this.testObject === undefined) {
				this.testObject = this._createTestObject(pTestObject);
				if(this.random === false) {
					return this.testObject.getCurrentState();
				} else {
					return this.testObject.createRandomObject();
				}
			} else {
				if(this.random === false) {
					if(this.testObject.setNextState() === true) {
						return this.testObject.getCurrentState();
					} else {
						this.testObject === undefined;
						return new EndOfType();
					}
				} else {
					if(pPointer === 0) {
						return this.testObject.createRandomObject();
					} else {
						return new EndOfType();
					}
				}
			}
		} else {
			if(this.random === true) {
				return undefined;
			} else {
				return new EndOfType();
			}
		}
	},

	hasDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		this._createInstance(pTestObject);
		if(this.objectDefinition !== "self" || (this.depth === undefined && TestObject.MAX_DEPTH_TEST_OBJECT > pTestObject.depth) || (this.depth !== undefined && this.depth > pTestObject.depth)) {
			var testObject = this.testObject;
			if(testObject === undefined) {
				testObject = this._createTestObject(pTestObject);
			}
			for(var i = 0, len = this.instance.properties.length; i < len; i++) {
				if(this.instance.properties[i].getTypesWithDependency(pPreviousObject, pCurrentObject, pTestObject).length > 0) {
					return true;
				}
			}
		}
		return false;
	},

	_createInstance: function(pTestObject) {
		if(this.instance === undefined) {
			if(this.objectDefinition === "self") {
				this.instance = pTestObject.definition;
			} else {
				this.instance = new this.objectDefinition();
			}
		}
	},

	_createTestObject: function(pTestObject) {
		var depth = pTestObject.depth;
		if(this.objectDefinition === "self") {
			depth++;
		}
		return new TestObject(this.instance, pTestObject.allCombinations, depth, pTestObject);
	},

	copy: function() {
		return new ObjectType(this.required, this.objectDefinition, this.random, this.depth);
	}
});

module.exports = ObjectType;