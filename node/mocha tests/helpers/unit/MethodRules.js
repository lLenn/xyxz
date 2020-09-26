const { RuleList } = require("./Rule.js");

//TODO: Add error validation
MethodRules = function(pName, pDefinition, pRules, pParent) {
	if(pRules === undefined) {
		pRules = new RuleList();
	}
	pRules.parent = this;

	this.name = pName;
	this.definition = pDefinition;
	this.rules = pRules;
	this.parent = pParent;
}

MethodRules.prototype = {
	constructor: MethodRules,

	addRule: function() {
		return this.rules.addRule();
	},

	createMethodRules: function() {
		if(this.parent !== undefined) {
			return this.parent.createMethodRules();
		} else {
			throw new Error("MethodRules: Parent is not defined!");
		}
	},

	copy: function() {
		return new MethodRules(this.name, this.definition.copy(), this.rules.copy(), this.delays);
	}
}

MethodRulesList = function(pRules) {
	if(pRules === undefined) {
		pRules = [];
	}

	this.rules = pRules;
}

MethodRulesList.prototype = {
	constructor: MethodRulesList,

	createMethodRules: function(pName, pDefinition, pRules) {
		var rule = new MethodRules(pName, pDefinition, pRules);
		rule.parent = this;
		this.rules.push(rule);
		return rule;
	},

	copy: function() {
		var copy = new MethodRulesList();
		copy.rules = this.rules.map(function(pRule) { return pRule.copy(copy); });
		return copy;
	}
}

module.exports = { MethodRules, MethodRulesList };