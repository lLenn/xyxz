const { MethodRulesList } = require("./MethodRules.js");

ClassTestCase = function(pConstructor, pConstructorDefinition, pConstructorRules, pMethodRules) {
	if(pMethodRules === undefined) {
		pMethodRules = new MethodRulesList();
	}

	this.constructor = pConstructor;
	this.constructorDefinition = pConstructorDefinition;
	this.constructorRules = pConstructorRules;
	this.methodRules = pMethodRules;

	this.instance;
}

ClassTestCase.prototype = {
	constructor: ClassTestCase,

	init: function() {
	},

	create: function() {
		this.instance = new (Function.prototype.bind.apply(this.constructor, [null].concat(Array.from(arguments))));
	},

	expectConstructorToPassAll: function(pAllCombinations) {
		var that = this;
		return $.Deferred(function(pDefer){
			that._testNext(pDefer, [{ name: "constructor", definition: that.constructorDefinition, rules: that.constructorRules }], pAllCombinations);
		});
	},

	expectMethodsToExecuteOnAllConstructions: function(pAllCombinations) {
		var that = this;
		return $.Deferred(function(pDefer){
			that._testNext(pDefer, [{
				name: "constructor",
				definition: that.constructorDefinition,
				rules: that.constructorRules
			}].concat(
				that.methodRules.rules.map(function(pRule) { return { name: pRule.name, definition: pRule.definition, rules: pRule.rules }; })
			), pAllCombinations);
		});
	},

	_testNext: function(pDefer, pSequence, pAllCombinations) {
		var sequencePointer = 0;
		var that = this;
		var nextInterval = setInterval(function() {
			try {
				if(that.testNextState === true) {
					that.testNextState = false;
					if(pSequence[sequencePointer].testObject === undefined || pSequence[sequencePointer].testObject.setNextState() === true) {
						if(pSequence[sequencePointer].testObject === undefined) {
							pSequence[sequencePointer].testObject = new TestObject(pSequence[sequencePointer].definition, pAllCombinations);
						}
						if(sequencePointer !== 0) {
							that.init();
							that.create(pSequence[0].testObject.getCurrentState());
							for(var i = 1; i < sequencePointer; i++) {
								that._executeMethod(pSequence[i].name, pSequence[i].testObject);
							}
						}
						var prevSequencePointer = sequencePointer;
						if(sequencePointer + 1 < pSequence.length) {
							sequencePointer++;
							pSequence[sequencePointer].testObject.definition.resetAll()
						}
						that._testMethod(pDefer, pSequence[prevSequencePointer]);
					} else if(sequencePointer > 0) {
						sequencePointer--;
						that.testNextState = true;
						return;
					} else {
						try {
							that.expectAllRulesTested(pSequence[0].rules);
						} catch(e) {
							clearInterval(nextInterval);
							pDefer.reject(e);
						}
						clearInterval(nextInterval);
						pDefer.resolve();
					}
				}
			} catch(e) {
				clearInterval(nextInterval);
				pDefer.reject(e);
			}
		}, 10);
		this.testNextState = true;
	},

	_testMethod: function(pDefer, pMethodRule) {
		try {
			//Add support for async calls
			//Add expectAllRulesTested for methods
			var state = pMethodRule.testObject.getCurrentState();
			var args = [];
			for(var index in state) {
				args[parseInt(index)] = TestObject.copyObject(state[index], 0, [], true);
			}
			console.log(pMethodRule.name + ": ");
			console.log(args);
			var value = this._executeMethod(pMethodRule.name, args);
			this.expectInstanceToAbideByRules(TestObject.copyObject(pMethodRule.testObject.getCurrentState()), pMethodRule.rules);
			if(typeof pMethodRule.definition.returnValue === "object") {
				expect(value).to.deep.equal(pMethodRule.definition.returnValue);
			} else if(pMethodRule.definition.returnValue !== undefined) {
				expect(value).to.equal(pMethodRule.definition.returnValue);
			}
		} catch(e) {
			e.method = TestObject.copyObject(pMethodRule.name, 0, [], true);
			e.args = TestObject.copyObject(pMethodRule.testObject.getCurrentState(), 0, [], true);
			throw e;
		};
		this.testNextState = true;
	},

	/**
	 * @description: applies this.rules onto the element depending on the object
	 * @param {object} The object
	 */
	expectInstanceToAbideByRules: function(object, rules) {
		var that = this;
		var instance = this.instance;
		var argumentsObject = TestObject.copyObject(object);
		var propObject = {};
		for(var prop in instance) {
			if(instance.hasOwnProperty(prop)) {
				propObject[prop] = TestObject.copyObject(instance[prop]);
			}
		}

		rules.iterate(function(prop, expectation) {
			try {
				if(expectation.condition !== undefined) {
					if(expectation.condition.call(null, argumentsObject, propObject, instance) === false) {
						return;
					}
				}
				if(prop.type !== RuleProperty.ALL) {
					var actualValue = that._getValue(prop, argumentsObject, propObject, instance);
					var expectedValue = that._getValue(expectation.value, argumentsObject, propObject, instance);
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
								expectation.testValues(testActualValue, testExpectedValue, object);
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
						expectation.testValues(TestObject.getValue(actualValue, prop.subKey), expectedValue, object);
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
					expectation.customTest.call(null, argumentsObject, propObject, instance);
				}
			} catch(e) {
				e.prop = TestObject.copyObject(prop, 0, [], true);
				e.expectation = TestObject.copyObject(expectation, 0, [], true);
				throw e;
			}
		});
	},

	_executeMethod: function(pName, pArguments) {
		try {
			if(pName !== "constructor") {
				return this.instance[pName].apply(this.instance, pArguments);
			} else {
				this.create.apply(this, pArguments);
			}
		} catch(e) {
			throw e;
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

	copy: function() {
		return new ClassTestCase(this.constructor, this.constructorDefinition.copy(), this.constructorRules.copy(), this.methodRules.copy())
	}
}

module.exports = ClassTestCase;