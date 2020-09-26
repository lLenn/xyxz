var ProjectDataManager = require("../../../../../JODA/Site/js/managers/nixps-joda-ProjectDataManager.js");
var JobDefinition = require("./Job.definition.js");

describe("nixps-joda-ProjectDataManager.js", function(){
	describe("projectDataProvider", function() {
		it("should be faster with indices", function() {

		});
	});

	describe("getProjectsToChild", function() {
		it("should return an array from parent to child", function() {
			var testObject = new TestObject(new JobDefinition());

			var parent = testObject.createRandomObject();
			parent.projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];
			parent.projects[0].projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];
			parent.projects[0].projects[2].projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];

			parent.projects[1].projects = [testObject.createRandomObject(), testObject.createRandomObject()];
			parent.projects[1].projects[1].projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];
			parent.projects[1].projects[1].projects[0].projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];
			parent.projects[1].projects[1].projects[1].projects = [testObject.createRandomObject()];
			parent.projects[1].projects[1].projects[2].projects = [testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject(), testObject.createRandomObject()];

			var dataManager = new ProjectDataManager();

			expect(dataManager.getProjectsToChild(parent, parent.projects[1].projects[1].projects[2].projects[3].project_id)).to.deep.equal([ parent, parent.projects[1], parent.projects[1].projects[1], parent.projects[1].projects[1].projects[2], parent.projects[1].projects[1].projects[2].projects[3]]);
			expect(dataManager.getProjectsToChild(parent, parent.project_id)).to.deep.equal([ parent ]);
 		});
	});
});