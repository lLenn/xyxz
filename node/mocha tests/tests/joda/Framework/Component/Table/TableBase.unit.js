const TableBaseDefinition = require("./TableBase.definition");

describe("nixps-table-TableBase", function() {
	var itemRules = new RuleList();
	itemRules
		.addRule().iterateOverCSS(["tbody > tr", "td"]).expect().html().equalTo(function(pOptions, pProperties, pArguments, pElement) {
			return pOptions.dataProvider.map(function(pData) {
				return pOptions.rows[0].columns.map(function(pColumn) {
					var content = pColumn.cellRenderer.render(pProperties, pData[pColumn.key]);
					if(content instanceof $) {
						content = content.html();
					};
					return content;
				});
			});
		});

	describe("create", function() {
		it.skip("should validate the options correctly", function() {
			var testObject = new Widget("div", "nixps-table.TableBase", itemRules, new TableBaseDefinition());
			testObject.expectValidOption("inputForm", "object", false, "Checkbox: the 'label' option must be a string!");
		});
		it("should draw the table correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixps-table.TableBase", itemRules, new TableBaseDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementCreateToPassAll()).always(done);
		});
	});

	describe("redraw", function() {
		it("should redraw the table correctly", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixps-table.TableBase", itemRules, new TableBaseDefinition());
			testObject.setParent($("#mocha"));
			$.when(testObject.expectElementRedrawToPassAll()).always(done);
		});
	});

	describe.skip("methods", function() {
		it("should have working methods", function(done) {
			this.timeout(30000);
			var testObject = new Widget("div", "nixps-table.TableBase", itemRules, new TableBaseDefinition(), methodRules);
			testObject.setParent($("#mocha"));
			$.when(testObject.expectMethodsToExecuteOnAllOptions()).always(done);
		});
	});
});