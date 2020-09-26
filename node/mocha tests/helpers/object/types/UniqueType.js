UniqueType = function() {

}

UniqueType.prototype = {
	constructor: UniqueType,

	hasDependency: function() {
		return false;
	},

	copy: function() {
		return new UniqueType();
	}
}

module.exports = UniqueType;