const Filter = require("./Filter.js");

NumberFilter = function(pComparison, pValue) {
	this.filter(pComparison, pValue);
}

NumberFilter.prototype = Object.assign({}, Filter.prototype, {
	constructor: NumberFilter,
	filter: Filter,

	compare: function(pTestValue) {
		switch(this.comparison) {
			case "gt": return pTestValue > this.value;
			case "gte": return pTestValue >= this.value;
			case "lt": return pTestValue < this.value;
			case "lte": return pTestValue <= this.value;
			case "int": return Number.isInteger(pTestValue);
			case "float": return !Number.isInteger(pTestValue);
			default: return true;
		}
	},

	copy: function() {
		return new NumberFilter(this.comparison, this.value);
	}
});

module.exports = NumberFilter;