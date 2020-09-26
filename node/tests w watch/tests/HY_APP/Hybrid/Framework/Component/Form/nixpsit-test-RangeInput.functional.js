const webdriver = require("nixpsit-test").testing.getWebdriver();
const RangeInput = require("./classes/nixpsit-functional-RangeInput.class.js");
const { RangeInputDefinition } = require("./classes/nixpsit-test-RangeInput.definition.js");

describe("nixpsit-form-RangeInput", function(){
	describe("events", function() {
		it("should show value when hovering over", async function() {
			this.timeout(20000);
			var RangeInput = new RangeInput(webdriver, { minValue: 0, maxValue: 100, value: 50 });
			await RangeInput.create();
			await RangeInput.valueIndicator.isNotVisible();
			await RangeInput.sliderButton.hover();
			await RangeInput.valueIndicator.isVisible();
			await RangeInput.valueIndicator.expectToHaveText("50");
			await RangeInput.remove();
		});

		it("should change when dragging", async function() {
			this.timeout(20000);
			await webdriver.manage().window().setRect({ x: 20, y: 20, width: 1153, height: 600 });
			var RangeInput = new RangeInput(webdriver, { minValue: 0, maxValue: 100, value: 50 });
			await RangeInput.create();
			await RangeInput.css("padding", "20px");
			var rectButton = await RangeInput.sliderButton.getDimension();
			var rectSlider = await RangeInput.sliderContainer.getDimension();
			await RangeInput.sliderButton.hover();
			var firstRectIndicator = await RangeInput.valueIndicator.getDimension();

			await RangeInput.sliderButton.dragAndDrop({ x: -30, y: 0 });
			await RangeInput.sliderButton.expectToHaveStyle({ left: (rectButton.x - rectSlider.x - 30) + "px" });
			var rectIndicator = await RangeInput.valueIndicator.getDimension();
			await RangeInput.valueIndicator.expectToHaveStyle({ left: (firstRectIndicator.x - rectSlider.x - 30 + (firstRectIndicator.width/2) - (rectIndicator.width/2)) + "px" });
			var value = ((rectButton.x - rectSlider.x - 20)/rectSlider.width) * 100;
			await RangeInput.expectOptionCloseTo("value", value);

			await RangeInput.sliderButton.dragAndDrop({ x: 50, y: 0 });
			await RangeInput.sliderButton.expectToHaveStyle({ left: (rectButton.x - rectSlider.x + 20) + "px" });
			rectIndicator = await RangeInput.valueIndicator.getDimension();
			await RangeInput.valueIndicator.expectToHaveStyle({ left: (firstRectIndicator.x - rectSlider.x + 20 + (firstRectIndicator.width/2) - (rectIndicator.width/2)) + "px" });
			value = ((rectButton.x - rectSlider.x + 30)/rectSlider.width) * 100;
			await RangeInput.expectOptionCloseTo("value", value);

			await RangeInput.sliderButton.click({ x: 100, y: 0 });
			await RangeInput.sliderButton.expectToHaveStyle({ left: (rectButton.x - rectSlider.x + 120) + "px" });
			rectIndicator = await RangeInput.valueIndicator.getDimension();
			await RangeInput.valueIndicator.expectToHaveStyle({ left: (firstRectIndicator.x - rectSlider.x + 120 + (firstRectIndicator.width/2) - (rectIndicator.width/2)) + "px" });
			value = ((rectButton.x - rectSlider.x + 130)/rectSlider.width) * 100;
			await RangeInput.expectOptionCloseTo("value", value);

			await RangeInput.sliderButton.click({ x: -50, y: 0 });
			await RangeInput.sliderButton.expectToHaveStyle({ left: (rectButton.x - rectSlider.x + 70) + "px" });
			rectIndicator = await RangeInput.valueIndicator.getDimension();
			await RangeInput.valueIndicator.expectToHaveStyle({ left: (firstRectIndicator.x - rectSlider.x + 70 + (firstRectIndicator.width/2) - (rectIndicator.width/2)) + "px" });
			value = ((rectButton.x - rectSlider.x + 80)/rectSlider.width) * 100;
			await RangeInput.expectOptionCloseTo("value", value);
			await RangeInput.remove();
		});

		it("should adjust on resize", async function() {
			this.timeout(20000);
			var RangeInput = new RangeInput(webdriver, { minValue: 0, maxValue: 100, value: 50 });
			await RangeInput.create();
			var rectSlider = await RangeInput.getRect();
			await RangeInput.sliderButton.expectToHaveStyle({ left: Math.floor((rectSlider.width/2) - 10) + "px" });
			var windowRect = await webdriver.manage().window().getRect();
			await webdriver.manage().window().setRect({ x: 20, y: 20, width: windowRect.width - 50, height: windowRect.height - 50 });
			rectSlider = await RangeInput.getRect();
			await RangeInput.sliderButton.expectToHaveStyle({ left: Math.floor((rectSlider.width/2) - 10) + "px" });
			await webdriver.manage().window().setRect({ x: 20, y: 20, width: windowRect.width - 74.67, height: windowRect.height - 50 });
			rectSlider = await RangeInput.getRect();
			await RangeInput.sliderButton.expectToHaveStyle({ left: Math.floor((rectSlider.width/2) - 10) + "px" });
			await webdriver.manage().window().setRect(windowRect);
			await RangeInput.remove();
		});
	});
});