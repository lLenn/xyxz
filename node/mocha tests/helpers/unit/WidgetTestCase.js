const ClassTestCase = require("./ClassTestCase.js");
const { ObjectDefinition = require("../object/ObjectDefinition.js");
const PropertyDefinition = require("../object/PropertyDefinition.js");
const ValuesType = require("../object/types/ValuesType.js");

$.widget("base.BaseWidget", $.Widget, {});
const BASE_WIDGET = $("<div>").BaseWidget().data("base-BaseWidget");
const ALLOWED_VALIDATE_OPTIONS_TYPES = ["object", "string", "number", "number_gt0", "number_eq0", "number_lt0", "function", "array", "boolean"];
const WIDGET_CONSTRUCTOR_DEFINITION = ObjectDefinition.createFrom([
	new PropertyDefinition("0", [new ValuesType(true, ["<div>"])])
])

WidgetTestCase = function(elementTag, component, rules, optionsDefinition, methodDefinitions) {
	this.classTestCase($, WIDGET_CONSTRUCTOR_DEFINITION, rules, [])

	this.elementTag = elementTag;
	var componentNamespace = component.split(".");
	if($[componentNamespace[0]] === undefined || $.isFunction($[componentNamespace[0]][componentNamespace[1]]) === false) {
		throw new Error("Component " + component + " not found!");
	}
	this.componentNamespace = componentNamespace[0];
	this.componentName = componentNamespace[1];
	this.rules = TestObject.copyObject(rules);

	this.optionsDefinition = TestObject.copyObject(optionsDefinition);
	this.methodDefinitions = TestObject.copyObject(methodDefinitions);
	this.breakpoints = [];

	this.element;
	this.parent;
}

WidgetTestCase.prototype = {
	constructor: WidgetTestCase,
	classTestCase: ClassTestCase,

	init: function() {
		this.element = $("<" + this.elementTag + ">");
		if(this.parent !== undefined) {
			this.parent.empty();
			this.element.appendTo(this.parent);
		}
	},

	create: function(options) {
		this.element[this.componentName](options);
	},

	createRandom: function() {
		var testObject = new TestObject(this.optionsDefinition);
		this.init();
		this.create(testObject.createRandomObject());
	},

	setOption: function(options) {
		this.element[this.componentName]("option", options);
	},

	getOption: function() {
		return this.element[this.componentName]("option");
	},

	setParent: function(parent) {
		this.parent = parent;
	},

	expectElementCreateToPassAll: function(allCombinations) {
		var that = this;
		return $.Deferred(function(pDefer){
			var testObject = new TestObject(that.optionsDefinition, allCombinations);
			that._testNext(pDefer, testObject, "create");
		});
	},

	expectElementRedrawToPassAll: function(allCombinations) {
		var that = this;
		return $.Deferred(function(pDefer){
			var testObject = new TestObject(that.optionsDefinition, allCombinations);
			that._testNext(pDefer, testObject, "option");
		});
	},

	/**
	 * @description: applies this.rules onto the element depending on the object
	 * @param {object} The object
	 * @param {jQuery} The element
	 */
	expectElementToAbideByRules: function(object, rules, args) {
		if(object === undefined) {
			object = this.element[this.componentName]("option");
		}
		if(rules === undefined) {
			rules = this.rules;
		}
		if(args === undefined) {
			args = [];
		}
		var that = this;
		var element = this.element;
		var optionsObject = TestObject.copyObject(object);
		var propObject = {};
		var widgetInstance = element.data(that.componentNamespace + "-" + that.componentName);
		for(var prop in widgetInstance) {
			if(prop === "options" || (prop !== "_super" && prop !== "_superApply" && typeof widgetInstance[prop] !== "function" && BASE_WIDGET[prop] === undefined)) {
				propObject[prop] = TestObject.copyObject(widgetInstance[prop]);
			}
		}

		rules.iterate(function(prop, expectation) {
			try {
				for(var i = 0, len = that.breakpoints.length; i < len; i++) {
					if(that.breakpoints[i].call(null, optionsObject, propObject, args, that.element) === true) {
						debugger;
					}
				}

				if(expectation.breakpoint !== undefined && expectation.breakpoint === true) {
					debugger;
				}

				if(expectation.condition !== undefined) {
					if(expectation.condition.call(null, optionsObject, propObject, args, that.element) === false) {
						return;
					}
				}
				if(prop.type !== RuleProperty.ALL) {
					var actualValue = that._getValue(prop, optionsObject, propObject, args, element);
					var expectedValue = that._getValue(expectation.value, optionsObject, propObject, args, element);
					if(prop.iterate === true) {
						var compare = function(pActualValue, pExpectedValue) {
							for(var i = 0, len = pActualValue.length; i < len; i++) {
								if($.isArray(pExpectedValue) === true) {
									var testExpectedValue = TestObject.getValue(pExpectedValue[i], expectation.value.subKey);
								} else {
									var testExpectedValue = TestObject.getValue(pExpectedValue, expectation.value.subKey);
								}
								if(prop.type === RuleProperty.CSS_SELECTOR) {
									var testActualValue = $(pActualValue[i]);
								} else {
									var testActualValue = TestObject.getValue(pActualValue[i], prop.subKey);
								}
								if(expectation.manipulator !== undefined) {
									testExpectedValue = expectation.manipulator(testExpectedValue);
								}
								expectation.testValues(testActualValue, testExpectedValue, object, args);
							}
						}
						var travelValues = function(pActualValue, pExpectedValue) {
							if(Array.isArray(pExpectedValue) === false || Array.isArray(pExpectedValue) === false) {
								throw new Error("Expected actual and expected values to be arrays!");
							}
							if(pExpectedValue.length !== pActualValue.length) {
								throw new Error("Length of the actual (" + pActualValue.length + ") and expected (" + pExpectedValue.length + ") value to iterate don't match!");
							}
							if(pActualValue instanceof $) {
								compare(pActualValue, pExpectedValue)
							} else {
								for(var i = 0, len = pActualValue.length; i < len; i++) {
									travelValues(pActualValue[i], pExpectedValue[i]);
								}
							}
						}
						travelValues(actualValue, expectedValue);
					} else {
						expectedValue = TestObject.getValue(expectedValue, expectation.value.subKey)
						if(expectation.manipulator !== undefined) {
							expectedValue = expectation.manipulator(expectedValue);
						}
						expectation.testValues(TestObject.getValue(actualValue, prop.subKey), expectedValue, object, args);
					}
				} else if(prop.type === RuleProperty.ALL) {
					if(typeof expectation.customTest !== "function") {
						throw new Error("The expectation property with type 'ALL' should have a custom expectation!");
					}
				} else {
					throw new Error("The expectation property with type '" + prop.type + "' isn't supported!")
				}

				if(typeof expectation.customTest === "function") {
					expectation["tested"].customTest = true;
					expectation.customTest.call(null, optionsObject, propObject, element, args);
				}
			} catch(e) {
				e.prop = TestObject.copyObject(prop, 0, [], true);
				e.expectation = TestObject.copyObject(expectation, 0, [], true);
				e.widget = {
					componentNamespace: that.componentNamespace,
					componentName: that.componentName
				}
				e.html = that.element[0].outerHTML;
				throw e;
			}
		});
	},

	_testState: function(pDefer, pState, pAction) {
		try {
			if(pAction === "create") {
				this.init();
			} else if(pAction === "option") {
				var prevState = TestObject.copyObject(this.getOption());
			}
			var that = this;
			var hasRendered = setTimeout(function() {
				var error = new Error(that.componentName + ": no rendered event was triggered!");
				error.options = pState;
				if(pAction === "option") {
					error.prevOptions = prevState;
				}
				pDefer.reject(error);
			}, 10000);
			this.element.one("rendered", function(event) {
				clearTimeout(hasRendered);
				var toFillState = TestObject.copyObject(pState);
				$.when(TestObject.fillObject(toFillState)).then(function() {
					try {
						expect(event.target).to.equal(that.element[0]);
						that.expectElementToAbideByRules(toFillState);
						that.testStateNext = true;
					} catch(e) {
						e.options = TestObject.copyObject(toFillState, 0, [], true);
						if(pAction === "option") {
							e.prevOptions = TestObject.copyObject(prevState, 0, [], true);
						}
						pDefer.reject(e);
					}
				}, pDefer.reject, pDefer.progress);

				var checkSecondRender = function() {
					//console.warn(that.componentName + ": rendered event was triggered a second time withing 30ms!")
				}
				var clearCheckSecondRender = setTimeout(function() {
					that.element.off("rendered", checkSecondRender);
				}, 30);
				that.element.one("rendered", checkSecondRender);
			});
			switch(pAction) {
				case "create": that.create(pState); break;
				case "option": that.setOption(TestObject.copyObject(pState)); break;
			}
		} catch(e) {
			e.options = TestObject.copyObject(pState, 0, [], true);
			pDefer.reject(e);
		}
	},

	_testMethod: function(pDefer, pState) {
		try {
			this.init();
			this.create(pState)
			for(var i = 0, len = this.methodDefinitions.rules.length; i < len; i++) {
				try {
					var prop = this.methodDefinitions.rules[i].name;
					var definition = this.methodDefinitions.rules[i].definition;
					var rules = this.methodDefinitions.rules[i].rules;
					if(definition.properties.length > 0) {
						var methodTestObj = new TestObject(definition, false, 0, this);
						var state;
						do {
							state = methodTestObj.getCurrentState();
							var args = [];
							for(var index in state) {
								args[parseInt(index)] = state[index];
							}
							var value = this.element[this.componentName].apply(this.element, [prop].concat(args));
							if(typeof definition.returnValue === "object") {
								expect(value).to.deep.equal(definition.returnValue);
							} else if(definition.returnValue !== undefined) {
								expect(value).to.equal(definition.returnValue);
							}
							this.expectElementToAbideByRules(undefined, rules, args);
						} while(methodTestObj.setNextState() === true);
					} else {
						element[this.componentName].apply(element, [prop]);
						this.expectElementToAbideByRules(undefined, rules, args);
					}
					this.expectAllRulesTested(rules)
				} catch(e) {
					e.method = TestObject.copyObject(prop, 0, [], true);
					e.args = TestObject.copyObject(state, 0, [], true);
					e.options = TestObject.copyObject(pState, 0, [], true);
					pDefer.reject(e);
				};
			}
			this.testStateNext = true;
		} catch(e) {
			e.options = TestObject.copyObject(pState, 0, [], true);
			pDefer.reject(e);
		}
	},

	_testNext: function(pDefer, pTestObject, pAction) {
		if(pAction === "option") {
			var originalState = TestObject.copyObject(pTestObject.getCurrentState());
			var originalTested = false;
			var optionsTestObject = TestObject.copyObject(pTestObject);
			var initOptionsTestObject = true;
			this.init();
			this.create(originalState);
		}
		this.testStateNext = false;
		var that = this;
		var nextInterval = setInterval(function() {
			if(that.testStateNext === true) {
				that.testStateNext = false;
				if(pAction === "option" && (initOptionsTestObject === true || optionsTestObject.setNextState() === true)) {
					if(initOptionsTestObject === true) {
						optionsTestObject.definition.resetAll();
						initOptionsTestObject = false;
					}
					that.init();
					that.create(pTestObject.getCurrentState());
					that._testState(pDefer, TestObject.copyObject(optionsTestObject.getCurrentState()), pAction);
				} else if(pTestObject.setNextState() === true) {
					if(pAction === "option") {
						initOptionsTestObject = true;
					}
					if(pAction !== "method") {
						that._testState(pDefer, TestObject.copyObject(pTestObject.getCurrentState()), pAction);
					} else {
						that._testMethod(pDefer, TestObject.copyObject(pTestObject.getCurrentState()));
					}
				} else if(pAction === "option" && originalTested === false) {
					originalTested = true;
					that._testState(pDefer, originalState, pAction);
				} else {
					if(pAction !== "method") {
						try {
							that.expectAllRulesTested()
						} catch(e) {
							pDefer.reject(e);
						}
					}
					clearInterval(nextInterval);
					pDefer.resolve();
				}
			}
		}, 10);
		if(pAction !== "method") {
			this._testState(pDefer, TestObject.copyObject(pTestObject.getCurrentState()), pAction);
		} else {
			this._testMethod(pDefer, TestObject.copyObject(pTestObject.getCurrentState()));
		}
	},

	_getValue: function(pRuleProperty, pOptions, pProperties, pArguments, pElement) {
		if(pRuleProperty instanceof RuleProperty) {
			if(pRuleProperty.type === RuleProperty.CSS_SELECTOR) {
				if(Array.isArray(pRuleProperty.key)) {
					var recursive = function(pElement, pSelectors) {
						var elements = pElement.find(pSelectors[0]).toArray();
						if(pSelectors.length > 1) {
							for(var i = 0, len = elements.length; i < len; i++) {
								elements[i] = recursive($(elements[i]), pSelectors.slice(1));
							}
						}
						return elements.map(function(pItem) { return $(pItem); });
					}
					return recursive(pElement, pRuleProperty.key);
				} else {
					return pElement.find(pRuleProperty.key);
				}
			} else if(pRuleProperty.type === RuleProperty.WIDGET_OPTION) {
				return TestObject.getValue(pOptions, pRuleProperty.key);
			} else if(pRuleProperty.type === RuleProperty.WIDGET_VARIABLE) {
				return TestObject.getValue(pProperties, pRuleProperty.key);
			} else if(pRuleProperty.type === RuleProperty.METHOD_ARGUMENT) {
				return pArguments[pRuleProperty.key];
			}
		} else {
			if(typeof pRuleProperty === "function") {
				return pRuleProperty.call(null, pOptions, pProperties, pArguments, pElement);
			} else {
				return pRuleProperty;
			}
		}
	},

	expectAllRulesTested: function(rules) {
		if(rules === undefined) {
			rules = this.rules;
		}
		rules.iterate(function(prop, expectation) {
			try {
				for(var propRule in expectation.expectations) {
					if(propRule !== "tested") {
						expect(expectation.tested[propRule]).to.be.true;
					}
				}
			} catch(e) {
				if(e.name === "AssertionError") {
					e.message = "expected rule to have been tested!";
					e.prop = TestObject.copyObject(prop, 0, [], true);
					e.expectation = TestObject.copyObject(expectation, 0, [], true);
				}
				throw e;
			}
		});
	},

	expectValidOption: function(optionName, type, empty, errorMessage, arrayType) {
		if(ALLOWED_VALIDATE_OPTIONS_TYPES.indexOf(type) === -1) {
			throw new Error("Type '" + type + "' isn't supported as valid option");
		}
		var that = this;
		var testObject = new TestObject(this.optionsDefinition);
		var options = testObject.createRandomObject();
		options[optionName] = null;
		try {
			expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			if(type !== "object") {
				options[optionName] = this._returnValidationVariable("object");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			} else if(type === "object" && empty === false) {
				options[optionName] = this._returnValidationVariable("object", true);
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "string") {
				options[optionName] = this._returnValidationVariable("string");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "boolean") {
				options[optionName] = this._returnValidationVariable("boolean");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "number" && type !== "number_gt0") {
				options[optionName] = this._returnValidationVariable("number_gt0");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "number" && type !== "number_lt0") {
				options[optionName] = this._returnValidationVariable("number_lt0");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "number" && type !== "number_eq0") {
				options[optionName] = this._returnValidationVariable("number_eq0");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
			if(type !== "array") {
				options[optionName] = this._returnValidationVariable("array");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			} else {
				if(empty === false) {
					options[optionName] = this._returnValidationVariable("array", true);
					expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
				}
				if(arrayType !== undefined) {
					for(var i = 0, len = ALLOWED_VALIDATE_OPTIONS_TYPES.length; i < len; i++) {
						if(ALLOWED_VALIDATE_OPTIONS_TYPES[i] !== arrayType && ALLOWED_VALIDATE_OPTIONS_TYPES[i] !== "number") {
							options[optionName] = [this._returnValidationVariable(ALLOWED_VALIDATE_OPTIONS_TYPES[i])];
							expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
						}
					}
				}
			}
			if(type !== "function") {
				options[optionName] = this._returnValidationVariable("function");
				expect(function() { $("<" + that.elementTag + ">")[that.componentName](options); }).to.throw(errorMessage);
			}
		} catch(e) {
			e.options = TestObject.copyObject(options, 0, [], true);
			throw e;
		}
	},

	expectMethodsToExecuteOnAllOptions: function() {
		var that = this;
		return $.Deferred(function(pDefer){
			var testObject = new TestObject(that.optionsDefinition);
			that._testNext(pDefer, testObject, "method");
		});
	},

	expectMethodsToExecute: function(random) {
		var that = this;
		return $.Deferred(function(pDefer){
			var testObject = new TestObject(that.optionsDefinition);
			that._testMethod(pDefer, (random === false?testObject.getCurrentState():testObject.createRandomObject()));
		});
	},

	addBreak: function(condition) {
		this.breakpoints.push(condition);
	},

	_returnValidationVariable: function(type, empty) {
		if(empty === undefined) {
			empty = false;
		}
		switch(type) {
			case "object": return empty===false?{ some: "prop" }:{};
			case "string": return "something";
			case "number_lt0": return -123;
			case "number_eq0": return 0;
			case "number_gt0": return 123;
			case "boolean": return true;
			case "array": return empty===false?["something"]:[];
			case "function": return function() {};
			default: throw new Error("Type '" + type + "' isn't supported as valid option");
		}
	}
}

module.exports = WidgetTestCase;