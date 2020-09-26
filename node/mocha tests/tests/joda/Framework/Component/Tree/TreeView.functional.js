const webdriver = require("../../../../driver");
const { By, until } = require("selenium-webdriver");
const { expect } = require("chai");

describe("nixps-tree-TreeView", function() {
	describe("events", function() {
		it("should open and close child nodes", async function(){
			this.timeout(30000);
			await webdriver.executeScript(function() {
				var renderNode = function(node) {
					return $("<div>").text(node.text)
				}
				var root = {
					text: "root",
					leaf: false,
					expanded: true,
					children: [
						{
							text: "child1",
							leaf: false,
							expanded: true,
							children: [
								{
									text: "child4",
									leaf: true,
									expanded: false
								},
								{
									text: "child5",
									leaf: true,
									expanded: false
								},
								{
									text: "child6",
									leaf: true,
									expanded: false
								}
							]
						},
						{
							text: "child2",
							leaf: true,
							expanded: false
						},
						{
							text: "child3",
							leaf: false,
							expanded: false,
							children: [
								{
									text: "child7",
									leaf: true,
									expanded: false
								}
							]
						}
					]
				};
				$("#tests_container").append($("<div>").TreeView({ root: root, renderNode: renderNode }));
			});
			await webdriver.wait(until.elementLocated(By.css('.nixps-tree-TreeView > .root-node > .tree-node')), 10000);

			var treeNode = webdriver.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node'));
			await treeNode.findElement(By.css('.icon')).click();
			expect(await treeNode.findElement(By.css('.child-nodes')).getCssValue("display")).to.equal("none");
			await treeNode.findElement(By.css('.render')).click();
			expect(await treeNode.findElement(By.css('.child-nodes')).getCssValue("display")).to.equal("block");

			var className = await treeNode.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node:nth-child(2) > .tree-node > .render'));
			expect((await className.getAttribute("class")).indexOf("odd")).to.not.equal(-1);
			treeNode = webdriver.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node:nth-child(1) > .tree-node'));
			await treeNode.findElement(By.css('.icon')).click();
			expect((await className.getAttribute("class")).indexOf("even")).to.not.equal(-1);

			treeNode = webdriver.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node:nth-child(3) > .tree-node'));
			await treeNode.findElement(By.css('.icon')).click();
			expect(await treeNode.findElement(By.css('.child-nodes')).getCssValue("display")).to.equal("block");
		});

		it("should trigger an event when a row is clicked (waiting until chromedriver is W3C compliant)");
		/*
		 * We need to wait till chromedriver is W3C compliant
		 	, async function(){
			this.timeout(30000);
			await webdriver.executeScript(function() {
				window.eventTriggered = false;
				var renderNode = function(node) {
					return $("<div>").text(node.text)
				}
				var root = {
					text: "root",
					leaf: false,
					expanded: true,
					children: [
						{
							text: "child1",
							leaf: false,
							expanded: true,
							children: [
								{
									text: "child4",
									leaf: true,
									expanded: false
								},
								{
									text: "child5",
									leaf: true,
									expanded: false
								},
								{
									text: "child6",
									leaf: false,
									expanded: false
								}
							]
						},
						{
							text: "child2",
							leaf: true,
							expanded: false
						},
						{
							text: "child3",
							leaf: false,
							expanded: true,
							children: [
								{
									text: "child7",
									leaf: true,
									expanded: false
								}
							]
						}
					]
				};
				$("#tests_container").on("treeviewleafclick", function(event, data) {
					window.eventTriggered = data.text;
				});
				$("#tests_container").append($("<div>").TreeView({ root: root, renderNode: renderNode }));
			});
			await webdriver.wait(until.elementLocated(By.css('.nixps-tree-TreeView > .root-node > .tree-node')), 10000);

			var treeNode = webdriver.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node:nth-child(3) > .tree-node > .child-nodes > .child-node:nth-child(1) > .tree-node'));
			await treeNode.findElement(By.css('.render')).click();
			expect(await webdriver.executeScript(function() { return window.eventTriggered; })).to.equal("child7");

			await webdriver.executeScript(function() {
				window.eventTriggered2 = false;
				$("#tests_container").on("treeviewleafclick", function(event, data) {
					window.eventTriggered2 = data;
				});
				$("#tests_container").children(".nixps-tree-TreeView").TreeView("option", {
					root: {
						"name":"cloudflow://PP_FILE_STORE/products/testPIDJob10/",
						"expanded":true,
						"leaf":false,
						"children": [
							{
								"name":"files",
								"expanded":true,
								"leaf":false,
								"children": [
									{
										"name":"00003.pdf",
										"expanded":false,
										"leaf":true,
										"url":"cloudflow://PP_FILE_STORE/products/testPIDJob10/files/00003.pdf"
									},{
										"name":"00004.pdf",
										"expanded":false,
										"leaf":true,
										"url":"cloudflow://PP_FILE_STORE/products/testPIDJob10/files/00004.pdf"
									}
								]
							}
						]
					},
					renderNode: function(node) {
						return $("<div>").text(node.name)
					}
				});
			});
			await webdriver.wait(until.elementLocated(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node > .tree-node > .child-nodes > .child-node:nth-child(1) > .tree-node')), 10000);

			var treeNode = webdriver.findElement(By.css('.nixps-tree-TreeView > .root-node > .tree-node > .child-nodes > .child-node > .tree-node > .child-nodes > .child-node:nth-child(1) > .tree-node'));
			await treeNode.click();
			expect(await webdriver.executeScript(function() { return window.eventTriggered2; })).to.deep.equal({
				"name":"00003.pdf",
				"expanded":false,
				"leaf":true,
				"url":"cloudflow://PP_FILE_STORE/products/testPIDJob10/files/00003.pdf"
			});
		});
			*/
	});
});