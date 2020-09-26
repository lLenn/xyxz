const { By } = require("selenium-webdriver");
const { Widget, ComponentList, Component, Locator } = require("../../../../../helpers/functional");

class TreeView extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, "nixps-tree.TreeView", pOptions, pLocator);

		this.root = new TreeNode(pWebdriver, new Locator(By.css(".tree-node"), this.locator))
	}
}

class TreeNode extends Component {
	constructor(pWebdriver, pLocator) {
		super(pWebdriver, pLocator);

		this.content = new Component(pWebdriver, new Locator(By.css(".tree-node__content"), this.locator));
		this.children = new ComponentList(pWebdriver, TreeNode, new Locator(By.css(".tree-node__child-nodes > .tree-node__child-node > .tree-node"), this.locator));
	}
}


module.exports = TreeView;