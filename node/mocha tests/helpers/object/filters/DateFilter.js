const Filter = require("./Filter.js");

DateFilter = function(pComparison, pValue) {
	this.filter(pComparison, pValue);
}

DateFilter.prototype = Object.assign({}, Filter.prototype, {
	constructor: DateFilter,
	filter: Filter,

	compare: function(pTestValue) {
		switch(this.comparison) {
			case "gt": return Date.parse(new Date(pTestValue)) > Date.parse(new Date(this.value));
			case "gte": return Date.parse(new Date(pTestValue)) >= Date.parse(new Date(this.value));
			case "lt": return Date.parse(new Date(pTestValue)) < Date.parse(new Date(this.value));
			case "lte": return Date.parse(new Date(pTestValue)) <= Date.parse(new Date(this.value));
			default: return true;
		}
	},

	copy: function() {
		return new DateFilter(this.comparison, this.value);
	}
});

module.exports = DateFilter;