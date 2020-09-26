const webdriver = require("nixpsit-test").testing.getWebdriver();
const Checkbox = require("./classes/nixpsit-functional-Checkbox.class.js");

describe("nixpsit-form-Checkbox", function(){
	describe("events", function() {
		it("should change when clicked and fire an event", async function() {
			this.timeout(10000);
			var checkbox = new Checkbox(webdriver, {});
			await checkbox.create();
			await checkbox.expectEventToFire("click", "checkboxchange", { checked: true });
			await checkbox.on.isVisible();
			await checkbox.off.isNotVisible();
			await checkbox.input.isNotVisible();
			await checkbox.input.expectToHaveAttr("checked", 'true');
			await checkbox.expectEventToFire("click", "checkboxchange", { checked: false });
			await checkbox.off.isVisible();
			await checkbox.on.isNotVisible();
			await checkbox.input.isNotVisible();
			await checkbox.input.expectToNotHaveAttr("checked");
			
			checkbox.componentOptions = { on: null, off: null, label: "hey" };
			await checkbox.redraw();
			await checkbox.input.expectEventToFire("click", "checkboxchange", { checked: true });
			await checkbox.on.isNotVisible();
			await checkbox.off.isNotVisible();
			await checkbox.input.isVisible();
			await checkbox.input.expectToHaveAttr("checked", 'true');
			await checkbox.input.expectEventToFire("click", "checkboxchange", { checked: false });
			await checkbox.off.isNotVisible();
			await checkbox.on.isNotVisible();
			await checkbox.input.isVisible();
			await checkbox.input.expectToNotHaveAttr("checked");
			await checkbox.remove();
		});
	});
});