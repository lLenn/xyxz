describe("nixps.io.File", function() {
	describe("splitPath", function() {
		it("should return an array with the parts of the path", function() {
			expect(nixps.io.File.splitPath("cloudflow://some/path/with/a/file.text")).to.deep.equal(["cloudflow:", "some", "path", "with", "a", "file.text"]);
			expect(nixps.io.File.splitPath("some/path/with/a/file.text")).to.deep.equal(["some", "path", "with", "a", "file.text"]);
		});
	});
	
	describe("joinDirectories", function() {
		it("should return a string created with the directories given", function() {
			expect(nixps.io.File.joinDirectories(["cloudflow:", "some", "path", "with", "a", "file.text"])).to.equal("cloudflow://some/path/with/a/file.text");
			expect(nixps.io.File.joinDirectories(["some", "path", "with", "a", "file.text"])).to.equal("some/path/with/a/file.text");
			expect(nixps.io.File.joinDirectories(["cloudflow:", "some", "path", "with", "a", "file/"])).to.equal("cloudflow://some/path/with/a/file/");
			expect(nixps.io.File.joinDirectories(["some", "path", "with", "a"])).to.equal("some/path/with/a/");
			expect(nixps.io.File.joinDirectories(["cloudflow:", "", "path", "with", "a", "file/"])).to.equal("cloudflow://path/with/a/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow:", "", "path", "with", "", "file/"])).to.equal("cloudflow://path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow://some/", "", "path", "with", "", "file/"])).to.equal("cloudflow://some/path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow://some", "", "path", "with", "", "file/"])).to.equal("cloudflow://some/path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow:/", "", "path", "with", "", "file/"])).to.equal("cloudflow://path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow://", "", "path", "with", "", "file/"])).to.equal("cloudflow://path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow:/", "some", "path", "with", "", "file/"])).to.equal("cloudflow://some/path/with/file/");
			expect(nixps.io.File.joinDirectories(["cloudflow://", "some", "path", "with", "", "file/"])).to.equal("cloudflow://some/path/with/file/");
			expect(nixps.io.File.joinDirectories(["some", "path", "with", ""])).to.equal("some/path/with/");
			expect(nixps.io.File.joinDirectories(["some", "", "with", "a"])).to.equal("some/with/a/");
			expect(nixps.io.File.joinDirectories(["", "path", "with", "a"])).to.equal("path/with/a/");
		});
	});
	
	describe("getDir", function() {
		it("should return the active directory", function() {
			expect(nixps.io.File.getDir("cloudflow://some/path/with/a/file.text")).to.equal("cloudflow://some/path/with/a/");
			expect(nixps.io.File.getDir("some/path/with/a/file.text")).to.equal("some/path/with/a/");
			expect(nixps.io.File.getDir("some/path/with/a/")).to.equal("some/path/with/");
			expect(nixps.io.File.getDir("some/path/with/a")).to.equal("some/path/with/");
		});
	});

	describe("isValidDirectoryName", function() {
		it("should return true or false depending on the validity of the name", function() {
			expect(nixps.io.File.isValidDirectoryName("cloudflow://some/path/with/a/file.text")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName(".")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName(" ")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName(" .")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("name ")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("name.")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("n/ame")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("con")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("com?")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("com7")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("somename.hey")).to.be.false;
			expect(nixps.io.File.isValidDirectoryName("somename")).to.be.true;
			expect(nixps.io.File.isValidDirectoryName("s5646omename")).to.be.true;
			expect(nixps.io.File.isValidDirectoryName("s5646om§§§¨¨fµ``µ```£££££@&ename")).to.be.true;
		});
	});
	
	describe("getFileName", function() {
		it("should return the file name of a path", function() {
			expect(nixps.io.File.getFileName("cloudflow://some/path/with/a/file.text", true)).to.equal("file.text");
			expect(nixps.io.File.getFileName("cloudflow://some/path/with/a/file.text")).to.equal("file");
			expect(nixps.io.File.getFileName("some/path/with/a/file.text", true)).to.equal("file.text");
			expect(nixps.io.File.getFileName("some/path/with/a/file.text")).to.equal("file");
			expect(nixps.io.File.getFileName("some/path/with/a/file.ext.text", true)).to.equal("file.ext.text");
			expect(nixps.io.File.getFileName("some/path/with/a/file.ext.text")).to.equal("file.ext");
			expect(nixps.io.File.getFileName("some/path/with/a/file", true)).to.equal("file");
			expect(nixps.io.File.getFileName("some/path/with/a/file")).to.equal("file");
			expect(nixps.io.File.getFileName("some/path/with/a/.", true)).to.equal(".");
			expect(nixps.io.File.getFileName("some/path/with/a/.")).to.equal("");
			expect(nixps.io.File.getFileName("some/path/with/a/.html", true)).to.equal(".html");
			expect(nixps.io.File.getFileName("some/path/with/a/.html")).to.equal("");
		});
	});
	
	describe("changeFileName", function() {
		it("should return the path with the new filename", function() {
			expect(nixps.io.File.changeFileName("cloudflow://some/path/with/a/file.text", "new_file")).to.equal("cloudflow://some/path/with/a/new_file.text");
			expect(nixps.io.File.changeFileName("some/path/with/a/file.text", "new_file")).to.equal("some/path/with/a/new_file.text");
			expect(nixps.io.File.changeFileName("some/path/with/a/file.ext.text", "new_file")).to.equal("some/path/with/a/new_file.text");
			expect(nixps.io.File.changeFileName("some/path/with/a/file", "new_file")).to.equal("some/path/with/a/new_file");
			expect(nixps.io.File.changeFileName("some/path/with/a/.", "new_file")).to.equal("some/path/with/a/new_file.");
			expect(nixps.io.File.changeFileName("some/path/with/a/.html", "new_file")).to.equal("some/path/with/a/new_file.html");
			expect(nixps.io.File.changeFileName("some", "new_file")).to.equal("new_file");
			expect(nixps.io.File.changeFileName("some.html", "new_file")).to.equal("new_file.html");
			expect(nixps.io.File.changeFileName(".html", "new_file")).to.equal("new_file.html");
			expect(nixps.io.File.changeFileName(".", "new_file")).to.equal("new_file.");
		});
	});
	
	describe("getRelativePath", function() {
		it("should return the path relative to a folder within the folders array", function() {
			expect(nixps.io.File.getRelativePath(["cloudflow://some/other/path/", "cloudflow://some/path/"], "cloudflow://some/path/with/a/file.text")).to.equal("with/a/file.text");
			expect(nixps.io.File.getRelativePath(["cloudflow://some/other/path/", "cloudflow://some/damn/path/"], "cloudflow://some/path/with/a/file.text")).to.equal("cloudflow://some/path/with/a/file.text");
			expect(nixps.io.File.getRelativePath([], "cloudflow://some/path/with/a/file.text")).to.equal("cloudflow://some/path/with/a/file.text");
			expect(nixps.io.File.getRelativePath(undefined, "cloudflow://some/path/with/a/file.text")).to.equal("cloudflow://some/path/with/a/file.text");
		});
	});
});