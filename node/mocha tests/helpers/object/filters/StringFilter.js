const Filter = require("./Filter.js");

StringFilter = function(pComparison, pValue) {
	this.filter(pComparison, pValue);
}

StringFilter.prototype = Object.assign({}, Filter.prototype, {
	constructor: StringFilter,
	filter: Filter,

	compare: function(pTestValue) {
		switch(this.comparison) {
			//Word operators
			case "wgt": return pTestValue.split(" ").length > this.value;
			case "wgte": return pTestValue.split(" ").length >= this.value;
			case "wlt": return pTestValue.split(" ").length < this.value;
			case "wlte": return pTestValue.split(" ").length <= this.value;
			case "weq": return pTestValue.split(" ").length === this.value;
			//Length operators
			case "lgt": return pTestValue.length > this.value;
			case "lgte": return pTestValue.length >= this.value;
			case "llt": return pTestValue.length < this.value;
			case "llte": return pTestValue.length <= this.value;
			case "leq": return pTestValue.length === this.value;
			default: return true;
		}
	},

	copy: function() {
		return new StringFilter(this.comparison, this.value);
	}
});

module.exports = StringFilter;