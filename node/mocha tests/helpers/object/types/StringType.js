const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");
const StringDictionary = require("../dictionaries/StringDictionary.js");

/**
 * @name nixpsit.unit.StringType
 * @description Creates a type object for string.
 * Will return a string from the dictionary.
 * @param {boolean} pRequired - indicates if the property is required in the object
 * @param {boolean} [pEmpty=false] - indicates if the string can be empty
 * @param {Filter[]} [pFilter=[new Filter]] - an array of filters to filter out certain values
 * @param {StringDictionary} [pDictionary=new StringDictionary] - the dictionary from which to retrieve the values
 */
StringType = function(pRequired, pEmpty, pFilter, pDictionary) {
	this.typeDefinition(pRequired, pFilter);

	if(pEmpty === undefined) {
		pEmpty = false;
	}

	if(pDictionary === undefined) {
		pDictionary = new StringDictionary();
	}

	this.dictionary = pDictionary;
	this.empty = pEmpty
}

StringType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: StringType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		switch(pPointer) {
			case 0: if(this.empty === true) { return ""; } else { this.pointer++; };
			case 1: return "a";
			case 2: return this.dictionary.getVariable();
			case 3: return this.dictionary.getVariable() + " " + this.dictionary.getVariable();
			default: return new EndOfType();
		}
	},

	copy: function() {
		return new StringType(this.required, this.empty, this.filters.map(function(pFilter) { return pFilter.copy(); }), this.dictionary.copy());
	}
});

module.exports = StringType;