const TypeDefinition = require("./TypeDefinition.js");
const EndOfType = require("./EndOfType.js");

DateType = function(pRequired, pFilters, pFormat) {
	if(pFormat === undefined) {
		pFormat = "all";
	}
	this.typeDefinition(pRequired, pFilters);

	this.format = pFormat;
}

DateType.prototype = Object.assign({}, TypeDefinition.prototype, {
	constructor: DateType,
	typeDefinition: TypeDefinition,

	getValue: function(pPointer, pTestObject) {
		var date = new Date();
		switch(this.format) {
			case "all":
				switch(pPointer) {
					case 0: return date.valueOf();
					case 1: return date.toISOString();
					case 2: date.setYear(1000); return date.valueOf();
					case 3: date.setYear(1000); return date.toISOString();
					case 4: date.setYear(2000); return date.valueOf();
					case 5: date.setYear(2000); return date.toISOString();
					case 6: date.setYear(3000); return date.valueOf();
					case 7: date.setYear(3000); return date.toISOString();
					default: return new EndOfType();
				}
			case "unix_epoch":
				switch(pPointer) {
					case 0: return date.valueOf();
					case 1: date.setYear(1000); return date.valueOf();
					case 2: date.setYear(2000); return date.valueOf();
					case 3: date.setYear(3000); return date.valueOf();
					default: return new EndOfType();
				}
			case "ISO":
				switch(pPointer) {
					case 0: return date.toISOString();
					case 1: date.setYear(1000); return date.toISOString();
					case 2: date.setYear(2000); return date.toISOString();
					case 3: date.setYear(3000); return date.toISOString();
					default: return new EndOfType();
				}
			default: return new EndOfType();
		}
	},

	copy: function() {
		return new DateType(this.required, this.filters.map(function(pFilter) { return pFilter.copy(); }), this.format);
	}
});

module.exports = DateType;