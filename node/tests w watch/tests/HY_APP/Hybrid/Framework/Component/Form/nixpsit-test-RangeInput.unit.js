describe("nixpsit-form-RangeInput", function() {
    var itemRules = new RuleList();
	itemRules
		.addRule().onCSS(".slider-container").expect().lengthOf().equalTo(1)
		.addRule().onCSS(".min-max-background").expect().lengthOf().equalTo(1)
		.addRule().onCSS(".value-background")
			.expect().lengthOf().equalTo(1)
			.expect().css("width").equalTo(function(pOptions, pProps, pArgs, pEl) {
				if(pProps.options.value !== undefined) {
					return Math.floor(((pProps.options.value - pOptions.minValue)/(pOptions.maxValue - pOptions.minValue))*pEl.width())+"px";
				} else {
					return "0px";
				} 
			})
		.addRule().onCSS(".slider-button")
			.expect().lengthOf().equalTo(1)
			.expect().css("left").equalTo(function(pOptions, pProps, pArgs, pEl) {
				if(pProps.options.value !== undefined) {
					return Math.floor(((pProps.options.value - pOptions.minValue)/(pOptions.maxValue - pOptions.minValue))*pEl.width() - 10) +"px";
				} else {
					return "-10px";
				} 
			})
		.addRule().onCSS(".value-indicator")
			.expect().visible().equalTo(false)			
			.expect().text().equalTo(function(pOptions, pProps) { return pProps.options.value.toFixed(0).toString(); })
			.expect().css("left").equalTo(function(pOptions, pProps, pArgs, pEl) {
				if(pProps.options.value !== undefined) {
					return Math.floor(((pProps.options.value - pOptions.minValue)/(pOptions.maxValue - pOptions.minValue))*pEl.width() - Math.floor(pEl.find(".value-indicator").outerWidth()/2)) +"px";
				} else {
					return -Math.floor(pEl.find(".value-indicator").outerWidth()/2) + "px";
				} 
			});			
			
	var methodRules = new MethodRulesList();
	methodRules.createMethodRules("setValue", new RangeInputSetValueDefinition())
		.addRule().onOption("value")
			.expect().it().equalToArgument("0");

	describe("create", function() {
		it("should validate the options correctly", function() {
			var testObject = new Widget("div", "nixpsit-form.RangeInput", itemRules, new RangeInputDefinition());
			testObject.expectValidOption("minValue", "number", true, "RangeInput: the 'minValue' option should be a number!");
			testObject.expectValidOption("maxValue", "number", true, "RangeInput: the 'maxValue' option should be a number!");				
		});

		it("should draw the slider correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.RangeInput", itemRules, new RangeInputDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementCreateToPassAll()).always(done);
		});
	});
	
	describe("redraw", function() {
		it("should redraw the slider correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.RangeInput", itemRules, new RangeInputDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementRedrawToPassAll()).always(done);
		});
	});
	
	describe("methods", function() {
		it("should have working methods", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.RangeInput", itemRules, new RangeInputDefinition(), methodRules);
			testObject.setParent($("#mocha"));
			$.when(testObject.expectMethodsToExecuteOnAllOptions()).always(done);
		});
	});
});