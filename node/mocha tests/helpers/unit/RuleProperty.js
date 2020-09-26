/*
 * @description: constructor
 * @param {string} The key on which the rule applies
 * @param {string} The type of key: one of the RuleProperty constants; see below
 * @param {boolean} Whether the property should be iterated
 */
RuleProperty = function(pKey, pType, pIterate, pSubKey) {
	if(pType === undefined) {
		pType = RuleProperty.CSS_SELECTOR;
	}
	this.key = pKey;
	this.subKey = pSubKey;
	this.type = pType;
	this.iterate = pIterate;
}

RuleProperty.ALL = "All";
RuleProperty.CSS_SELECTOR = "CSS_SELECTOR";
RuleProperty.WIDGET_VARIABLE = "WIDGET_VARIABLE";
RuleProperty.WIDGET_OPTION = "WIDGET_OPTION";
RuleProperty.METHOD_ARGUMENT = "METHOD_ARGUMENT";
RuleProperty.ARRAY_ITEM = "ARRAY_ITEM";
RuleProperty.OBJECT_PROPERTY = "OBJECT_PROPERTY";

RuleProperty.prototype = {
	constructor: RuleProperty,
	copy: function() {
		return new RuleProperty(this.key, this.type, this.iterate, this.subKey);
	}
}

module.exports = RuleProperty;