const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");
const NumberFilter = require("../filters/NumberFilter.js");

/**
 * @name nixpsit.unit.NumberType
 * @description Creates a type object for number.
 * Will return a number.
 * @param {boolean} pRequired - indicates if the property is required in the object
 * @param {boolean} [pNaN=false] - indicates if the value can be NaN
 * @param {Filter[]} [pFilter=[new Filter]] - an array of filters to filter out certain values, the default will filter nothing
 */
NumberType = function(pRequired, pNaN, pFilters) {
	if(pFilters === undefined) {
		pFilters = [new NumberFilter("int")];
	}
	this.typeDefinition(pRequired, pFilters);

	this.nan = pNaN;
}

NumberType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: NumberType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		switch(pPointer) {
			case 0: if(this.nan === true) { return NaN; } else { this.pointer++; };
			case 1: return -10;
			case 2: return 0;
			case 3: return 10;
			case 4: return 0.12;
			case 5: return 50.98348633225687431313;
			default: return new EndOfType();
		}
	},

	copy: function() {
		return new NumberType(this.required, this.nan, this.filters.map(function(pFilter) { return pFilter.copy(); }));
	}
});

module.exports = NumberType;