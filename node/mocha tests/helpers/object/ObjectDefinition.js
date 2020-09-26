const EndOfObject = require("./types/EndOfObject.js");
const UniqueType = require("./types/UniqueType.js");

ObjectDefinition = function(pPropertyDefinitions, pClassConstructor) {
	if(pPropertyDefinitions === undefined) {
		pPropertyDefinitions = [];
	}

	this.properties = pPropertyDefinitions;
	this.classConstructor = pClassConstructor;
	this.propertyPointer = 0;
}

ObjectDefinition.createFrom = function(pProperties) {
	var object = function() {
		this.objectDefinition(pProperties);
	}

	object.prototype = Object.assign({}, ObjectDefinition.prototype, {
		constructor: object,
		objectDefinition: ObjectDefinition
	});

	return object;
}

ObjectDefinition.isObjectDefinitionConstructor = function(pConstructor) {
	return !(typeof pConstructor !== "function" || typeof pConstructor.prototype.isObjectDefinition !== "function" || new pConstructor().isObjectDefinition() !== true);
}

ObjectDefinition.prototype = {
	constructor: ObjectDefinition,

	isObjectDefinition: function() {
		return true;
	},

	iterate: function(definition, callback) {
		for(var i = 0, len = this.properties.length; i < len; i++) {
			callback.call(null, this.properties[i]);
		}
	},

	getProperty: function(property) {
		for(var i = 0, len = this.properties.length; i < len; i++) {
			if(this.properties[i].name === property) {
				return this.properties[i];
			}
		}
	},

	next: function(pPreviousObject, pCurrentObject, pTestObject, pDependency) {
		if(pDependency === undefined) {
			pDependency = false
		}
		if(this.propertyPointer === this.properties.length) {
			return new EndOfObject();
		}
		if(this.properties[this.propertyPointer].hasType(UniqueType) === false) {
			var value = this.properties[this.propertyPointer].next(pPreviousObject, pCurrentObject, pTestObject, pDependency);
			if(EndOfType.is(value)) {
				this.properties[this.propertyPointer].reset();
				this.propertyPointer++;
				return this.next(pPreviousObject, pCurrentObject, pTestObject, pDependency);
			}
			return value;
		} else {
			this.propertyPointer++;
			return this.next(pPreviousObject, pCurrentObject, pTestObject, pDependency);
		}
	},

	nextDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		return this.next(pPreviousObject, pCurrentObject, pTestObject, true);
	},

	current: function() {
		return this.properties[this.propertyPointer];
	},

	reset: function() {
		this.propertyPointer = 0;
		while(this.properties[this.propertyPointer].hasType(UniqueType) === true) {
			this.propertyPointer++;
		}
	},

	resetAll: function() {
		this.reset();
		for(var i = 0, len = this.properties.length; i < len; i++) {
			this.properties[i].resetAll();
		}
	},

	copy: function() {
		return new ObjectDefinition(this.properties.map(function(pProperty) { return pProperty.copy(); }), this.classConstructor);
	}
}

module.exports = ObjectDefinition;