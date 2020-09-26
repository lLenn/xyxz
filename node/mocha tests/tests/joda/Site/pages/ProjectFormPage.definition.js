const { ObjectDefinition, PropertyDefinition, types } = require("../../../../helpers/object");
const { ParentType } = types;

ProjectFormPageDefinition = ObjectDefinition.createFrom([
	new PropertyDefinition("projectType", ["string"]).chooseFrom(["PrePressOrder"]),
	new PropertyDefinition("listRowCount", ["number"]).chooseFrom([10, 30]),
	new PropertyDefinition("listRowHeight", [new ParentType(function(pObject, pParents, pMetaData) {
		if(pObject.projectType === "PrePressOrder") {
			return 300;
		} else {
			return 10;
		}
	}, function(pPreviousObject, pCurrentObject, pParents) {
		return (pPreviousObject.projectType !== pCurrentObject.projectType);
	})]),
	new PropertyDefinition("listImageSize", ["number"]).chooseFrom([50, 100]),
	new PropertyDefinition("listQueryColleciton", [new ParentType(function(pObject, pParents, pMetaData) {
		if(pObject.projectType === "PrePressOrder") {
			return [
			    { id: "search.extra_fields.name", label: "Name", type: "string" },
			    { id: "search.extra_fields.state", label: "State", type: "string" },
			    { id: "search.extra_fields.description", label: "Description", type: "string" },
			    { id: "search.extra_fields.customxmlinputsr_parametersProductId", label: "Product ID", type: "string" },
			    { id: "search.extra_fields.customxmlinputsr_parameters.OrganizationName", label: "Client Name", type: "string" },
			    { id: "search.extra_fields.customxmlinputSheetSettingsPageWidth", label: "Page Width", type: "integer" },
			    { id: "search.extra_fields.customxmlinputSheetSettingsPageHeight", label: "Page Height", type: "integer" }
			]
		} else {
			return [
				{ id: "state", label: $.i18n._("nixps-cloudflow-ProjectFormList.state"), type: "string" },
				{ id: "name", label: $.i18n._("nixps-cloudflow-ProjectFormList.name"), type: "string" },
				{ id: "description", label: $.i18n._("nixps-cloudflow-ProjectFormList.description"), type: "string" }
			]
		}
	}, function(pPreviousObject, pCurrentObject, pParents) {
		return (pPreviousObject.projectType !== pCurrentObject.projectType);
	})]),
	new PropertyDefinition("propertiesWhitepaper", ["string"]).chooseFrom(["HY-JODA Jobs Files"]),
	new PropertyDefinition("propertiesInput", ["string"]).chooseFrom(["File"]),
	new PropertyDefinition("canCreate", ["boolean"]).chooseFrom([false]),
	new PropertyDefinition("createWhitepaper", ["string"]).chooseFrom(["TMPL-JOBS-Labels"]),
	new PropertyDefinition("createInput", ["string"]).chooseFrom(["Input Name"]),
])

module.exports = ProjectFormPageDefinition;