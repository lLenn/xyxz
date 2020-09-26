const expectations = {
	JQueryRuleExpectations: require("./expectations/JQueryRuleExpectations.js"),
	ObjectRuleExpectations: require("./expectations/ObjectRuleExpectations.js"),
	RuleExpectations: require("./expectations/RuleExpectations.js"),
}
const ClassTestCase = require("./ClassTestCase.js");
const MethodRules = require("./MethodRules.js");
const Rule = require("./Rule.js");
const RuleProperty = require("./RuleProperty.js");
const Widget = require("./Widget.js");
const WidgetTestCase = require("./WidgetTestCase.js");

module.exports = { ClassTestCase, MethodRules, Rule, RuleProperty, Widget, WidgetTestCase, types, dictionaries, filters };