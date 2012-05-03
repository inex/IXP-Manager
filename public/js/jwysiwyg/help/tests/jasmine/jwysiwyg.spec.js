describe("jWYSIWYG", function () {
	beforeEach(function () {
		$("textarea").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			}
		});
	});

	afterEach(function () {
		$("textarea").wysiwyg("destroy");
	});

	it('should get correct args in both case', function () {
		spyOn($.wysiwyg, "insertHtml");

		// 1st case
		$("#id1").wysiwyg("insertHtml", "<p>111</p>");
		// 2nd case
		$.wysiwyg.insertHtml($("#id2"), "<p>222</p>");

		expect($.wysiwyg.insertHtml).toHaveBeenCalledWith($("#id1"), "<p>111</p>");
		expect($.wysiwyg.insertHtml).toHaveBeenCalledWith($("#id2"), "<p>222</p>");
	});
});
