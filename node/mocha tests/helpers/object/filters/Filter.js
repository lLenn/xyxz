Filter = function(pComparison, pValue) {
	if(typeof this.copy !== "function") {
		throw new Error("Classes of the TypeDefinition interface should implement the copy method!");
	}

	this.comparison = pComparison;
	this.value = pValue;
}

Filter.prototype = {
	constructor: Filter,

	compare: function(pTestValue) {
		return true;
	},

	copy: function() {
		return new Filter(this.comparison, this.value);
	}
}

module.exports = Filter;