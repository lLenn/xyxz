const ObjectRuleExpectation = require("./expectations/ObjectRuleExpectation.js");
const RuleExpectation = require("./expectations/RuleExpectation.js");
const RuleProperty = require("./RuleProperty.js");

Rule = function(pRuleProperty, pRuleExpectations, pParent) {
	this.property = pRuleProperty;
	this.expectations = pRuleExpectations;
	this.parent = pParent;
}

Rule.prototype = {
	constructor: Rule,

	onAll: function() {
		this.property = new RuleProperty("", RuleProperty.ALL);

		return this;
	},

	iterateOverCSS: function(pCSS) {
		this.property = new RuleProperty(pCSS, RuleProperty.CSS_SELECTOR, true);

		return this;
	},

	onCSS: function(pCSS) {
		this.property = new RuleProperty(pCSS, RuleProperty.CSS_SELECTOR);

		return this;
	},

	onOption: function(pOption) {
		this.property = new RuleProperty(pOption, RuleProperty.WIDGET_OPTION);

		return this;
	},

	onProperty: function(pOption) {
		this.property = new RuleProperty(pOption, RuleProperty.OBJECT_PROPERTY);

		return this;
	},

	onVariable: function(pVariable) {
		this.property = new RuleProperty(pVariable, RuleProperty.WIDGET_VARIABLE);

		return this;
	},

	notExpect: function() {
		var expectation = this.expect();
		expectation.not = true;
		return expectation;
	},

	expect: function(pCustomTest) {
		if(this.expectations === undefined) {
			this.expectations = [];
		}
		if(this.property.type === RuleProperty.CSS_SELECTOR) {
			var expectation = new JQueryRuleExpectation();
		} else if(this.property.type === RuleProperty.WIDGET_OPTION || this.property.type === RuleProperty.WIDGET_VARIABLE || this.property.type === RuleProperty.ObjectProperty) {
			var expectation = new ObjectRuleExpectation();
		} else {
			var expectation = new RuleExpectation();
		}
		expectation.parent = this;
		expectation.custom(pCustomTest);
		this.expectations.push(expectation);
		return expectation;
	},

	//Parent functions
	addRule: function() {
		if(this.parent !== undefined) {
			return this.parent.addRule();
		} else {
			throw new Error("RuleExpectation: Parent is not defined!");
		}
	},

	createMethodRules: function() {
		if(this.parent !== undefined) {
			return this.parent.createMethodRules();
		} else {
			throw new Error("RuleExpectation: Parent is not defined!");
		}
	},

	copy: function(pParent) {
		return new Rule(this.property.copy(), this.expectations.map(function(pExpectation) { return pExpectation.copy() }), pParent);
	}
}

RuleList = function(pRules, pParent) {
	if(pRules === undefined) {
		pRules = [];
	}

	this.rules = pRules;
	this.parent = pParent;
}

RuleList.prototype = {
	constructor: RuleList,
	iterate: function(callback) {
		for(var i = 0, len = this.rules.length; i < len; i++) {
			for(var j = 0, jLen = this.rules[i].expectations.length; j < jLen; j++) {
				callback.call(null, this.rules[i].property, this.rules[i].expectations[j]);
			}
		}
	},

	addRule: function() {
		var rule = new Rule();
		rule.parent = this;
		this.rules.push(rule);
		return rule;
	},

	//Parent functions
	createMethodRules: function() {
		if(this.parent !== undefined) {
			return this.parent.createMethodRules();
		} else {
			throw new Error("RulesList: Parent is not defined!");
		}
	},

	copy: function() {
		var copy = new RuleList();
		copy.rules = this.rules.map(function(pRule) { return pRule.copy(copy); });
		return copy;
	}
}

module.exports = { Rule, RuleList };