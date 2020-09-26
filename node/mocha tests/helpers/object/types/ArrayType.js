const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");

/**
 * @name nixpsit.unit.ArrayType
 * @description Creates a type object for arrays.
 * When no object definition is set it will use string as items, otherwise it will set random objects created from the definition as items.
 * @param {boolean} pRequired - indicates if the property is required in the object
 * @param {boolean} [pEmpty=false] - indicates if the array can be empty
 * @param {ObjectDefinition|"self"} [pObjectDefinition=undefined] - indicates if the array needs to be populated with a specific object, use "self" when the object is the same definition as the parent
 * @param {number} [pLength=undefined] - if an object definition is defined this will set a specific length for the array
 * @param {number} [pDepth=undefined] - if an object definition is set to "self" this will set the depth the array will iterate before stopping to avoid an endless loop; if not defined the depth will be: TestObject.MAX_DEPTH_TEST_OBJECT
 * @param {boolean} [pRandom=false] - if an object definition is defined this will make sure every object is random, otherwise the items of the array wil contain objects from a previous iteration
 */
ArrayType = function(pRequired, pEmpty, pObjectDefinition, pLength, pDepth, pRandom) {
	this.typeDefinition(pRequired);

	if(pEmpty === undefined) {
		pEmpty = true;
	}

	if(pRandom === undefined) {
		pRandom = false;
	}

	this.empty = pEmpty;
	this.instance;
	this.objectDefinition = pObjectDefinition;
	this.depth = pDepth;
	this.testObject;
	this.length = pLength;
	this.random = pRandom;

	this.currentArray;
}

ArrayType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: ArrayType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		if(this.objectDefinition !== undefined) {
			this._createInstance(pTestObject);
			if(this.objectDefinition !== "self" || (this.depth === undefined && TestObject.MAX_DEPTH_TEST_OBJECT > pTestObject.depth) || (this.depth !== undefined && this.depth > pTestObject.depth)) {
				var depth = pTestObject.depth;
				if(this.objectDefinition === "self") {
					depth++;
				}
				this.testObject = this._createTestObject(pTestObject);
				if(this.length !== undefined) {
					if(pPointer === 0) {
						var val = [];
						for(var i = 0; i < this.length; i++) {
							val.push(this.testObject.createRandomObject());
						}
						return val;
					} else {
						return new EndOfType();
					}
				} else {
					switch(pPointer) {
						case 0: this.currentArray = [this.testObject.createRandomObject()]; break;
						case 1:
							if(this.random === true || Array.isArray(this.currentArray) === false) {
								this.currentArray = [this.testObject.createRandomObject(), this.testObject.createRandomObject(), this.testObject.createRandomObject()]
							} else {
								this.currentArray.unshift(this.testObject.createRandomObject());
								this.currentArray.unshift(this.testObject.createRandomObject());
							}
							break;
						case 2:
							if(this.random === true) {
								this.currentArray = [this.testObject.createRandomObject(), this.testObject.createRandomObject()];
							} else if (Array.isArray(this.currentArray) === false) {
								var random = this.testObject.createRandomObject();
								this.currentArray = [random, random];
							} else {
								this.currentArray.shift();
								this.currentArray[0] = this.currentArray[1];
							}
							break;
						case 3: if(this.empty === true) { this.currentArray = []; break; } else { this.pointer++; };
						default: this.currentArray = new EndOfType();
					}

					if(Array.isArray(this.currentArray)) {
						var newArray = this.currentArray.map(function(pObject) { return TestObject.copyObject(pObject); });
					} else {
						var newArray = this.currentArray;
					}

					return newArray;
				}
			} else {
				return new EndOfType();
			}
		} else {
			switch(pPointer) {
				case 0: if(this.empty === true) { return []; } else { this.pointer++; };
				case 1: return ["10"];
				case 2: return ["30", "20", "10"];
				default: return new EndOfType();
			}
		}
	},

	hasDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		if(this.objectDefinition !== undefined) {
			this._createInstance(pTestObject);
			if(this.objectDefinition !== "self" || (this.depth === undefined && TestObject.MAX_DEPTH_TEST_OBJECT > pTestObject.depth) || (this.depth !== undefined && this.depth > pTestObject.depth)) {
				var testObject = this.testObject;
				if(testObject === undefined) {
					testObject = this._createTestObject(pTestObject);
				}
				for(var i = 0, len = this.instance.properties.length; i < len; i++) {
					if(this.instance.properties[i].getTypesWithDependency(pPreviousObject, pCurrentObject, testObject).length > 0) {
						this.currentArray = undefined;
						return true;
					}
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
		return new ArrayType(this.required, this.empty, this.objectDefinition, this.length, this.depth, this.random);
	}
});

module.exports = ArrayType;