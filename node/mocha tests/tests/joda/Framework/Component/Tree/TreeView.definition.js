const { ObjectDefinition, PropertyDefinition } = require("../../../../../helpers/unit");

TreeViewDefinition = ObjectDefinition.createFrom([
    new PropertyDefinition("indentation", ["number"]).chooseFrom([0, 100]),
    new PropertyDefinition("maxValue", ["linked"]).isLinkedWith(["minValue"]).withCallback(function(pOptions) {
        if(pOptions.minValue === 0) {
            return 100;
        } else {
            return 300;
        }
    }),
    new PropertyDefinition("value", ["linked"]).isNotRequired().isLinkedWith(["minValue"]).withCallback(function(pOptions) {
        return Math.floor(Math.random() * (pOptions.maxValue - pOptions.minValue)) + pOptions.minValue;
    }),
]);

RangeInputSetValueDefinition = ObjectDefinition.createFrom([
    new PropertyDefinition("0", ["parent"]).withCallback(function(pOptions, pParent) {
        return Math.floor(Math.random() * (pParent.maxValue - pParent.minValue)) + pParent.minValue;
    })
]);

//TODO: add getValue method


module.exports = { RangeInputDefinition, RangeInputSetValueDefinition };