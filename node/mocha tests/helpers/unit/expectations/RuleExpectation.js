const RuleProperty = require("../RuleProperty.js");

const ALLOWED_RULE_EXPECTATIONS = ["text", "attr", "className", "component", "custom", "test", "contains", "empty", "length", "canBeUndefined", "hasToBe", "visible"];

RuleExpectation = function(pExpectations, pValue, pCondition, pParent) {
	if(pExpectations === undefined) {
		pExpectations = {};
	}
	this.type = "plain_expectation";
	this.expectations = pExpectations;
	this.not = false;
	this.assertion = "equal";
	this.value = pValue;
	this.manipulator;
	this.condition = pCondition;
	this.tested = {};
	this.breakpoint = false;
	this.customTest = false;

	this.parent = pParent

	this._validate();
}

RuleExpectation.prototype = {
	constructor: RuleExpectation,

	custom: function(pCustomTest) {
		if(pCustomTest === undefined) {
			this.customTest = false;
			return;
		}

		if(typeof pCustomTest !== "function") {
			throw new Error("RuleExpectation: custom expects a function as first argument!");
		}
		this.customTest = pCustomTest;

		return this;
	},

	callback: function(pCallback) {
		this.expectations.callback = pCallback;

		return this;
	},

	it: function() {
		this.expectations.it = true;

		return this;
	},

	lengthOf: function() {
		this.expectations.length = true;

		return this;
	},

	empty: function() {
		this.expectations.empty = true;

		return this;
	},

	equalTo: function(pValue) {
		this.assertion = "equal";
		this.value = pValue;

		return this;
	},

	includesOption: function(pOption, pOptionKey) {
		this.assertion = "include";
		this.value = new RuleProperty(pOption, RuleProperty.WIDGET_OPTION, false, pOptionKey);

		return this;
	},

	equalToOption: function(pOption, pOptionKey) {
		this.assertion = "equal";
		this.value = new RuleProperty(pOption, RuleProperty.WIDGET_OPTION, false, pOptionKey);

		return this;
	},

	equalToVariable: function(pVariable, pVariableKey) {
		this.assertion = "equal";
		this.value = new RuleProperty(pVariable, RuleProperty.WIDGET_VARIABLE, false, pVariableKey);

		return this;
	},

	manipulateWith: function(pManipulator) {
		if(typeof pManipulator !== "function") {
			throw new Error("RuleExpectation: manipulateWith expects a function as first argument!");
		}
		this.manipulator = pManipulator;

		return this;
	},

	equalToArgument: function(pIndex) {
		this.assertion = "equal";
		this.value = new RuleProperty(pIndex, RuleProperty.METHOD_ARGUMENT);

		return this;
	},

	includesArgument: function(pIndex) {
		this.assertion = "include";
		this.value = new RuleProperty(pIndex, RuleProperty.METHOD_ARGUMENT);

		return this;
	},

	onConditionThat: function(pCondition) {
		this.condition = pCondition;

		return this;
	},

	addBreak: function() {
		this.breakpoint = true;

		return this;
	},

	testValues: function(pActual, pExpectation /*, pObject, pArgs*/) {
		if(typeof this.testExpectations === "function") {
			this.testExpectations(pActual, pExpectation);
		}
		if(this.expectations.length !== undefined) {
			this.tested.length = true;
			if(typeof this.expectations.length === "number") {
				expect(pActual).to.have.length(this.expectations.length);
			}
		}
		if(this.expectations.empty !== undefined) {
			this.tested.not = true;
			if((this.not === true && pExpectation === false) || (this.not === false && pExpectation === true)) {
				expect(pActual).to.be.empty;
			} else {
				expect(pActual).to.not.be.empty;
			}
		}
		if(this.expectations.it === true) {
			this.tested.it = true;
			this.testExpectation(pActual, pExpectation);
		}
		if(this.expectations.callback !== undefined) {
			this.tested.callback = true;
			if(typeof value === "object") {
				expect((function() { return this.expectation.callback.call(null, object, element, args); })()).to.deep.equal(value);
			} else {
				expect((function() { return this.expectation.callback.call(null, object, element, args); })()).to.equal(value);
			}
		}
	},

	testExpectation: function(pActual, pExpected) {
		if(typeof pExpected === "object") {
			if(this.assertion === "include") {
				if(this.not === false) {
					expect(pActual).to.deep.include(pExpected);
				} else {
					expect(pActual).to.not.deep.include(pExpected);
				}
			} else {
				if(this.not === false) {
					expect(pActual).to.deep.equal(pExpected);
				} else {
					expect(pActual).to.not.deep.equal(pExpected);
				}
			}

		} else {
			if(this.assertion === "include") {
				if(this.not === false) {
					expect(pActual).to.include(pExpected);
				} else {
					expect(pActual).to.not.include(pExpected);
				}
			} else {
				if(this.not === false) {
					expect(pActual).to.equal(pExpected);
				} else {
					expect(pActual).to.not.equal(pExpected);
				}
			}
		}
	},

	_validate: function() {
		for(var prop in this.expectations) {
			if(ALLOWED_RULE_EXPECTATIONS.indexOf(prop) === -1) {
				throw new Error("RuleExpectation: Property '" + prop + "' in expectations isn't supported!");
			}
		}
	},

	//Parent functions
	expect: function() {
		if(this.parent !== undefined) {
			return this.parent.expect();
		} else {
			throw new Error("RuleExpectation: Parent is not defined!");
		}
	},

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
		return new RuleExpectation(this.expectations, this.value.copy(), pParent);
	}
}

module.exports = RuleExpectation;