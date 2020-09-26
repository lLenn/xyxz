const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");
const TestObject = require("../TestObject.js");

/**
 * @name nixpsit.unit.ParentType
 * @description Creates a type object for parent object.
 * Will return the result from the callback.
 * @param {boolean} pRequired - indicates if the property is required in the object
 * @param {parentCallback} pCallback
 * @param {dependecyCallback} pCallback
 */
/**
 * @callback parentCallback
 * @param {object} pObject - the object currently being build by TestObject
 * @param {object[]} pParents - an array of the parents of the object; top-down
 * @param {{ pointer:number }} pMetaData - data which can be used to store function variables from call to call
 * @param {module:nixpsit.unit#TestObject} pTestObject - the test object
 */
/**
 * @callback dependecyCallback
 * @param {object} pPreviousObject - the object previously built by TestObject
 * @param {object} pCurrentObject - the object currently being build by TestObject
 * @param {object[]} pParents - an array of the parents of the object; top-down
 */
ParentType = function(pRequired, pCallback, pDependecy) {
	this.typeDefinition(pRequired, undefined, pDependecy);

	this.callback = pCallback;
	this.withMeta;
}

ParentType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: ParentType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		if(pPointer === 0 || this.withMeta !== undefined) {
			var parents = [];
			var parentTestObject = pTestObject.parent;
			while(parentTestObject != null) {
				parents.push(parentTestObject)
				parentTestObject = parentTestObject.parent;
			}
			if(this.withMeta === undefined) {
				var metaData = { pointer: pPointer };
			} else {
				var metaData = this.withMeta;
				metaData.pointer = pPointer;
			}
			var value = this.callback.call(null, pTestObject.object, parents, metaData, pTestObject);
			if(pPointer !== metaData.pointer) {
				this.withMeta = metaData;
				this.pointer = metaData.pointer + 2;
			}
			return value;
		} else {
			return new EndOfType();
		}
	},

	copy: function() {
		return new ParentType(this.required, this.callback, this.dependency);
	}
});

module.exports = ParentType;