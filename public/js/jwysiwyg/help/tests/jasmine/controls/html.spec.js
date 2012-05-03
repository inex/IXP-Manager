describe("controls.html", function () {
	beforeEach(function () {
		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			}
		}).wysiwyg("clear");
	});

	afterEach(function () {
		$("textarea").wysiwyg("destroy");
	});

	it('should save text into editor iframe when it was typed in source', function () {
		$("#id1").wysiwyg("triggerControl", "html");

		$("#id1").val("insert text in textarea when html is triggered on");

		$("#id1").wysiwyg("triggerControl", "html");

		expect($("#id1").wysiwyg("getContent")).toEqual($("#id1").val());
	});
});
