describe("nixps-tree-TreeView", function(){
	
	var iconClasses1 = ["fa fa-caret-down", "fa fa-caret-right"];
	var iconClasses2 = ["someIcon", "someOtherIcon"];
	
	describe("create", function() {
		it("should validate the options correctly", function() {			
			expect(function() { $("<div>").TreeView({ root: null }); }).to.throw('TreeView: root option must be an object!');
			expect(function() { $("<div>").TreeView({ root: 123 }); }).to.throw('TreeView: root option must be an object!');
			expect(function() { $("<div>").TreeView({ root: function(){} }); }).to.throw('TreeView: root option must be an object!');
			expect(function() { $("<div>").TreeView({ root: "" }); }).to.throw('TreeView: root option must be an object!');
			expect(function() { $("<div>").TreeView({ root: ["something"] }); }).to.throw('TreeView: root option must be an object!');
			
			expect(function() { $("<div>").TreeView({ renderNode: null }); }).to.throw('TreeView: renderNode option must be a function!');
			expect(function() { $("<div>").TreeView({ renderNode: 123 }); }).to.throw('TreeView: renderNode option must be a function!');
			expect(function() { $("<div>").TreeView({ renderNode: {} }); }).to.throw('TreeView: renderNode option must be a function!');
			expect(function() { $("<div>").TreeView({ renderNode: "" }); }).to.throw('TreeView: renderNode option must be a function!');
			expect(function() { $("<div>").TreeView({ renderNode: ["something"] }); }).to.throw('TreeView: renderNode option must be a function!');
			
			expect(function() { $("<div>").TreeView({ iconClasses: null }); }).to.throw("TreeView: iconClasses option must be an object with 'open' and 'closed' properties defined!");
			expect(function() { $("<div>").TreeView({ iconClasses: 123 }); }).to.throw("TreeView: iconClasses option must be an object with 'open' and 'closed' properties defined!");
			expect(function() { $("<div>").TreeView({ iconClasses: "" }); }).to.throw("TreeView: iconClasses option must be an object with 'open' and 'closed' properties defined!");
			expect(function() { $("<div>").TreeView({ iconClasses: ["something"] }); }).to.throw("TreeView: iconClasses option must be an object with 'open' and 'closed' properties defined!");
		});
		
		checkNode = function(node, toCheck, iconClasses, renderAppendix, rowCount, indent, inExpanded, node_id) {
			if(iconClasses === undefined) {
				iconClasses = iconClasses1;
			}
			if(renderAppendix === undefined) {
				renderAppendix = "";
			}
			if(rowCount === undefined) {
				rowCount = 0;
			}
			if(indent === undefined) {
				indent = 1;
			}
			if(node_id === undefined) {
				node_id = "0";
			}
			if(inExpanded === undefined) {
				inExpanded = toCheck.expanded;
			}
			expect(node.children(".tree-node")).to.have.length(1);
			expect(node.children(".tree-node").attr("data-node-id")).to.equal(node_id);
			if(inExpanded === true) {
				expect(node.children(".tree-node").children(".content").hasClass(rowCount%2===1?"odd":"even")).to.be.true;
			}
			expect(node.children(".tree-node").css("margin-left")).to.equal(-(indent-1)*15 + "px");
			expect(node.children(".tree-node").css("padding-left")).to.equal((indent+(toCheck.leaf === false?0:1))*15 + "px");
			expect(node.children(".tree-node").children(".content").css("margin-left")).to.equal(-(indent+(toCheck.leaf === false?0:1))*15 + "px");
			expect(node.children(".tree-node").children(".content").css("padding-left")).to.equal((indent+(toCheck.leaf === false?0:1))*15 + "px");
			if(toCheck.leaf === false) {
				expect(node.children(".tree-node").children(".content").children("span.icon")).to.have.length(1);
				expect(node.children(".tree-node").children(".content").children("span.icon").hasClass(toCheck.expanded===true?iconClasses[0]:iconClasses[1])).to.be.true;
				expect(node.children(".tree-node").children(".child-nodes")).to.have.length(1);
				expect(node.children(".tree-node").children(".child-nodes").css("display")).to.equal(toCheck.expanded === true?"block":"none");
				if(toCheck.children !== undefined && toCheck.children.length > 0) {
					expect(node.children(".tree-node").children(".child-nodes").children(".child-node")).to.have.length(toCheck.children.length);
					for(var i = 0, len = toCheck.children.length; i < len; i++) {
						checkRowCount = checkNode($(node.children(".tree-node").children(".child-nodes").children(".child-node")[i]), toCheck.children[i], iconClasses, renderAppendix, rowCount+1, indent+1, inExpanded && toCheck.expanded, node_id + "." + i);
						if(toCheck.expanded === true) {
							rowCount = checkRowCount;
						}
					}
				} else {
					expect(node.children(".tree-node").children(".child-nodes").children(".child-node")).to.have.length(0);
				}
			} else {
				expect(node.children(".tree-node").children(".content").children("span.icon")).to.have.length(0);
				expect(node.children(".tree-node").children(".child-nodes")).to.have.length(0);
			}
			expect(node.children(".tree-node").children(".content").children(".render")).to.have.length(1);
			expect(node.children(".tree-node").children(".content").children(".render").children("div").text()).to.equal(toCheck.text+renderAppendix);
			
			return rowCount;
		}
		
		it("should draw the component correctly given a certain root", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			var root = {
				text: "root",
				leaf: false,
				expanded: false,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					}
				]
			};
			var tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root)
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root);
		});
		
		it("should draw the component correctly given certain iconClasses", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode, iconClasses: { open: iconClasses2[0], closed: iconClasses2[1] }});
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root, iconClasses2);
		});
		
		it("should draw the component correctly given certain renderNode", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text+"103")
			}
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode, iconClasses: { open: iconClasses2[0], closed: iconClasses2[1] }});
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root, iconClasses2, "103");
		});
	});
	
	describe("redraw", function() {
		it("should redraw the component correctly", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			var root = {
				text: "root",
				leaf: false,
				expanded: false,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					}
				]
			};
			var tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			$("#mocha").append(tree);
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root)
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree.TreeView("option", { root: $.extend(true, {}, root) });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root);
			
			root = {
				text: "root",
				leaf: false,
				expanded: true
			};
			tree.TreeView("option", { root: $.extend(true, {}, root) });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root);

			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: false,
						expanded: false,
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
			tree.TreeView("option", { root: $.extend(true, {}, root) });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root);
			
			root = {
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
									text: "child6",
									leaf: false,
									expanded: false
								}
							]
						},
						{
							text: "child2",
							leaf: false,
							expanded: false,
							children: [
								{
									text: "child7",
									leaf: true,
									expanded: false
								}
							]
						},
						{
							text: "child3",
							leaf: true,
							expanded: false
						}
					]
				};
				tree.TreeView("option", { root: $.extend(true, {}, root) });
				expect(tree.children(".root-node")).to.have.length(1);
				checkNode(tree.children(".root-node"), root);
		});
		
		it("should redraw the component correctly given certain iconClasses", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode});
			
			tree.TreeView("option", { iconClasses: { open: iconClasses2[0], closed: iconClasses2[1] } });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root, iconClasses2);
		});
		
		it("should redraw the component correctly given certain renderNode", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			
			root = {
				text: "root",
				leaf: false,
				expanded: true,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: false,
						expanded: false
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			
			var secondRenderNode = function(node) {
				return $("<div>").text(node.text+"103")
			}
			
			tree.TreeView("option", { renderNode: secondRenderNode });
			expect(tree.children(".root-node")).to.have.length(1);
			checkNode(tree.children(".root-node"), root, undefined, "103");
		});
	});
	
	describe("getState", function() {
		it("should get the correct state", function() {
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
						leaf: true,
						expanded: false
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
						children: [{
								text: "child4",
								leaf: true,
								expanded: false
							},
							{
								text: "child5",
								leaf: false,
								expanded: false
							}
						]
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			expect(tree.TreeView("getState")).to.deep.equal({ nodes: {
				"0": true,
				"0.2": false,
				"0.2.1": false
			}});
		});
	});
	
	describe("setState", function() {
		it("should set the correct state", function() {
			var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			var root = {
				text: "root",
				leaf: false,
				expanded: false,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
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
						children: [{
								text: "child4",
								leaf: true,
								expanded: false
							},
							{
								text: "child5",
								leaf: false,
								expanded: true
							}
						]
					}
				]
			};
			tree = $("<div>").TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			tree.TreeView("setState", { nodes: {
				"0": true,
				"0.2": false,
				"0.2.1": false
			}});
			root.expanded = true;
			root.children[2].expanded = false;
			root.children[2].children[1].expanded = false;
			checkNode(tree.children(".root-node"), root);var renderNode = function(node) {
				return $("<div>").text(node.text)
			}
			
			root = {
				text: "root",
				leaf: false,
				expanded: false,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
					},
					{
						text: "child2",
						leaf: true,
						expanded: false
					},
					{
						text: "child3",
						leaf: true,
						expanded: true,
					}
				]
			};
			tree.TreeView("option", { root: $.extend(true, {}, root), renderNode: renderNode });
			tree.TreeView("setState", { nodes: {
				"0": true,
				"0.2": false,
				"0.2.1": false
			}});
			root.expanded = true;
			checkNode(tree.children(".root-node"), root);
			
			root = {
				text: "root",
				leaf: false,
				expanded: false,
				children: [
					{
						text: "child1",
						leaf: true,
						expanded: false
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
						children: [{
								text: "child4",
								leaf: true,
								expanded: false
							},
							{
								text: "child5",
								leaf: false,
								expanded: true
							}
						]
					}
				]
			};
			tree.TreeView({ root: $.extend(true, {}, root), renderNode: renderNode });
			tree.TreeView("setState", { nodes: {
				"0": true,
				"0.2": false,
				"0.2.1": false
			}});
			root.expanded = true;
			root.children[2].expanded = false;
			root.children[2].children[1].expanded = false;
			checkNode(tree.children(".root-node"), root);
			tree.TreeView("setState", {});
			root.children[2].expanded = true;
			root.children[2].children[1].expanded = true;
			checkNode(tree.children(".root-node"), root);
		});
	});	
});