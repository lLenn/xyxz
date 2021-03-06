JQueryRuleExpectation = function(pExpectations, pValue, pCondition) {
	this.ruleExpectation(pExpectations, pValue, pCondition);
	this.type = "jquery_expectation";
}

JQueryRuleExpectation.prototype = Object.assign({}, RuleExpectation.prototype, {
	constructor: JQueryRuleExpectation,
	ruleExpectation: RuleExpectation,

	attr: function(pNames) {
		if(Array.isArray(pNames) === false) {
			throw new Error("JQueryRuleExpectation: attr expects an array as first argument!");
		}
		this.expectations.attr = pNames;

		return this;
	},

	data: function(pKeys) {
		if(Array.isArray(pKeys) === false) {
			throw new Error("JQueryRuleExpectation: data expects an array as first argument!");
		}
		this.expectations.data = pKeys;

		return this;
	},

	text: function() {
		this.expectations.text = true;

		return this;
	},

	className: function() {
		this.expectations.className = true;

		return this;
	},

	visible: function() {
		this.expectations.visible = true;

		return this;
	},

	html: function() {
		this.expectations.html = true;

		return this;
	},

	css: function(pStyle) {
		this.expectations.css = pStyle;

		return this;
	},

	componentOptions: function(pName, pOptions) {
		if(pOptions !== undefined && Array.isArray(pOptions) === false) {
			throw new Error("JQueryRuleExpectation: componentOptions expects an array or undefined as second argument!");
		}
		if(this.expectations.component === undefined) {
			this.expectations.component = { name: pName };
		}
		this.expectations.component.options = pOptions;

		return this;
	},

	componentMethods: function(pName, pMethods) {
		if(Array.isArray(pMethods) === false) {
			throw new Error("JQueryRuleExpectation: componentMethods expects an array as second argument!");
		}
		if(this.expectations.component === undefined) {
			this.expectations.component = { name: pName };
		}
		this.expectations.component.methods = pMethods;

		return this;
	},

	testExpectations: function(pActual, pExpectation) {
		if(this.expectations.css !== undefined) {
			this.tested.css = true;
			this.testExpectation(pActual.css(this.expectations.css), pExpectation);
		}
		if(this.expectations.html !== undefined && this.expectations.html === true) {
			this.tested.html = true;
			expect(pActual.html()).to.equal(pExpectation[0].outerHTML);
		}
		if(this.expectations.text !== undefined && this.expectations.text === true) {
			this.tested.text = true;
			expect(pActual.text()).to.equal(pExpectation);
		}
		if(this.expectations.attr !== undefined) {
			this.tested.attr = true;
			for(var i = 0, len = this.expectations.attr.length; i < len; i++) {
				expect(pActual.attr(this.expectations.attr[i])).to.equal(pExpectation);
			}
		}
		if(this.expectations.data !== undefined) {
			this.tested.data = true;
			for(var i = 0, len = this.expectations.data.length; i < len; i++) {
				expect(pActual.data(this.expectations.data[i])).to.equal(pExpectation);
			}
		}
		if(this.expectations.className !== undefined) {
			this.tested.className = true;
			if(typeof this.expectations.className === "string") {
				var classes = this.expectations.className.split(" ");
			} else {
				var classes = pExpectation.split(" ");
			}
			for(var i = 0, len = classes.length; i < len; i++) {
				expect(pActual.hasClass(classes[i])).to.be.true;
			}
		}
		if(this.expectations.component !== undefined) {
			this.tested.component = true;
			if(this.expectations.component.options !== undefined) {
				for(var i = 0, len = this.expectations.component.options.length; i < len; i++) {
					this.testExpectation(pActual[this.expectations.component.name]("option", this.expectations.component.options[i]), pExpectation);
				}
			} else if(this.expectations.component.methods !== undefined) {
				for(var i = 0, len = this.expectations.component.methods.length; i < len; i++) {
					this.testExpectation(pActual[this.expectations.component.name](this.expectations.component.methods[i]), pExpectation);
				}
			} else {
				expect(pActual[this.expectations.component.name]("option")).to.deep.include(pExpectation);
			}
		}

		if(this.expectations.visible !== undefined) {
			this.tested.visible = true;
			this.testExpectation(pExpectation, pActual.css("display") !== "none");
		}
	},

	copy: function() {
		return new JQueryRuleExpectation(this.expectations, this.value.copy(), this.condition);
	}
});