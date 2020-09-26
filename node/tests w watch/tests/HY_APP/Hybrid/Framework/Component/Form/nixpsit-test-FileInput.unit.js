var itemRules = new RuleList();
itemRules
	.addRule().onCSS(".file-container > .view")
		.expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length === 0; })
		.expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length !== 0; })
	.addRule().onCSS(".file-container > .edit")
		.expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length !== 0; })
		.expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length === 0; })
	.addRule().onCSS(".file-container > .file-name")
		.expect().text().equalToOption("file").manipulateWith(function(pValue) { return (pValue==null?"":pValue); })
	.addRule().onCSS(".upload-file")
		.expect().componentOptions("Button", ["type"]).equalTo("upload")
		.expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.indexOf("all") !== -1 || pOptions.rights.indexOf("upload") !== -1; })
		.expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length === 0; })
	.addRule().onCSS(".remove-file")
		.expect().componentOptions("Button", ["type"]).equalTo("remove")
		.expect().visible().equalTo(true).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.indexOf("all") !== -1 || pOptions.rights.indexOf("remove") !== -1; })
		.expect().visible().equalTo(false).onConditionThat(function(pOptions, pProps, pArgs, pElement) { return pOptions.rights.length === 0; });

describe.only("nixpsit-form-FileInput", function(){
	describe("create", function() {
		it("should validate the options correctly", function() {
			var testObject = new Widget("div", "nixpsit-form.FileInput", itemRules, new FileInputDefinition());
		});
		it("should draw the file input correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.FileInput", itemRules, new FileInputDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementCreateToPassAll()).always(done);
		});
	});
	
	describe("redraw", function() {
		it("should redraw the file input correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixpsit-form.FileInput", itemRules, new FileInputDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementRedrawToPassAll()).always(done);
		});
	});
});