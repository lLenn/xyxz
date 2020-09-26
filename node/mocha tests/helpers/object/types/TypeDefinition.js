const Filter = require("../filters/Filter.js");
const EndOfType = require("./EndOfType.js");
const EndOfObject = require("./EndOfObject.js");

TypeDefinition = function(pRequired, pFilters, pDependency) {
	if(typeof this.next !== "function") {
		throw new Error("Classes of the TypeDefinition interface should implement the next method!");
	}
	if(typeof this.copy !== "function") {
		throw new Error("Classes of the TypeDefinition interface should implement the copy method!");
	}
	if(pRequired === undefined) {
		pRequired = true;
	}
	if(pFilters === undefined) {
		pFilters = [new Filter()];
	}
	if(pDependency === undefined) {
		pDependency = false;
	}
	this.dependency = pDependency;
	this.required = pRequired;
	if(this.required === false) {
		this.pointer = 0;
	} else {
		this.pointer = 2;
	}
	this.filters = pFilters;
}

TypeDefinition.prototype = {
	constructor: TypeDefinition,

	next: function(pPreviousObject, pCurrentObject, pTestObject) {
		if(this.required === false && this.pointer === 0) {
			this.pointer++;
			return undefined;
		} else if(this.required === false && this.pointer === 1) {
			this.pointer++;
			return null;
		} else {
			if(this.pointer < 2) {
				this.pointer = 2;
			}
			this.pointer++;
			if(this.pointer === 100) {
				throw new Error(this.constructor.name + ": Possible eternal loop detected! Make sure that the class which implements the getValue method of the TypeDefinition interface returns EndOfType instance at some point!");
			}
			var value = this.getValue(this.pointer-3, pTestObject);
			var compare = true;
			for(var i = 0, len = this.filters.length; i < len; i++) {
				compare = compare && this.filters[i].compare(value);
			}
			if(!(EndOfType.is(value) || EndOfObject.is(value)) && compare === false) {
				return this.next(pTestObject);
			}
			return value;
		}
	},

	getMaxPointer: function(pTestObject) {
		var pointer = 0;
		while((EndOfType.is(this.getValue(pointer, pTestObject))) === false) {
			pointer++;
			if(pointer === 100) {
				throw new Error("Possible eternal loop detected! Make sure that the class which implements the getValue method of the TypeDefinition interface returns EndOfType instance at some point!");
			}
		}
		return pointer;
	},

	getRandom: function(pTestObject, pDepth) {
		if(pDepth === undefined) {
			pDepth = 0;
		}
		if(pDepth === 100) {
			throw new Error("Possible eternal loop detected! Make sure that the filter on the class compares correct values and doesn't exlude all possibilities!");
		}
		var randPointer = Math.floor(Math.random() * this.getMaxPointer(pTestObject));
		var value = this.getValue(randPointer, pTestObject);
		var compare = true;
		for(var i = 0, len = this.filters.length; i < len; i++) {
			compare = compare && this.filters[i].compare(value);
		}
		if(!(EndOfType.is(value) || EndOfObject.is(value)) && compare === false) {
			return this.getRandom(pTestObject, pDepth + 1);
		}
		return value;
	},

	reset: function() {
		if(this.required === false) {
			this.pointer = 0;
		} else {
			this.pointer = 2;
		}
	},

	hasDependency: function(pPreviousObject, pCurrentObject, pTestObject) {
		if(typeof this.dependency === "function") {
			var parents = [];
			var parentTestObject = pTestObject.parent;
			while(parentTestObject != null) {
				parents.unshift(parentTestObject)
				parentTestObject = parentTestObject.parent;
			}
			return this.dependency(pPreviousObject===null?{}:pPreviousObject, pCurrentObject, parents);
		}
		return false;
	}
}

module.exports = TypeDefinition;