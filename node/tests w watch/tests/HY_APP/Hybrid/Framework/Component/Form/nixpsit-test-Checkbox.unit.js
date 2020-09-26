describe("nixpsit-form-Checkbox", function() {
	var itemRules = new RuleList();
	itemRules.addRule().onCSS(".on-container").expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.on !== null && pOptions.checked === true; });
	itemRules.addRule().onCSS(".off-container").expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.checked === true; });
	itemRules.addRule().onCSS(".on-container").expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.on !== null && pOptions.checked === false; });
	itemRules.addRule().onCSS(".off-container").expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.checked === false; });
	itemRules.addRule().onCSS(".on-container").expect().html().equalToOption("on.image").onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.on !== null; });
	itemRules.addRule().onCSS(".on-container").expect().attr(["title"]).equalToOption("on.title").onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.on !== null; });
	itemRules.addRule().onCSS(".off-container").expect().html().equalToOption("off.image").onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.on !== null; });
	itemRules.addRule().onCSS(".off-container").expect().attr(["title"]).equalToOption("off.title").onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.on !== null; });
	itemRules.addRule().onCSS(".nixps-cloudflow-Input").expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off !== null && pOptions.on !== null; });
	itemRules.addRule().onCSS(".nixps-cloudflow-Input").expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.off === null || pOptions.on === null; });
	itemRules.addRule().onCSS(".nixps-cloudflow-Input").expect().componentMethods("Input", ["getValue"]).equalToOption("checked");
	itemRules.addRule().onCSS("> label").expect().text().equalToOption("label");
	itemRules.addRule().onCSS("> label").expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.label === undefined || pOptions.label === ""; });
	
	var optionDefinition = new ObjectDefinition([
		new PropertyDefinition("on", ["values"]).chooseFrom([{ image: $("<img>").attr("href", "href1"), title: "tl1" }, null, { image: $("<img>").attr("href", "href2"), title: "tl2" }]),
		new PropertyDefinition("off", ["linked"]).isLinkedWith(["on"]).withCallback(function(pOptions) {
			if(pOptions.on == null) {
				return pOptions.on;
			} else {
				var values = [{ image: $("<img>").attr("href", "href3"), title: "tl3" }, { image: $("<img>").attr("href", "href4"), title: "tl4" }];
				return values[Math.round(Math.random())];	
			}
		}),
		new PropertyDefinition("label", ["string"]),
		new PropertyDefinition("checked", ["boolean"]),
	]);

	var methodRules = new MethodRulesList();
	var setValue = methodRules.createMethodRules("setValue", new ObjectDefinition([new PropertyDefinition("0", ["boolean"])]));
	setValue.addRule().onCSS(".on-container").expect().visible().equalToArgument(0);
	setValue.addRule().onCSS(".off-container").notExpect().visible().equalToArgument(0);
	setValue.addRule().onCSS(".nixps-cloudflow-Input").expect().componentMethods("Input", ["getValue"]).equalToArgument(0);
	
	describe("create", function() {
		it("should validate the options correctly", function() {
			var testObject = new Widget("div", "nixpsit-form.Checkbox", itemRules, optionDefinition);
			testObject.expectValidOption("label", "string", true, "Checkbox: the 'label' option must be a string!");
			testObject.expectValidOption("checked", "boolean", false, "Checkbox: the 'checked' option must be a boolean!");
		});
		it("should draw the checkbox correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.Checkbox", itemRules, optionDefinition);
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementCreateToPassAll()).always(done);
		});
	});
	
	describe("redraw", function() {
		it("should redraw the checkbox correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.Checkbox", itemRules, optionDefinition);
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementRedrawToPassAll()).always(done);
		});
	});
	
	describe("methods", function() {
		it("should have working methods", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.Checkbox", itemRules, optionDefinition, methodRules);
			testObject.setParent($("#mocha"));
			$.when(testObject.expectMethodsToExecuteOnAllOptions()).always(done);
		});
	});
});