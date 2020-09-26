const StringType = require("./types/StringType.js");
const BooleanType = require("./types/BooleanType.js");
const NumberType = require("./types/NumberType.js");
const DateType = require("./types/DateType.js");
const ArrayType = require("./types/ArrayType.js");
const ParentType = require("./types/ParentType.js");
const ObjectType = require("./types/ObjectType.js");
const TestType = require("./types/TestType.js");
const ValuesType = require("./types/ValuesType.js");
const InputType = require("./types/InputType.js");
const UniqueType = require("./types/UniqueType.js");

const NumberFilter = require("./filters/NumberFilter.js");
const DateFilter = require("./filters/DateFilter.js");
const StringFilter = require("./filters/StringFilter.js");

PropertyDefinition = function(pName, pTypes) {
	if(Array.isArray(pTypes) === false) {
		throw new Error("PropertyDefinition: constructor expects an array as second argument!");
	}
	this.name = pName;
	this.types = pTypes;
	this.typesPointer = 0;

	//backwards compatibility
	for(var i = 0, len = this.types.length; i < len; i++) {
		if(typeof this.types[i] === "string") {
			this.types[i] = this._getTypeValues(this.types[i]);
		}
	}
}

PropertyDefinition.prototype = {
	constructor: PropertyDefinition,

	current: function() {
		return this.getTypeObject(this.typesPointer);
	},

	next: function(pPreviousObject, pCurrentObject, pTestObject, pDependency) {
		if(pDependency === undefined) {
			pDependency = false
		}
		if(this.typesPointer === this.types.length) {
			return new EndOfType();
		}
		if(pDependency === true || this.getTypeObject(this.typesPointer).hasDependency(pPreviousObject, pCurrentObject, pTestObject) === false) {
			var value = this.getTypeObject(this.typesPointer).next(pPreviousObject, pCurrentObject, pTestObject);
			if(EndOfType.is(value)) {
				this.getTypeObject(this.typesPointer).reset();
				this.typesPointer++;
				return this.next(pPreviousObject, pCurrentObject, pTestObject, pDependency);
			}
		} else {
			this.typesPointer++;
			return this.next(pPreviousObject, pCurrentObject, pTestObject, pDependency);
		}
		return value;
	},

	nextDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		return this.next(pPreviousObject, pCurrentObject, pTestObject, true);
	},

	reset: function() {
		this.typesPointer = 0;
		for(var i = 0, len = this.types.length; i < len; i++) {
			if(this.types[i] instanceof UniqueType === false) {
				this.getTypeObject(i).reset();
			}
		}
	},

	resetAll: function() {
		this.reset();
	},

	getTypeObject: function(pIndex) {
		if(pIndex === this.types.length) {
			return new EndOfType();
		}
		return this.types[pIndex];
	},

	getType: function(pType) {
		for(var i = 0, len = this.types.length; i < len; i++) {
			if(this.types[i] instanceof pType) {
				return this.types[i];
			}
		}

		return null;
	},

	getTypesWithDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		var types = [];
		for(var i = 0, len = this.types.length; i < len; i++) {
			if(this.types[i].hasDependency(pPreviousObject, pCurrentObject, pTestObject)) {
				types.push(this.types[i]);
			}
		}
		return types;
	},

	hasType: function(pType) {
		for(var i = 0, len = this.types.length; i < len; i++) {
			if(this.types[i] instanceof pType) {
				return true;
			}
		}

		return false;
	},

	//functions for backwards compatibility
	isNotRequired: function() {
		this._addPropertyToTypes("required", false);

		return this;
	},

	chooseFrom: function(pValues) {
		if(typeof pValues !== "function" && Array.isArray(pValues) === false) {
			throw new Error("PropertyDefinition: chooseFrom expects an array or function as first argument!");
		}
		this.types = [new ValuesType(this.types[0].required, pValues)];

		return this;
	},

	canNotBeEmpty: function() {
		this._addPropertyToTypes("empty", false);

		return this;
	},

	isLinkedWith: function(pLink) {
		throw new Error("Convert 'linked' to ParentType!");
	},

	hasLengthOf: function(pLength) {
		this._addPropertyToTypes("length", pLength);

		return this;
	},

	withFilter: function(pComparison, pValue) {
		this._addPropertyToTypes("filters", { comparison: pComparison, value: pValue });

		return this;
	},

	withDictionary: function(pDictionary) {
		this._addPropertyToTypes("dictionary", pDictionary);

		return this;
	},

	ofType: function(pObjectDefinition) {
		if(ObjectDefinition.isObjectDefinitionConstructor(pObjectDefinition) === true) {
			this._addPropertyToTypes("objectDefinition", pObjectDefinition);
		} else {
			throw new Error("PropertyDefinition: type '" + pObjectDefinition + "' isn't of the class ObjectDefinition!");
		}
		return this;
	},

	ofSelf: function() {
		this._addPropertyToTypes("objectDefinition", "self");

		return this;
	},

	withDepth: function(pDepth) {
		if(typeof pDepth !== "number") {
			throw new Error("PropertyDefinition: withDepth expects a number as first argument!");
		}
		this._addPropertyToTypes("objectDepth", pDepth);

		return this;
	},

	withCallback: function(pCallback) {
		this._addPropertyToTypes("callback", pCallback);

		return this;
	},

	_getTypeValues: function(pType) {
		switch(pType) {
			case "string": return new StringType();
			case "boolean": return new BooleanType();
			case "number": return new NumberType();
			case "date": return new DateType(undefined, undefined, "all");
			case "date_unix_epoch": return new DateType(undefined, undefined, "unix_epoch");
			case "date_ISO": return new DateType(undefined, undefined, "ISO");
			case "array": return new ArrayType();
			case "parent": return new ParentType();
			case "object": return new ObjectType(undefined, undefined, false);
			case "test": return new TestType();
			case "values": return new ValuesType();
			case "linked": throw new Error("Convert 'linked' to ParentType!");
			case "input": return new InputType();
			case "uniqID": return new UniqueType();
			default: throw new Error("PropertyDefinition: Type " + pType + " isn't supported!");
		}
	},

	_addPropertyToTypes: function(pProp, pValue) {
		for(var i = 0, len = this.types.length; i < len; i++) {
			switch(pProp) {
				case "filters":
					var filter;
					switch(this.types[i].constructor.name) {
						case "StringType": filter = new StringFilter(pValue.comparison, pValue.value); break;
						case "NumberType": filter = new NumberFilter(pValue.comparison, pValue.value); break;
						case "DateType": filter = new DateFilter(pValue.comparison, pValue.value); break;
						case "InputType": filter = new InputFilter(pValue.comparison, pValue.value); break;
						default: throw new Error("PropertyDefinition: Type " + this.types[i].constructor.name + " isn't supported!");
					}
					this.types[i].filters.push(filter);
					break;
				case "required":
					this.types[i].required = pValue;
					break;
				case "empty":
					if(["StringType", "ArrayType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].empty = pValue;
					}
					break;
				case "length":
					if(["ArrayType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].length = pValue;
					}
					break;
				case "dictionary":
					if(["StringType", "InputType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].dictionary = pValue;
					}
					break;
				case "objectDefinition":
					if(["ArrayType", "ObjectType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].objectDefinition = pValue;
					}
					break;
				case "objectDepth":
					if(["ArrayType", "ObjectType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].depth = pValue;
					}
					break;
				case "callback":
					if(["ParentType", "ValuesType"].indexOf(this.types[i].constructor.name) !== -1) {
						this.types[i].callback = pValue;
					}
					break;
			}
		}
	},

	copy: function() {
		return new PropertyDefinition(this.name, this.types.map(function(pType) { return pType.copy(); }));
	}
}

module.exports = PropertyDefinition;