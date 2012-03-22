describe("issue 159", function () {
	beforeEach(function () {
		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			}
		});
	});

	afterEach(function () {
		$("textarea").wysiwyg("clear").wysiwyg("destroy");
	});

	it('should insert image and return content with correct image source', function () {
		$("#id1").wysiwyg("insertImage", "../images/quote02.gif");

		var content = $("#id1").val();

		expect(content.indexOf("../images/quote02.gif")).not.toEqual(-1);
	});
});
