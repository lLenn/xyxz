const RuleExpectation = require("./RuleExpectation.js");

ObjectRuleExpectation = function(pExpectations, pValue, pCondition) {
	this.ruleExpectation(pExpectations, pValue, pCondition);
	this.type = "object_expectation";
}

ObjectRuleExpectation.prototype = Object.assign({}, RuleExpectation.prototype, {
	constructor: ObjectRuleExpectation,
	ruleExpectation: RuleExpectation,

	copy: function() {
		return new ObjectExpectation(this.expectations, this.value.copy(), this.condition);
	}
});

module.exports = ObjectRuleExpectation;