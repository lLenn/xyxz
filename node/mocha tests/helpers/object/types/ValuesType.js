const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");

/**
 * @name nixpsit.unit.ValuesType
 * @description Creates a type object for predetermined values.
 * This wil set the property of the object based on the callback provided.
 * If the callback is a function then the object that's being created will be passed as an argument.
 * If the callback is an array it iterate each value in the array.
 * @param {boolean} pRequired - indicates if the property is required in the object
 * @param {valuesCallback|Array} pCallback - see description
 */
/**
 * @callback valuesCallback
 * @param {object} pObject - the object currently being build by TestObject
 * @param {object[]} pParents - an array of the parents of the object; top-down
 */
ValuesType = function(pRequired, pCallback) {
	this.typeDefinition(pRequired);

	this.callback = pCallback;
}

ValuesType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: ValuesType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		if(typeof this.callback === "function") {
			if(this.withMeta === undefined) {
				if(pPointer === 3) {
					return new EndOfType();
				}
				var metaData = { pointer: pPointer };
			} else {
				var metaData = this.withMeta;
				metaData.pointer = pPointer;
			}
			var value = this.callback.call(null, TestObject.copyObject(pTestObject.object), metaData);
			if(pPointer !== metaData.pointer) {
				this.withMeta = metaData;
				this.pointer = metaData.pointer + 2;
			}
			return value;
		}
		if(this.callback.length === pPointer) {
			return new EndOfType();
		} else {
			var value = this.callback[pPointer];
			if(typeof value === "string" && value.indexOf("__") === 0) {
				switch(value.substring(2)) {
					case "now": return Date.now();
					case "nowISO": return new Date().toISOString();
				}
			}
			return value;
		}
	},

	copy: function() {
		return new ValuesType(this.required, this.callback);
	}
});

module.exports = ValuesType;