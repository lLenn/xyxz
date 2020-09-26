const EndOfType = require("./types/EndOfType.js");
const EndOfObject = require("./types/EndOfObject.js");
const ObjectType = require("./types/ObjectType.js");
const ArrayType = require("./types/ArrayType.js");
const ParentType = require("./types/ParentType.js");
const PropertyDefinition = require("./PropertyDefinition.js");
const ObjectDefinition = require("./ObjectDefinition.js");

var uIDs = [];

var OBJECTS_NOT_TO_COPY = []
if(typeof HTMLDocument !== "undefined") {
	OBJECTS_NOT_TO_COPY.push(HTMLDocument);
}
if(typeof DocumentType !== "undefined") {
	OBJECTS_NOT_TO_COPY.push(DocumentType);
}
if(typeof HTMLElement !== "undefined") {
	OBJECTS_NOT_TO_COPY.push(HTMLElement);
}
if(typeof jQuery !== "undefined") {
	OBJECTS_NOT_TO_COPY.push(jQuery);
}

TestObject = function(definition, allCombinations, depth, parent) {
	if(allCombinations === undefined) {
		allCombinations = false;
	}
	if(depth === undefined) {
		depth = 0;
	}
	this.definition = TestObject.copyObject(definition);
	this.definition.resetAll();
	this.parent = parent;
	if(this.definition.classConstructor !== undefined) {
		this.object = new this.definition.classConstructor();
	} else {
		this.object = {};
	};
	if(this.definition.classConstructor !== undefined) {
		this.previousObject = new this.definition.classConstructor();
	} else {
		this.previousObject = {};
	};
	this.allCombinations = allCombinations;
	this.workingProperties = definition!==undefined?definition.properties.map(function(pItem) { return pItem.name; }):[];
	this.dependencies = [];
	this.depth = depth;
	this.initialized = false;
}

TestObject.MAX_DEPTH_TEST_OBJECT = 1;

TestObject.copyObject = function(object, depth, copiedObjects, withoutCircularRefs) {
	if(typeof object === "object" && typeof object.copy === "function") {
		return object.copy();
	}

	if(depth === undefined) {
		depth = 0;
	}
	if(copiedObjects === undefined) {
		copiedObjects = [];
	}
	if(withoutCircularRefs === undefined) {
		withoutCircularRefs = false;
	}
	if(depth === 255) {
		return;
	}
	var pointer;
	if(Array.isArray(object) === true) {
		var copy = [];
		for(var i = 0, len = object.length; i < len; i++) {
			copy[i] = TestObject.copyObject(object[i], depth+1, copiedObjects, withoutCircularRefs);
		}
		return copy;
	} else if(TestObject.isValidObject(object) === true) {
		if((pointer = TestObject.isCircular(object, copiedObjects)) !== null) {
			if(withoutCircularRefs === false) {
				return pointer;
			} else {
				return undefined;
			}
		} else {
			var proto = Object.getPrototypeOf(object);
			if(proto === null) {
				var copy = Object.assign({}, object);
			} else {
				var copy = Object.assign(Object.create(proto), object);
			}
			copiedObjects.push({ objectID: object.objectID, copy: copy });
			for(var prop in object) {
				try {
					if(proto[prop] === undefined) {
						copy[prop] = TestObject.copyObject(object[prop], depth+1, copiedObjects, withoutCircularRefs);
					}
				} catch(e) {
					throw e;
				}
			}
			return copy;
		}
	} else {
		return object;
	}
}

TestObject.getValue = function(pObject, pField) {
	if(pField !== undefined) {
		var subKeys = pField.split('.');
		var subObject = pObject;
		for (var i = 0, len = subKeys.length; i < len; i++) {
			var subKey = subKeys[i];
			if (subObject[subKey] === undefined) {
				return;
			}
			subObject = subObject[subKey];
		}
		return subObject;
	} else {
		return pObject;
	}
}

TestObject.isCircular = function(pObject, pCopiedObjects) {
	if(pObject.objectID === undefined) {
		return null;
	}
	for(var i = 0, len = pCopiedObjects.length; i < len; i++) {
		if(pCopiedObjects[i].objectID === pObject.objectID) {
			return pCopiedObjects[i].copy;
		}
	}

	return null;
}

TestObject.isValidObject = function(pObject) {
	if(pObject === null || typeof pObject !== "object" || Array.isArray(pObject) === true) {
		return false;
	}

	for(var i = 0, len = OBJECTS_NOT_TO_COPY.length; i < len; i++) {
		if(pObject instanceof OBJECTS_NOT_TO_COPY[i]) {
			return false;
		}
	}

	return true;
}

//ToDo: add type for functions which just does stuff and doesn't return anything, like an event function and mock function which should be skipped
TestObject.fillObject = function(pObject, pDepth) {
	if(pDepth === undefined) {
		pDepth = 0;
	}
	if(pDepth === 100) {
		return;
	}
	return $.Deferred(function(pDefer) {
		var defers = [];
		if(Array.isArray(pObject) === true) {
			for(var i = 0, len = pObject.length; i < len; i++) {
				defers.push(TestObject.replaceFunction(pObject, prop, pDepth));
			}
		} else if(TestObject.isValidObject(pObject) === true) {
			for(var prop in pObject) {
				defers.push(TestObject.replaceFunction(pObject, prop, pDepth));
			}
		}
		$.when.apply($, defers).then(pDefer.resolve, pDefer.reject, pDefer.progress);
	});
}

TestObject.replaceFunction = function(pObject, pProp, pDepth) {
	if(typeof pObject[pProp] === "function") {
		return $.Deferred(function(pDefer) {
			$.when(pObject[pProp]()).then(function(pResult) {
				$.when(TestObject.fillObject(pResult, pDepth+1)).then(function() {
					pObject[pProp] = pResult;
					pDefer.resolve();
				}, pDefer.reject, pDefer.progress);
			}, pDefer.reject, pDefer.progress);
		})
	} else {
		return TestObject.fillObject(pObject[pProp], pDepth+1);
	}
}

TestObject.prototype = {
	constructor: TestObject,

	setNextState: function() {
		if(this.definition === undefined) {
			return false;
		}

		if(this.initialized === false) {
			this.initialized = true;
			this.setToDefaultObject();
			return true;
		}

		if(this.dependencies.length === 0) {
			var currentPropertyPointer = this.definition.propertyPointer;
			var value = this.definition.next(this.previousObject, this.object, this);
			if(EndOfObject.is(value)) {
				return false;
			}
			this.previousObject = TestObject.copyObject(this.object);
			if(this.allCombinations === true && currentPropertyPointer !== this.definition.propertyPointer) {
				this.setToDefaultObject(this.definition.propertyPointer, true);
			}
			if(value !== undefined) {
				this.object[this.definition.current().name] = value;
			} else {
				delete this.object[this.definition.current().name];
			}
			var dependencies = this._getDependencies(this.definition, this.previousObject);
			if(this.allCombinations === true && currentPropertyPointer !== this.definition.propertyPointer) {
				this.definition.reset();
			}
			if(dependencies !== null) {
				this.dependencies.unshift({ previousObject: TestObject.copyObject(this.previousObject), dependencies: dependencies });
				return this.setNextState();
			}
		} else {
			var value = this.dependencies[0].dependencies.nextDependency(this.dependencies[0].previousObject, this.object, this);
			if(EndOfObject.is(value)) {
				this.dependencies.shift();
				return this.setNextState();
			}
			var previousObject = TestObject.copyObject(this.object);
			if(value !== undefined) {
				this.object[this.dependencies[0].dependencies.current().name] = value;
			} else {
				delete this.object[this.dependencies[0].dependencies.current().name];
			}
			var dependencies = this._getDependencies(this.definition, previousObject);
			if(dependencies !== null) {
				var properties = dependencies.properties;
				for(var i = 0, len = this.dependencies.length; i < len; i++) {
					var dependencies = this._getDependencies(this.definition, this.dependencies[i].previousObject);
					for(var j = 0, jLen = dependencies.properties.length; j < jLen; j++) {
						if(properties.some(function(pProperty) { return pProperty.constructor.name === dependencies.properties[i].constructor.name; }) === false &&
							this.dependencies[i].dependencies.properties.some(function(pProperty) { return pProperty.constructor.name === dependencies.properties[i].constructor.name; }) === false) {
							properties.push(dependencies.properties[i]);
						}
					}
				}
				if(properties.length > 0) {
					this.dependencies.unshift({ previousObject: previousObject, dependencies: new ObjectDefinition(properties, this.allCombinations) });
					return this.setNextState();
				}
			}
		}
		this.setUID();
		return true;
	},

	getCurrentState: function() {
		if(this.initialized === false) {
			this.initialized = true;
			this.setToDefaultObject();
		}
		return this.object;
	},

	createRandomObject: function(pCondition, pDepth) {
		if(pDepth === undefined) {
			pDepth = 0;
		}
		if(pDepth === 255) {
			throw new Error("Unable to create random object! Please check if the conditions allow for the creation thereof!");
		}
		var testObject = new TestObject(this.definition, this.allCombinations, this.depth, this.parent);
		var dependencies = [];
		for(var i = 0, len = this.workingProperties.length; i < len; i++) {
			var prop = this.workingProperties[i];
			var propDependencies = this.definition.getProperty(prop).getTypesWithDependency(testObject.previousObject, testObject.object, testObject);
			if(propDependencies.length === 0 && this.definition.getProperty(prop).hasType(UniqueType) === false) {
				if(this.definition.getProperty(prop).required === false && Math.floor(Math.random()*2) === 0) {
					testObject.object[prop] = undefined;
				} else if(this.definition.getProperty(prop).required === false && Math.floor(Math.random()*2) === 1) {
					testObject.object[prop] = null;
				} else {
					var randType = Math.floor(Math.random() * (this.definition.getProperty(prop).types.length-1));
					if(this.definition.getProperty(prop).types[randType] === "object" && this.definition.getProperty(prop).callback === undefined) {
						testObject.object[prop] = new ObjectType(this.definition.getProperty(prop).required, this.definition.getProperty(prop).objectDefinition, true, this.definition.getProperty(prop).objectDepth).getValue(0, testObject);
					} else if(this.definition.getProperty(prop).types[randType] === "parent") {
						testObject.object[prop] = new ParentType(this.definition.getProperty(prop).required, this.definition.getProperty(prop).callback).getValue(0, testObject);
					} else {
						testObject.object[prop] = this.definition.getProperty(prop).getTypeObject(randType).getRandom(testObject);
					}
				}
			} else if(propDependencies.length > 0) {
				var propDefinition = new PropertyDefinition(prop, propDependencies.map(function(pDependency) { return TestObject.copyObject(pDependency); }));
				propDefinition.resetAll();
				dependencies.push(propDefinition);
			} else if(this.definition.getProperty(prop).hasType(UniqueType)) {
				testObject.object[prop] = this.generateUID();
			}
		}
		for(var i = 0, len = dependencies.length; i < len; i++) {
			testObject.object[dependencies[i].name] = dependencies[i].nextDependency(testObject.previousObject, testObject.object, testObject);
		}
		if(typeof pCondition === "function" && pCondition.call(null, testObject.object) === false) {
			return this.createRandomObject(pCondition, pDepth+1);
		} else {
			return testObject.object;
		}
	},

	setToDefaultObject: function(untilIndex, skipDependencies) {
		if(untilIndex === undefined) {
			untilIndex = this.workingProperties.length;
		}
		if(skipDependencies === undefined) {
			skipDependencies = false;
		}
		var dependencyProperties = [];
		for(var i = 0; i < untilIndex; i++) {
			var propDependencies = this.definition.getProperty(this.workingProperties[i]).getTypesWithDependency(this.previousObject, this.object, this);
			if(propDependencies.length === 0 && this.definition.getProperty(this.workingProperties[i]).hasType(UniqueType) === false) {
				this.definition.getProperty(this.workingProperties[i]).reset();
				this.object[this.workingProperties[i]] = this.definition.getProperty(this.workingProperties[i]).next(this.previousObject, this.object, this);
			} else if(propDependencies.length > 0 && skipDependencies === false) {
				var propDefinition = new PropertyDefinition(this.workingProperties[i], propDependencies.map(function(pDependency) { return TestObject.copyObject(pDependency); }));
				propDefinition.resetAll();
				dependencyProperties.push(propDefinition);
			}
		}
		if(skipDependencies === false) {
			for(var i = 0, len = dependencyProperties.length; i < len; i++) {
				this.object[dependencyProperties[i].name] = dependencyProperties[i].nextDependency(this.previousObject, this.object, this);
			}
			this.dependencies = [{ previousObject: TestObject.copyObject(this.previousObject), dependencies: new ObjectDefinition(dependencyProperties) }];
		}
		this.setUID();
	},

	setUID: function(pObj) {
		if(pObj === undefined) {
			pObj = this.object;
		}

		for(var i = 0, len = this.workingProperties.length; i < len; i++) {
			if(this.definition.getProperty(this.workingProperties[i]).hasType(UniqueType)) {
				pObj[this.workingProperties[i]] = this.generateUID();
			} else if(this.definition.getProperty(this.workingProperties[i]).current() instanceof ObjectType && typeof pObj[this.workingProperties[i]] === "object") {
				if(this.definition.getProperty(this.workingProperties[i]).current().testObject !== undefined) {
					this.definition.getProperty(this.workingProperties[i]).current().testObject.setUID(pObj[this.workingProperties[i]]);
				}
			} else if(this.definition.getProperty(this.workingProperties[i]).current() instanceof ArrayType && Array.isArray(pObj[this.workingProperties[i]])) {
				if(this.definition.getProperty(this.workingProperties[i]).current().testObject !== undefined) {
					for(var j = 0, jLen = pObj[this.workingProperties[i]].length; j < jLen; j++) {
						this.definition.getProperty(this.workingProperties[i]).current().testObject.setUID(pObj[this.workingProperties[i]][j]);
					}
				}
			}
		}
	},

	getParents: function() {
		var parents = [];
		var parentTestObject = this.parent;
		while(parentTestObject != null) {
			parents.unshift(parentTestObject)
			parentTestObject = parentTestObject.parent.object;
		}
		return parents;
	},

	_getDependencies: function(pDefinition, pPreviousObject, pCurrentObject, pTestObject) {
		if(pPreviousObject === undefined) {
			pPreviousObject = this.previousObject;
		}
		if(pCurrentObject === undefined) {
			pCurrentObject = this.object;
		}
		if(pTestObject === undefined) {
			pTestObject = this;
		}
		var name = pDefinition.current().name;
		var properties = [];
		for(var i = 0, len = pDefinition.properties.length; i < len; i++) {
			var dependencies = pDefinition.properties[i].getTypesWithDependency(pPreviousObject, pCurrentObject, pTestObject);
			if(dependencies.length > 0) {
				var propDefinition = new PropertyDefinition(pDefinition.properties[i].name, dependencies.map(function(pDependency) { return TestObject.copyObject(pDependency); }));
				propDefinition.resetAll();
				properties.push(propDefinition);
			}
		}
		if(properties.length > 0) {
			return new ObjectDefinition(properties, this.allCombinations);
		} else {
			return null;
		}
	},

	s4: function() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	},

	generateUID: function() {
		do {
			var uid = this.s4() + this.s4() + '-' + this.s4() + '-' + this.s4() + '-' + this.s4() + '-' + this.s4() + this.s4() + this.s4();
		}
		while(uIDs.indexOf(uid) !== -1);
		uIDs.push(uid)
		return uid;
	},

	copy: function() {
		return new TestObject(this.definition.copy(false), this.allCombinations, this.depth, (this.parent !== undefined?this.parent.copy():undefined));
	}
}

module.exports = TestObject;