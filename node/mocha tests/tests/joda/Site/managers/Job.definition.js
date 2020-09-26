const { ObjectDefinition, PropertyDefinition, types } = require("../../../../../helpers/object");

const JobDefinition = ObjectDefinition.createFrom([
	new PropertyDefinition("birth", ["date"]).chooseFrom(["__now"]),
	new PropertyDefinition("type", ["string"]).chooseFrom(["PrePressOrder"]),
	new PropertyDefinition("name", ["string"]).canNotBeEmpty().chooseFrom(["name1", "name2", "name2"]),
	new PropertyDefinition("state", ["string"]).chooseFrom(["Waiting for files", "check files"]),
	new PropertyDefinition("description", ["string"]).canNotBeEmpty().chooseFrom(["description1", "description2", "description3"]),
	new PropertyDefinition("project_id", ["uniqID"]),
	new PropertyDefinition("next_states", ["array"]).chooseFrom(function(pObject) {
		if(pObject.state === "Waiting for files") {
			return ["check files", "approval", "imposition"]
		} else {
			return undefined;
		}
	}),
	new PropertyDefinition("ui_handler", ["object"]).chooseFrom([{ whitepaper: "BE-CONTI Prepress Jobs Flow" }]),
	new PropertyDefinition("type", ["object"]).chooseFrom([{ ListEdit: { name: "Jobs_PrePressOrder_ListEdit" }}])
]);

module.exports = JobDefinition;